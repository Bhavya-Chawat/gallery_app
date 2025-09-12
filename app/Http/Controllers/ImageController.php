<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Image;
use App\Models\Tag;
use App\Models\Like;
use App\Models\Collection;
use App\Models\ViewCount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ImageController extends Controller
{
    /**
     * Display public gallery images only.
     */
    public function index(Request $request)
    {
        $query = Image::with(['owner', 'album', 'tags']);
        
        // FIXED: Public gallery - ONLY show published public/unlisted images
        $query->whereIn('privacy', ['public', 'unlisted'])
              ->where('is_published', true);
        
        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('caption', 'like', '%' . $request->search . '%')
                  ->orWhere('alt_text', 'like', '%' . $request->search . '%')
                  ->orWhereHas('tags', function ($tagQuery) use ($request) {
                      $tagQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        // Filter by tag
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($tagQuery) use ($request) {
                $tagQuery->where('name', $request->tag);
            });
        }
        
        // Filter by album (only public albums)
        if ($request->filled('album')) {
            if ($request->album === 'none') {
                $query->whereNull('album_id');
            } else {
                $query->where('album_id', $request->album)
                      ->whereHas('album', function($q) {
                          $q->whereIn('privacy', ['public', 'unlisted'])
                            ->where('is_published', true);
                      });
            }
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
            case 'likes':
                $query->orderBy('likes_count', $direction);
                break;
            case 'size':
                $query->orderBy('size_bytes', $direction);
                break;
            default:
                $query->orderBy('created_at', $direction);
        }
        
        // FIXED: Paginate with proper count
        $images = $query->paginate(24)->withQueryString();
        
        // Add user like status if authenticated
        $currentUser = auth()->user();
        if ($currentUser) {
            $imageIds = $images->pluck('id');
            $userLikes = Like::where('user_id', $currentUser->id)
                ->where('likeable_type', Image::class)
                ->whereIn('likeable_id', $imageIds)
                ->pluck('likeable_id')
                ->toArray();
            
            $images->getCollection()->transform(function ($image) use ($userLikes) {
                $image->user_has_liked = in_array($image->id, $userLikes);
                return $image;
            });
        }
        
        // Get public albums for filter dropdown
        $albums = Album::where('is_published', true)
                      ->whereIn('privacy', ['public', 'unlisted'])
                      ->select('id', 'title', 'slug')
                      ->get();
        
        // Get popular tags
        $popularTags = Tag::popular()->take(20)->get();
        
        // Check upload permission
        $canUpload = $currentUser && (
            $currentUser->hasRole('admin') || 
            $currentUser->hasRole('editor')
        );
        
        return Inertia::render('Images/Index', [
            'images' => $images,
            'albums' => $albums,
            'popularTags' => $popularTags,
            'filters' => $request->only(['search', 'tag', 'album', 'date_from', 'date_to', 'sort', 'direction']),
            'canUpload' => $canUpload,
            'isMyImages' => false,
        ]);
    }

    /**
     * Display user's own images with all statuses.
     */
    public function myImages(Request $request)
    {
        $user = auth()->user();
        
        $query = Image::with(['owner', 'album', 'tags'])
            ->where('owner_id', $user->id);
        
        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('caption', 'like', '%' . $request->search . '%')
                  ->orWhere('alt_text', 'like', '%' . $request->search . '%')
                  ->orWhereHas('tags', function ($tagQuery) use ($request) {
                      $tagQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        // Filter by tag
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($tagQuery) use ($request) {
                $tagQuery->where('name', $request->tag);
            });
        }
        
        // Filter by album (user's albums only)
        if ($request->filled('album')) {
            if ($request->album === 'none') {
                $query->whereNull('album_id');
            } else {
                $query->where('album_id', $request->album);
            }
        }
        
        // Filter by privacy
        if ($request->filled('privacy')) {
            $query->where('privacy', $request->privacy);
        }
        
        // Filter by published status
        if ($request->filled('published')) {
            if ($request->published === 'published') {
                $query->where('is_published', true);
            } elseif ($request->published === 'unpublished') {
                $query->where('is_published', false);
            }
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
            case 'likes':
                $query->orderBy('likes_count', $direction);
                break;
            case 'size':
                $query->orderBy('size_bytes', $direction);
                break;
            default:
                $query->orderBy('created_at', $direction);
        }
        
        // FIXED: Paginate with proper count
        $images = $query->paginate(24)->withQueryString();
        
        // Add user like status to each image
        $imageIds = $images->pluck('id');
        $userLikes = Like::where('user_id', $user->id)
            ->where('likeable_type', Image::class)
            ->whereIn('likeable_id', $imageIds)
            ->pluck('likeable_id')
            ->toArray();
        
        $images->getCollection()->transform(function ($image) use ($userLikes) {
            $image->user_has_liked = in_array($image->id, $userLikes);
            return $image;
        });
        
        // Get user's albums and tags for filter dropdowns
        $albums = $user->albums()->select('id', 'title')->get();
        $userTags = Tag::whereHas('images', function ($query) use ($user) {
            $query->where('owner_id', $user->id);
        })->get();
        
        return Inertia::render('Images/Index', [
            'images' => $images,
            'albums' => $albums,
            'popularTags' => $userTags,
            'filters' => $request->only(['search', 'tag', 'album', 'privacy', 'published', 'date_from', 'date_to', 'sort', 'direction']),
            'canUpload' => true,
            'isMyImages' => true,
        ]);
    }

    /**
     * Enhanced bulk operations including move to album and tag operations
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,publish,unpublish,privacy,move_to_album,add_tags,remove_tags',
            'image_ids' => 'required|array',
            'image_ids.*' => 'exists:images,id',
            'privacy_level' => 'required_if:action,privacy|in:public,unlisted,private',
            'album_id' => 'required_if:action,move_to_album|nullable|exists:albums,id',
            'tags' => 'required_if:action,add_tags,remove_tags|array',
            'tags.*' => 'string|max:50',
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

        // Verify album ownership if moving to album
        if ($request->action === 'move_to_album' && $request->album_id) {
            $album = Album::find($request->album_id);
            if (!$album || ($album->owner_id !== $currentUser->id && !$currentUser->hasRole('admin'))) {
                return back()->withErrors(['album_id' => 'Selected album not found or access denied.']);
            }
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

            case 'move_to_album':
                $images->each(function ($image) use ($request) {
                    $this->authorize('update', $image);
                    $image->update(['album_id' => $request->album_id]);
                });
                
                if ($request->album_id) {
                    $album = Album::find($request->album_id);
                    $message = "{$count} images moved to album '{$album->title}' successfully.";
                } else {
                    $message = "{$count} images removed from albums successfully.";
                }
                break;

            case 'add_tags':
                $tagIds = [];
                foreach ($request->tags as $tagName) {
                    $tag = Tag::firstOrCreate(['name' => trim($tagName)]);
                    $tagIds[] = $tag->id;
                }
                
                foreach ($images as $image) {
                    $this->authorize('update', $image);
                    $image->tags()->syncWithoutDetaching($tagIds);
                }
                
                // Update tag usage counts
                Tag::whereIn('id', $tagIds)->each(function ($tag) {
                    $tag->updateUsageCount();
                });
                
                $message = "Tags added to {$count} images successfully.";
                break;

            case 'remove_tags':
                $tagIds = Tag::whereIn('name', $request->tags)->pluck('id');
                
                foreach ($images as $image) {
                    $this->authorize('update', $image);
                    $image->tags()->detach($tagIds);
                }
                
                // Update tag usage counts
                Tag::whereIn('id', $tagIds)->each(function ($tag) {
                    $tag->updateUsageCount();
                });
                
                $message = "Tags removed from {$count} images successfully.";
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

        $image->load(['owner', 'album', 'tags', 'collections' => function($query) {
            $query->with('curator');
        }]);

        // FIXED: Record view - increment view count
        if (!$request->ajax() && !$request->wantsJson()) {
            $this->recordView($image, $request);
        }

        // Get related images (from same album or with similar tags)
        $relatedImages = $this->getRelatedImages($image);

        // Get comments
        $comments = $image->comments()
            ->with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        // Check if current user liked this image
        $userLike = false;
        if (auth()->check()) {
            $userLike = Like::where([
                'likeable_type' => Image::class,
                'likeable_id' => $image->id,
                'user_id' => auth()->id(),
            ])->exists();
        }

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
                'owner_id' => $image->owner_id,
                'album' => $image->album,
                'tags' => $image->tags,
                'collections' => $image->collections->map(function($collection) {
                    return [
                        'id' => $collection->id,
                        'title' => $collection->title,  
                        'slug' => $collection->slug,
                        'curator' => [
                            'id' => $collection->curator->id,
                            'name' => $collection->curator->name,
                        ]
                    ];
                }),
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
            'userLike' => $userLike,
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

        $image->load(['album', 'tags']);

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
                'tags' => $image->tags->pluck('name'),
            ],
            'albums' => auth()->user()->albums()->select('id', 'title')->get(),
            'popularTags' => Tag::popular()->take(50)->get(),
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
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
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

        // Update tags
        if ($request->has('tags')) {
            $tagIds = [];
            foreach ($request->tags as $tagName) {
                if (!empty(trim($tagName))) {
                    $tag = Tag::firstOrCreate(['name' => trim($tagName)]);
                    $tagIds[] = $tag->id;
                }
            }
            $image->tags()->sync($tagIds);
            
            // Update tag usage counts
            Tag::whereIn('id', $tagIds)->each(function ($tag) {
                $tag->updateUsageCount();
            });
        }

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

        // Delete database records (tags, likes, comments will be cascade deleted)
        $image->delete();

        // Update album image count
        if ($albumId) {
            Album::find($albumId)?->updateImageCount();
        }

        return redirect()->route('gallery.index')
            ->with('success', 'Image deleted successfully.');
    }

    /**
     * Like/Unlike an image.
     */
    public function like(Image $image)
    {
        $this->authorize('view', $image);

        $user = auth()->user();
        
        $existingLike = Like::where([
            'likeable_type' => Image::class,
            'likeable_id' => $image->id,
            'user_id' => $user->id,
        ])->first();

        if ($existingLike) {
            $existingLike->delete();
            $image->decrement('likes_count');
            $liked = false;
        } else {
            Like::create([
                'likeable_type' => Image::class,
                'likeable_id' => $image->id,
                'user_id' => $user->id,
                'user_ip' => request()->ip(),
            ]);
            $image->increment('likes_count');
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $image->fresh()->likes_count,
        ]);
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

    // Private helper methods

    /**
     * FIXED: Record a view for the image with proper session tracking
     */
    private function recordView(Image $image, Request $request)
    {
        // Track unique views per session to avoid inflating counts
        $sessionKey = 'viewed_images';
        $viewedImages = session($sessionKey, []);
        
        if (!in_array($image->id, $viewedImages)) {
            // Increment the image view count
            $image->increment('views_count');
            
            // Add to session to prevent duplicate counting
            $viewedImages[] = $image->id;
            session([$sessionKey => $viewedImages]);
            
            // Record in view_counts table for analytics
            ViewCount::updateOrCreate(
                [
                    'viewable_type' => Image::class,
                    'viewable_id' => $image->id,
                    'date' => now()->format('Y-m-d'),
                ],
                []
            )->increment('count');
        }
    }

    /**
     * Get related images based on album or tags
     */
    private function getRelatedImages(Image $image)
    {
        $relatedQuery = Image::where('id', '!=', $image->id)
            ->whereIn('privacy', ['public', 'unlisted'])
            ->where('is_published', true);

        // First, try to get images from the same album
        if ($image->album_id) {
            $albumRelated = $relatedQuery->clone()
                ->where('album_id', $image->album_id)
                ->take(6)
                ->get();
            
            if ($albumRelated->count() >= 3) {
                return $albumRelated;
            }
        }

        // If not enough from album, get images with similar tags
        if ($image->tags->isNotEmpty()) {
            $tagIds = $image->tags->pluck('id');
            $tagRelated = $relatedQuery->clone()
                ->whereHas('tags', function ($query) use ($tagIds) {
                    $query->whereIn('tags.id', $tagIds);
                })
                ->withCount(['tags' => function ($query) use ($tagIds) {
                    $query->whereIn('tags.id', $tagIds);
                }])
                ->orderByDesc('tags_count')
                ->take(6)
                ->get();
            
            return $tagRelated;
        }

        // Fallback to recent images from the same owner
        return $relatedQuery->clone()
            ->where('owner_id', $image->owner_id)
            ->latest()
            ->take(6)
            ->get();
    }
}
