<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Image;
use App\Models\Tag;
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
            'files.*.size' => 'required|integer|min:1|max:' . (int) config('filesystems.gallery.max_upload_size', 52428800),
            'files.*.type' => 'required|string|in:image/jpeg,image/png,image/webp,image/avif',
            'album_id' => 'nullable|exists:albums,id',
            'privacy' => 'nullable|in:public,unlisted,private',
        ]);

        $user = auth()->user();

        // Check storage quota
        $totalSize = collect($request->files)->sum('size');
        $remainingBytes = $this->getUserRemainingStorage($user);
        
        if ($totalSize > $remainingBytes) {
            return response()->json([
                'message' => 'Storage quota exceeded',
                'error' => 'insufficient_storage',
                'available_bytes' => $remainingBytes,
                'required_bytes' => $totalSize,
            ], 413);
        }

        // Verify album ownership
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
            try {
                $presignedUrl = Storage::disk('minio')->temporaryUrl(
                    $storageKey,
                    now()->addMinutes(15)
                );

                $uploads[] = [
                    'file_id' => $fileId,
                    'storage_key' => $storageKey,
                    'presigned_url' => $presignedUrl,
                    'original_name' => $file['name'],
                    'mime_type' => $file['type'],
                    'size' => $file['size'],
                ];
            } catch (\Exception $e) {
                Log::error('Failed to generate presigned URL', [
                    'storage_key' => $storageKey,
                    'error' => $e->getMessage()
                ]);
                
                return response()->json([
                    'message' => 'Failed to generate upload URL',
                    'error' => 'presign_failed'
                ], 500);
            }
        }

        // Store upload session in cache for verification
        cache()->put("upload_session:{$uploadSessionId}", [
            'user_id' => $user->id,
            'album_id' => $request->album_id,
            'privacy' => $request->privacy ?? 'unlisted',
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
                if (!Storage::disk('minio')->exists($sessionFile['storage_key'])) {
                    $errors[] = ['file_id' => $fileId, 'error' => 'File not found in storage'];
                    continue;
                }

                // Get actual file size from storage
                $actualSize = Storage::disk('minio')->size($sessionFile['storage_key']);
                
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
                    'width' => 0,
                    'height' => 0,
                    'privacy' => $session['privacy'],
                    'license' => $request->common_metadata['license'] ?? null,
                    'is_published' => $session['privacy'] !== 'private',
                    'published_at' => $session['privacy'] !== 'private' ? now() : null,
                    'slug' => $this->generateSlug($sessionFile['original_name']),
                ]);

                // Attach tags if provided
                if (!empty($request->common_metadata['tags'])) {
                    $this->attachTags($image, $request->common_metadata['tags']);
                }

                // Update user storage usage
                $this->incrementUserStorage(auth()->user(), $actualSize);

                $createdImages[] = [
                    'id' => $image->id,
                    'title' => $image->title,
                    'slug' => $image->slug,
                    'storage_path' => $image->storage_path,
                    'status' => 'created',
                ];

            } catch (\Exception $e) {
                Log::error('Failed to create image record', [
                    'file_id' => $fileId,
                    'error' => $e->getMessage()
                ]);
                
                $errors[] = [
                    'file_id' => $fileId,
                    'error' => 'Failed to create image record: ' . $e->getMessage()
                ];
            }
        }

        // Update album image count if needed
        if ($session['album_id'] && !empty($createdImages)) {
            $this->updateAlbumCount($session['album_id']);
        }

        // Clear upload session
        cache()->forget($sessionKey);

        return response()->json([
            'message' => count($createdImages) . ' images uploaded successfully',
            'images' => $createdImages,
            'errors' => $errors,
        ], !empty($errors) ? 207 : 201);
    }
/**
 * Direct upload (MAIN METHOD FOR SIMPLE UPLOAD).
 */
