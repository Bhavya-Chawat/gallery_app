<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImageResource;
use App\Jobs\ProcessImageJob;
use App\Models\Album;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('throttle:uploads,10')->only(['presign', 'complete']);
    }

    /**
     * Generate presigned URLs for direct upload to S3/MinIO.
     */
    public function presign(Request $request)
    {
        $request->validate([
            'files' => 'required|array|min:1|max:20',
            'files.*.name' => 'required|string|max:255',
            // client-reported size in bytes; compare against configured byte limit
            'files.*.size' => 'required|integer|min:1|max:' . (int) config('filesystems.gallery.max_upload_size', 52428800),
            'files.*.type' => 'required|string|in:image/jpeg,image/png,image/webp,image/avif',
            'album_id' => 'nullable|exists:albums,id',
            'privacy' => 'nullable|in:public,unlisted,private',
        ]);

        $user = auth()->user();

        // Check storage quota
        $totalSize = collect($request->files)->sum('size');
        if (!$user->canUpload($totalSize)) {
            return response()->json([
                'message' => 'Storage quota exceeded',
                'error' => 'insufficient_storage',
                'available_bytes' => $user->getRemainingStorageBytes(),
                'required_bytes' => $totalSize,
            ], 413);
        }

        // Verify album ownership
        $album = null;
        if ($request->album_id) {
            $album = Album::find($request->album_id);
            if (!$album || ($album->owner_id !== $user->id && !$user->hasRole('admin'))) {
                return response()->json([
                    'message' => 'Album not found or access denied',
                ], 403);
            }
        }

        $uploadSessionId = Str::uuid();
        $uploads = [];

        foreach ($request->files as $file) {
            $fileId = Str::uuid();
            $extension = $this->getExtensionFromMimeType($file['type']);
            $storageKey = $this->generateStorageKey($fileId, $extension);

            // Generate presigned URL
            $presignedUrl = Storage::disk('s3')->temporaryUrl(
                $storageKey,
                now()->addMinutes(15),
                [
                    'ResponseContentType' => $file['type'],
                    'ResponseContentDisposition' => 'attachment; filename="' . $file['name'] . '"',
                ]
            );

            $uploads[] = [
                'file_id' => $fileId,
                'storage_key' => $storageKey,
                'presigned_url' => $presignedUrl,
                'original_name' => $file['name'],
                'mime_type' => $file['type'],
                'size' => $file['size'],
            ];
        }

        // Store upload session in cache for verification
        cache()->put("upload_session:{$uploadSessionId}", [
            'user_id' => $user->id,
            'album_id' => $request->album_id,
            'privacy' => $request->privacy ?? config('filesystems.gallery.default_privacy', 'unlisted'),
            'files' => collect($uploads)->keyBy('file_id')->toArray(),
            'expires_at' => now()->addMinutes(20),
        ], now()->addMinutes(20));

        return response()->json([
            'upload_session_id' => $uploadSessionId,
            'uploads' => $uploads,
            'expires_at' => now()->addMinutes(15),
        ]);
    }

    /**
     * Complete upload and create image records.
     */
    public function complete(Request $request)
    {
        $request->validate([
            'upload_session_id' => 'required|uuid',
            'files' => 'required|array|min:1',
            'files.*.file_id' => 'required|uuid',
            'files.*.checksum' => 'nullable|string',
            'common_metadata' => 'nullable|array',
            'common_metadata.title_prefix' => 'nullable|string|max:100',
            'common_metadata.caption' => 'nullable|string|max:500',
            'common_metadata.tags' => 'nullable|array',
            'common_metadata.license' => 'nullable|string|max:100',
        ]);

        $sessionKey = "upload_session:{$request->upload_session_id}";
        $session = cache()->get($sessionKey);

        if (!$session) {
            return response()->json([
                'message' => 'Upload session expired or invalid',
                'error' => 'invalid_session',
            ], 422);
        }

        if ($session['user_id'] !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized upload session',
                'error' => 'unauthorized_session',
            ], 403);
        }

        $createdImages = [];
        $errors = [];

        foreach ($request->files as $fileData) {
            $fileId = $fileData['file_id'];
            
            if (!isset($session['files'][$fileId])) {
                $errors[] = ['file_id' => $fileId, 'error' => 'File not found in session'];
                continue;
            }

            $sessionFile = $session['files'][$fileId];
            
            try {
                // Verify file exists in storage
                if (!Storage::disk('s3')->exists($sessionFile['storage_key'])) {
                    $errors[] = ['file_id' => $fileId, 'error' => 'File not found in storage'];
                    continue;
                }

                // Get actual file size from storage
                $actualSize = Storage::disk('s3')->size($sessionFile['storage_key']);
                
                // Create image record
                $image = Image::create([
                    'id' => $fileId,
                    'owner_id' => $session['user_id'],
                    'album_id' => $session['album_id'],
                    'title' => $this->generateTitle($sessionFile['original_name'], $request->common_metadata['title_prefix'] ?? null),
                    'caption' => $request->common_metadata['caption'] ?? null,
                    'alt_text' => $this->generateAltText($sessionFile['original_name']),
                    'original_filename' => $sessionFile['original_name'],
                    'storage_path' => $sessionFile['storage_key'],
                    'mime_type' => $sessionFile['mime_type'],
                    'size_bytes' => $actualSize,
                    'width' => 0, // Will be updated after processing
                    'height' => 0, // Will be updated after processing
                    'privacy' => $session['privacy'],
                    'license' => $request->common_metadata['license'] ?? null,
                    'is_published' => $session['privacy'] !== 'private',
                    'published_at' => $session['privacy'] !== 'private' ? now() : null,
                    'processing_status' => [
                        'thumbnails_generated' => false,
                        'metadata_extracted' => false,
                    ],
                ]);

                // Attach tags if provided
                if (!empty($request->common_metadata['tags'])) {
                    $tags = collect($request->common_metadata['tags'])->map(function ($tagName) {
                        return \App\Models\Tag::firstOrCreate(['name' => trim($tagName)]);
                    });
                    $image->tags()->attach($tags->pluck('id'));
                }

                // Update user storage usage
                auth()->user()->incrementStorageUsage($actualSize);

                // Queue processing job
              //  ProcessImageJob::dispatch($image);

                $createdImages[] = [
                    'image_id' => $image->id,
                    'status' => 'created',
                    'processing_queued' => true,
                ];

            } catch (\Exception $e) {
                $errors[] = [
                    'file_id' => $fileId,
                    'error' => 'Failed to create image record: ' . $e->getMessage()
                ];
            }
        }

        // Update album image count if needed
        if ($session['album_id'] && !empty($createdImages)) {
            Album::find($session['album_id'])?->updateImageCount();
        }

        // Clear upload session
        cache()->forget($sessionKey);

        return response()->json([
            'message' => count($createdImages) . ' images uploaded successfully',
            'uploaded' => $createdImages,
            'errors' => $errors,
            'processing' => 'Images are being processed in the background',
        ], !empty($errors) ? 207 : 201); // 207 Multi-Status if some failed
    }

    /**
     * Get upload progress/status.
     */
    public function status(Request $request, string $uploadSessionId)
    {
        $session = cache()->get("upload_session:{$uploadSessionId}");

        if (!$session || $session['user_id'] !== auth()->id()) {
            return response()->json(['message' => 'Upload session not found'], 404);
        }

        return response()->json([
            'session' => [
                'id' => $uploadSessionId,
                'expires_at' => $session['expires_at'],
                'files_count' => count($session['files']),
                'album_id' => $session['album_id'],
                'privacy' => $session['privacy'],
            ],
        ]);
    }

    /**
     * Direct upload (alternative to presign flow).
     */
    public function direct(Request $request)
    {
        $maxBytes = (int) config('filesystems.gallery.max_upload_size', 52428800);
        $maxKilobytes = (int) ceil($maxBytes / 1024);

        $request->validate([
            'files' => 'required|array|min:1|max:5',
            // Laravel's max for file size expects kilobytes
            'files.*' => 'required|image|max:' . $maxKilobytes,
            'album_id' => 'nullable|exists:albums,id',
            'privacy' => 'nullable|in:public,unlisted,private',
            'title_prefix' => 'nullable|string|max:100',
            'caption' => 'nullable|string|max:500',
            'tags' => 'nullable|array',
            'license' => 'nullable|string|max:100',
        ]);

        $user = auth()->user();
        $createdImages = [];
        $errors = [];

        $disk = config('filesystems.default', 'local');
        $disk = 'minio';
        foreach ($request->file('files') as $file) {
            try {
                // Check storage quota
                if (!$user->canUpload($file->getSize())) {
                    $errors[] = [
                        'filename' => $file->getClientOriginalName(),
                        'error' => 'insufficient_storage',
                        'message' => 'Insufficient storage quota for this file',
                        'size' => $file->getSize(),
                        'remaining_bytes' => $user->getRemainingStorageBytes(),
                    ];
                    continue;
                }

                $fileId = Str::uuid();
                $extension = $file->getClientOriginalExtension();
                $storageKey = $this->generateStorageKey($fileId, $extension);

                // Upload file
                $path = Storage::disk($disk)->putFileAs(
                    dirname($storageKey),
                    $file,
                    basename($storageKey)
                );

                if (!$path) {
                    throw new \RuntimeException('putFileAs returned empty path');
                }

                // Create image record
                $image = Image::create([
                    'id' => $fileId,
                    'owner_id' => $user->id,
                    'album_id' => $request->album_id,
                    'title' => $this->generateTitle($file->getClientOriginalName(), $request->title_prefix),
                    'caption' => $request->caption,
                    'alt_text' => $this->generateAltText($file->getClientOriginalName()),
                    'original_filename' => $file->getClientOriginalName(),
                    'storage_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size_bytes' => $file->getSize(),
                    'width' => 0,
                    'height' => 0,
                    'privacy' => $request->privacy ?? config('filesystems.gallery.default_privacy', 'unlisted'),
                    'license' => $request->license,
                    'is_published' => ($request->privacy ?? 'unlisted') !== 'private',
                    'published_at' => ($request->privacy ?? 'unlisted') !== 'private' ? now() : null,
                    'aspect_ratio' => 0,
                    'processing_status' => [
                        'thumbnails_generated' => false,
                        'metadata_extracted' => false,
                    ],
                ]);

                // Update user storage usage
                $user->incrementStorageUsage($file->getSize());

                // Queue processing
                ProcessImageJob::dispatch($image);

               $createdImages[] = [
    'id' => $image->id,
    'title' => $image->title,
    'original_filename' => $image->original_filename,
    'storage_path' => $image->storage_path,
    'size_bytes' => $image->size_bytes,
    'mime_type' => $image->mime_type,
    'created_at' => $image->created_at,
];


            } catch (\Throwable $t) {
                Log::error('Direct upload failed', [
                    'user_id' => $user->id,
                    'disk' => $disk,
                    'album_id' => $request->album_id,
                    'filename' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'storage_key' => isset($storageKey) ? $storageKey : null,
                    'exception' => get_class($t),
                    'message' => $t->getMessage(),
                    'trace' => $t->getTraceAsString(),
                ]);

                $errors[] = [
                    'filename' => $file->getClientOriginalName(),
                    'error' => 'upload_failed',
                    'message' => $t->getMessage(),
                ];
                continue;
            }
        }

        $status = empty($errors) ? 201 : (empty($createdImages) ? 422 : 207);

        return response()->json([
            'message' => count($createdImages) . ' images uploaded successfully',
            'images' => $createdImages,
            'errors' => $errors,
            'disk' => $disk,
        ], $status);
    }

    // Helper methods
    private function getExtensionFromMimeType(string $mimeType): string
    {
        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/avif' => 'avif',
            default => 'jpg',
        };
    }

    private function generateStorageKey(string $fileId, string $extension): string
    {
        $year = now()->year;
        $month = now()->format('m');
        return "images/{$year}/{$month}/{$fileId}/original.{$extension}";
    }

    private function generateTitle(?string $filename, ?string $prefix = null): string
    {
        $title = $filename ? pathinfo($filename, PATHINFO_FILENAME) : 'Untitled';
        $title = str_replace(['_', '-'], ' ', $title);
        $title = ucwords($title);
        
        return $prefix ? "{$prefix} {$title}" : $title;
    }

    private function generateAltText(string $filename): string
    {
        return $this->generateTitle($filename);
    }
}
