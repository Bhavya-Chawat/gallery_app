<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Image;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ImageController extends Controller
{
    /**
     * Display a listing of images.
     */
    public function index(Request $request)
    {
        $query = Image::with(['owner', 'album']);
        
        // Check if this is "My Images" request
        $isMyImages = $request->get('owner') === 'mine' || $request->get('show_all');
        $currentUser = auth()->user();
        
        // Apply visibility scope based on context
        if ($isMyImages && $currentUser) {
            // For "My Images" - show ALL user's images (including private/unpublished)
            $query->where('owner_id', $currentUser->id);
        } else {
            // For public gallery - ONLY show published public/unlisted images
            $query->whereIn('privacy', ['public', 'unlisted'])
                  ->where('is_published', true);
        }
        
        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('caption', 'like', '%' . $request->search . '%')
                  ->orWhere('alt_text', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by album
        if ($request->filled('album')) {
            $query->where('album_id', $request->album);
        }
        
        // Filter by privacy (only for "My Images")
        if ($isMyImages && $request->filled('privacy')) {
            $query->where('privacy', $request->privacy);
        }
        
        // Filter by published status (only for "My Images")  
        if ($isMyImages && $request->filled('published')) {
            $query->where('is_published', $request->published === 'published');
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        
        switch ($sort) {
            case 'title':
                $query->orderBy('title', $direction);
                break;
            case 'views':
                $query->orderBy('views_count', $direction);
                break;
            case 'size':
                $query->orderBy('file_size', $direction);
                break;
            default:
                $query->orderBy('created_at', $direction);
        }
        
        $images = $query->paginate(24)->withQueryString();
        
        // Get additional data
        $albums = Album::where('is_published', true)->get(['id', 'title', 'slug']);
        
        // Check upload permission
        $canUpload = $currentUser && (
            $currentUser->hasRole('admin') || 
            $currentUser->hasRole('editor')
        );
        
        return Inertia::render('Images/Index', [
            'images' => $images,
            'albums' => $albums,
            'filters' => $request->only(['search', 'album', 'privacy', 'published', 'date_from', 'date_to', 'sort', 'direction']),
            'canUpload' => $canUpload,
            'isMyImages' => $isMyImages,
        ]);
    }

    /**
     * Bulk operations for user's images
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,publish,unpublish,privacy',
            'image_ids' => 'required|array',
            'image_ids.*' => 'exists:images,id',
            'privacy_level' => 'required_if:action,privacy|in:public,unlisted,private'
        ]);

        $imageIds = $request->image_ids;
        $currentUser = auth()->user();
        
        // Only allow bulk operations on user's own images
        $images = Image::whereIn('id', $imageIds)
                      ->where('owner_id', $currentUser->id)
                      ->get();

        if ($images->isEmpty()) {
            return back()->withErrors(['images' => 'No valid images selected.']);
        }

        $count = $images->count();
        $message = '';

        switch ($request->action) {
            case 'delete':
                foreach ($images as $image) {
                    $this->authorize('delete', $image);
                    
                    // Delete files
                    Storage::disk('minio')->delete($image->storage_path);
                    
                    // Update user storage usage
                    $currentUser->decrementStorageUsage($image->size_bytes);
                    
                    $image->delete();
                }
                $message = "{$count} images deleted successfully.";
                break;

            case 'publish':
                $images->each(function ($image) {
                    $this->authorize('update', $image);
                    $image->update([
                        'is_published' => true,
                        'published_at' => now(),
                    ]);
                });
                $message = "{$count} images published successfully.";
                break;

            case 'unpublish':
                $images->each(function ($image) {
                    $this->authorize('update', $image);
                    $image->update([
                        'is_published' => false,
                        'published_at' => null,
                    ]);
                });
                $message = "{$count} images unpublished successfully.";
                break;

            case 'privacy':
                $images->each(function ($image) use ($request) {
                    $this->authorize('update', $image);
                    $image->update([
                        'privacy' => $request->privacy_level,
                        'is_published' => $request->privacy_level !== 'private',
                    ]);
                });
                $message = "{$count} images privacy updated successfully.";
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Display the specified image.
     */
    public function show(Image $image, Request $request)
    {
        $this->authorize('view', $image);

        $image->load(['owner', 'album']);

        // Record view - DON'T increment here to avoid view spam
        // Only increment if user visits show page directly, not through API calls
        if (!$request->ajax() && !$request->wantsJson()) {
            $image->increment('views_count');
        }

        // Get related images (from same album)
        $relatedImages = collect();
        
        if ($image->album_id) {
            $relatedImages = Image::where('album_id', $image->album_id)
                ->where('id', '!=', $image->id)
                ->whereIn('privacy', ['public', 'unlisted'])
                ->where('is_published', true)
                ->take(6)
                ->get();
        }

        // Get comments
        $comments = $image->comments()
            ->with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Images/Show', [
            'image' => [
                'id' => $image->id,
                'title' => $image->title,
                'caption' => $image->caption,
                'alt_text' => $image->alt_text,
                'slug' => $image->slug,
                'storage_path' => $image->storage_path,
                'privacy' => $image->privacy,
                'license' => $image->license,
                'original_filename' => $image->original_filename,
                'mime_type' => $image->mime_type,
                'size_bytes' => $image->size_bytes,
                'formatted_size' => $image->formatted_size,
                'width' => $image->width,
                'height' => $image->height,
                'dimensions' => $image->dimensions,
                'taken_at' => $image->taken_at,
                'camera_make' => $image->camera_make,
                'camera_model' => $image->camera_model,
                'views_count' => $image->views_count,
                'likes_count' => $image->likes_count,
                'comments_count' => $image->comments_count,
                'allow_comments' => $image->allow_comments,
                'allow_downloads' => $image->allow_downloads,
                'is_published' => $image->is_published,
                'created_at' => $image->created_at,
                'owner' => $image->owner,
                'album' => $image->album,
                'exif_data' => $image->exif_data,
            ],
            'relatedImages' => $relatedImages->map(function ($img) {
                return [
                    'id' => $img->id,
                    'title' => $img->title,
                    'slug' => $img->slug,
                    'storage_path' => $img->storage_path,
                    'alt_text' => $img->alt_text,
                ];
            }),
            'comments' => $comments,
            'can' => [
                'update' => auth()->user()?->can('update', $image) ?? false,
                'delete' => auth()->user()?->can('delete', $image) ?? false,
                'download' => auth()->user()?->can('download', $image) ?? false,
                'comment' => auth()->check() && $image->allow_comments,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified image.
     */
    public function edit(Image $image)
    {
        $this->authorize('update', $image);

        $image->load(['album']);

        return Inertia::render('Images/Edit', [
            'image' => [
                'id' => $image->id,
                'title' => $image->title,
                'caption' => $image->caption,
                'alt_text' => $image->alt_text,
                'slug' => $image->slug,
                'storage_path' => $image->storage_path,
                'privacy' => $image->privacy,
                'license' => $image->license,
                'album_id' => $image->album_id,
                'allow_comments' => $image->allow_comments,
                'allow_downloads' => $image->allow_downloads,
            ],
            'albums' => auth()->user()->albums()->select('id', 'title')->get(),
        ]);
    }

    /**
     * Update the specified image in storage.
     */
    public function update(Request $request, Image $image)
    {
        $this->authorize('update', $image);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:1000',
            'alt_text' => 'nullable|string|max:500',
            'privacy' => 'required|in:public,unlisted,private',
            'license' => 'nullable|string|max:100',
            'album_id' => 'nullable|exists:albums,id',
            'allow_comments' => 'boolean',
            'allow_downloads' => 'boolean',
        ]);

        // Verify album belongs to user
        if ($request->album_id) {
            $album = Album::find($request->album_id);
            if (!$album || ($album->owner_id !== auth()->id() && !auth()->user()->hasRole('admin'))) {
                return redirect()->back()
                    ->withErrors(['album_id' => 'Selected album not found or you do not have permission.']);
            }
        }

        $image->update([
            'title' => $request->title,
            'caption' => $request->caption,
            'alt_text' => $request->alt_text,
            'privacy' => $request->privacy,
            'license' => $request->license,
            'album_id' => $request->album_id,
            'allow_comments' => $request->allow_comments ?? true,
            'allow_downloads' => $request->allow_downloads ?? true,
            'is_published' => $request->privacy !== 'private',
            'published_at' => $request->privacy !== 'private' && !$image->is_published ? now() : $image->published_at,
        ]);

        return redirect()->route('images.show', $image)
            ->with('success', 'Image updated successfully.');
    }

    /**
     * Remove the specified image from storage.
     */
    public function destroy(Image $image)
    {
        $this->authorize('delete', $image);

        // Store album ID for updating count later
        $albumId = $image->album_id;

        // Delete image files from storage
        Storage::disk('minio')->delete($image->storage_path);

        // Update user storage usage
        $image->owner->decrementStorageUsage($image->size_bytes);

        // Delete database records
        $image->delete();

        // Update album image count
        if ($albumId) {
            Album::find($albumId)?->updateImageCount();
        }

        return redirect()->route('gallery.index')
            ->with('success', 'Image deleted successfully.');
    }

    /**
     * Download the image with optional watermark.
     */
    public function download(Image $image, Request $request)
    {
        $this->authorize('download', $image);

        $variant = $request->get('variant', 'original');
        $path = $image->storage_path;
        $filename = $image->original_filename;

        // Get file contents from MinIO
        $fileContents = Storage::disk('minio')->get($path);
        $mimeType = Storage::disk('minio')->mimeType($path);

        // Force download with proper headers
        return response($fileContents)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Length', strlen($fileContents))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Publish/unpublish image.
     */
    public function togglePublish(Image $image)
    {
        $this->authorize('update', $image);

        if ($image->is_published) {
            $image->update([
                'is_published' => false,
                'published_at' => null,
            ]);
            $message = 'Image unpublished successfully.';
        } else {
            $image->update([
                'is_published' => true,
                'published_at' => now(),
            ]);
            $message = 'Image published successfully.';
        }

        return redirect()->back()
            ->with('success', $message);
    }
}