public function direct(Request $request)
{
    $maxBytes = (int) config('filesystems.gallery.max_upload_size', 52428800);
    $maxKilobytes = (int) ceil($maxBytes / 1024);

    $request->validate([
        'files' => 'required|array|min:1|max:10',
        'files.*' => 'required|image|max:' . $maxKilobytes,
        'metadata' => 'nullable|array',
        'metadata.*.title' => 'nullable|string|max:255',
        'metadata.*.alt_text' => 'nullable|string|max:255',
        'metadata.*.caption' => 'nullable|string|max:1000',
        'metadata.*.tags' => 'nullable|string|max:500',
        'album_id' => 'nullable|exists:albums,id',
        'privacy' => 'required|in:public,unlisted,private',
    ]);

    $user = auth()->user();
    $createdImages = [];
    $errors = [];

    foreach ($request->file('files') as $index => $file) {
        try {
            // Check storage quota
            if (!$user->canUpload($file->getSize())) {
                $errors[] = [
                    'filename' => $file->getClientOriginalName(),
                    'error' => 'insufficient_storage',
                    'message' => 'Insufficient storage quota',
                    'size' => $file->getSize(),
                    'remaining_bytes' => $user->getRemainingStorageBytes(),
                ];
                continue;
            }

            // Generate unique filename and path
            $fileId = Str::uuid();
            $extension = $file->getClientOriginalExtension();
            $filename = $fileId . '.' . $extension;

            // Upload file to MinIO
            $path = Storage::disk('minio')->putFileAs('', $file, $filename);

            if (!$path) {
                throw new \RuntimeException('File upload to storage failed');
            }

            // EXTRACT EXIF DATA AND DIMENSIONS
            $exifData = $this->extractExifData($file);
            $dimensions = $this->getImageDimensions($file);

            // Get metadata for this file - SAFE ACCESS WITH NULL COALESCING
            $metadata = $request->input("metadata.{$index}", []);

            // Create image record with ALL REQUIRED FIELDS INCLUDING EXIF
            $image = Image::create([
                'owner_id' => $user->id,
                'album_id' => $request->album_id,
                'title' => $metadata['title'] ?? $this->generateTitle($file->getClientOriginalName()),
                'caption' => $metadata['caption'] ?? null,
                'alt_text' => $metadata['alt_text'] ?? $this->generateAltText($file->getClientOriginalName()),
                'original_filename' => $file->getClientOriginalName(),
                'storage_path' => $path,
                'mime_type' => $file->getMimeType(),
                'size_bytes' => $file->getSize(),
                'width' => $dimensions['width'],        // ✅ EXTRACTED
                'height' => $dimensions['height'],      // ✅ EXTRACTED
                'aspect_ratio' => $dimensions['aspect_ratio'], // ✅ CALCULATED
                'privacy' => $request->privacy,
                'is_published' => $request->privacy !== 'private',
                'published_at' => $request->privacy !== 'private' ? now() : null,
                'views_count' => 0,
                'likes_count' => 0,
                'comments_count' => 0,
                'allow_comments' => true,
                'allow_downloads' => true,
                'processing_status' => [
                    'thumbnails_generated' => false,
                    'metadata_extracted' => true, // ✅ NOW TRUE
                ],
                // ✅ ADD EXIF DATA
                'exif_data' => $exifData['exif'],
                'taken_at' => $exifData['taken_at'],
                'camera_make' => $exifData['camera_make'],
                'camera_model' => $exifData['camera_model'],
                'license' => $exifData['license'],
            ]);

            // Handle tags
            if (!empty($metadata['tags'])) {
                $this->attachTags($image, $metadata['tags']);
            }

            // Update user storage usage
            $user->incrementStorageUsage($file->getSize());

            $createdImages[] = [
                'id' => $image->id,
                'title' => $image->title,
                'slug' => $image->slug,
                'original_filename' => $image->original_filename,
                'storage_path' => $image->storage_path,
                'size_bytes' => $image->size_bytes,
                'mime_type' => $image->mime_type,
                'width' => $image->width,        // ✅ RETURN DIMENSIONS
                'height' => $image->height,      // ✅ RETURN DIMENSIONS
                'camera_make' => $image->camera_make,    // ✅ RETURN CAMERA
                'camera_model' => $image->camera_model,  // ✅ RETURN CAMERA
                'taken_at' => $image->taken_at,          // ✅ RETURN DATE
                'created_at' => $image->created_at,
            ];

        } catch (\Throwable $e) {
            Log::error('Direct upload failed', [
                'user_id' => $user->id,
                'filename' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $errors[] = [
                'filename' => $file->getClientOriginalName(),
                'error' => 'upload_failed',
                'message' => $e->getMessage(),
            ];
            continue;
        }
    }

    // Update album image count if needed
    if ($request->album_id && !empty($createdImages)) {
        $this->updateAlbumCount($request->album_id);
    }

    $status = empty($errors) ? 201 : (empty($createdImages) ? 422 : 207);

    return response()->json([
        'success' => true,
        'message' => count($createdImages) . ' images uploaded successfully',
        'images' => $createdImages,
        'errors' => $errors,
    ], $status);
}

// ✅ ADD THESE NEW METHODS TO YOUR UPLOAD CONTROLLER:

/**
 * Extract EXIF data from uploaded file
 */
private function extractExifData($file): array
{
    $exifData = [
        'exif' => null,
        'taken_at' => null,
        'camera_make' => null,
        'camera_model' => null,
        'license' => null,
    ];

    try {
        // Read EXIF data from the uploaded file
        $filePath = $file->getRealPath();
        
        if (function_exists('exif_read_data') && is_file($filePath)) {
            $exif = @exif_read_data($filePath);
            
            if ($exif !== false && is_array($exif)) {
                // Store full EXIF data (but remove GPS for privacy)
                $safeExif = $exif;
                unset($safeExif['GPSLatitude'], $safeExif['GPSLongitude'], 
                      $safeExif['GPSLatitudeRef'], $safeExif['GPSLongitudeRef']);
                
                $exifData['exif'] = $safeExif;
                
                // Extract specific fields
                $exifData['camera_make'] = $exif['Make'] ?? null;
                $exifData['camera_model'] = $exif['Model'] ?? null;
                
                // Extract date taken
                if (isset($exif['DateTime'])) {
                    try {
                        $exifData['taken_at'] = \Carbon\Carbon::createFromFormat('Y:m:d H:i:s', $exif['DateTime']);
                    } catch (\Exception $e) {
                        // Ignore date parsing errors
                    }
                } elseif (isset($exif['DateTimeOriginal'])) {
                    try {
                        $exifData['taken_at'] = \Carbon\Carbon::createFromFormat('Y:m:d H:i:s', $exif['DateTimeOriginal']);
                    } catch (\Exception $e) {
                        // Ignore date parsing errors
                    }
                }
                
                // Extract copyright/license if present
                $exifData['license'] = $exif['Copyright'] ?? null;
            }
        }
    } catch (\Exception $e) {
        Log::warning('Failed to extract EXIF data', [
            'file' => $file->getClientOriginalName(),
            'error' => $e->getMessage()
        ]);
    }

    return $exifData;
}

/**
 * Get image dimensions
 */
private function getImageDimensions($file): array
{
    $dimensions = [
        'width' => 0,
        'height' => 0,
        'aspect_ratio' => 0,
    ];

    try {
        $filePath = $file->getRealPath();
        
        if (is_file($filePath)) {
            $imageSize = @getimagesize($filePath);
            
            if ($imageSize !== false && is_array($imageSize)) {
                $dimensions['width'] = $imageSize[0] ?? 0;
                $dimensions['height'] = $imageSize[1] ?? 0;
                
                if ($dimensions['width'] > 0 && $dimensions['height'] > 0) {
                    $dimensions['aspect_ratio'] = round($dimensions['width'] / $dimensions['height'], 4);
                }
            }
        }
    } catch (\Exception $e) {
        Log::warning('Failed to get image dimensions', [
            'file' => $file->getClientOriginalName(),
            'error' => $e->getMessage()
        ]);
    }

    return $dimensions;
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
        if (!$filename) return 'Untitled';
        
        $title = pathinfo($filename, PATHINFO_FILENAME);
        $title = str_replace(['_', '-'], ' ', $title);
        $title = ucwords($title);
        
        return $prefix ? "{$prefix} {$title}" : $title;
    }

    private function generateAltText(string $filename): string
    {
        return $this->generateTitle($filename);
    }

    private function generateSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        // Ensure unique slug
        while (Image::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function attachTags($image, $tagsInput)
    {
        $tagNames = is_string($tagsInput) 
            ? array_filter(array_map('trim', explode(',', $tagsInput)))
            : (is_array($tagsInput) ? $tagsInput : []);

        $tagIds = [];
        foreach ($tagNames as $tagName) {
            if (!empty(trim($tagName))) {
                $tag = Tag::firstOrCreate(['name' => trim($tagName)]);
                $tagIds[] = $tag->id;
            }
        }

        if (!empty($tagIds)) {
            $image->tags()->sync($tagIds);
            
            // Update tag usage counts
            Tag::whereIn('id', $tagIds)->each(function ($tag) {
                $tag->increment('usage_count');
            });
        }
    }

    private function getUserRemainingStorage($user): int
    {
        $quotaBytes = $user->storage_quota_bytes ?? 0;
        $usedBytes = $user->storage_used_bytes ?? 0;
        return max(0, $quotaBytes - $usedBytes);
    }

    private function incrementUserStorage($user, int $bytes): void
    {
        $user->increment('storage_used_bytes', $bytes);
    }

    private function updateAlbumCount($albumId): void
    {
        try {
            $album = Album::find($albumId);
            if ($album) {
                $album->images_count = $album->images()->count();
                $album->save();
            }
        } catch (\Exception $e) {
            Log::warning('Failed to update album count', [
                'album_id' => $albumId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get upload status/progress.
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
}
