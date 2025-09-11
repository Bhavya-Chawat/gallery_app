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
        $query = Image::with(['owner', 'album', 'tags']);

        // Only show visible images to non-owners
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            $query->visible();
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

        // Filter by tag
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        // Filter by owner
        if ($request->filled('owner') && auth()->check()) {
            if ($request->owner === 'mine') {
                $query->where('owner_id', auth()->id());
            } else {
                $query->where('owner_id', $request->owner);
            }
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        // Sort options
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
                break;
        }

        $images = $query->paginate(24);

        return Inertia::render('Images/Index', [
            'images' => $images,
            'filters' => $request->only(['search', 'album', 'tag', 'owner', 'date_from', 'date_to', 'sort', 'direction']),
            'albums' => Album::visible()->select('id', 'title', 'slug')->get(),
            'tags' => Tag::popular()->select('id', 'name', 'slug')->take(20)->get(),
            'canUpload' => auth()->check() && auth()->user()->can('create', Image::class),
            'auth' => [
                'user' => auth()->user(),
                'roles' => auth()->user()?->roles ?? [],
            ],
            'errors' => session('errors') ?? [],
        ]);
    }

    /**
     * Display the specified image.
     */
    public function show(Image $image, Request $request)
    {
        $this->authorize('view', $image);

        $image->load(['owner', 'album', 'tags', 'versions']);

        // Record view
        if ($image->isVisible()) {
            $image->incrementViews();
            $image->viewCounts()->updateOrCreate(
                ['date' => now()->toDateString()],
                ['count' => \DB::raw('count + 1')]
            );
        }

        // Get related images (from same album or with similar tags)
        $relatedImages = collect();
        
        if ($image->album_id) {
            $relatedImages = $relatedImages->merge(
                Image::where('album_id', $image->album_id)
                    ->where('id', '!=', $image->id)
                    ->visible()
                    ->take(6)
                    ->get()
            );
        }

        if ($relatedImages->count() < 6) {
            $tagIds = $image->tags->pluck('id');
            if ($tagIds->isNotEmpty()) {
                $additionalImages = Image::whereHas('tags', function ($q) use ($tagIds) {
                        $q->whereIn('tag_id', $tagIds);
                    })
                    ->where('id', '!=', $image->id)
                    ->whereNotIn('id', $relatedImages->pluck('id'))
                    ->visible()
                    ->take(6 - $relatedImages->count())
                    ->get();
                
                $relatedImages = $relatedImages->merge($additionalImages);
            }
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
                'created_at' => $image->created_at,
                'owner' => $image->owner,
                'album' => $image->album,
                'tags' => $image->tags,
                'versions' => $image->versions->map(function ($version) {
                    return [
                        'variant' => $version->variant,
                        'format' => $version->format,
                        'url' => $version->getUrl(),
                        'width' => $version->width,
                        'height' => $version->height,
                        'size_bytes' => $version->size_bytes,
                    ];
                }),
                'exif_data' => $image->exif_data,
            ],
            'relatedImages' => $relatedImages,
            'comments' => $comments,
            'userLike' => auth()->check() ? $image->likes()->where('user_id', auth()->id())->exists() : false,
            'can' => [
                'update' => auth()->user()?->can('update', $image) ?? false,
                'delete' => auth()->user()?->can('delete', $image) ?? false,
                'download' => auth()->user()?->can('download', $image) ?? false,
                'comment' => auth()->check() && $image->allow_comments && auth()->user()->can('create', \App\Models\Comment::class),
                'like' => auth()->check(),
            ],
            'auth' => [
                'user' => auth()->user(),
                'roles' => auth()->user()?->roles ?? [],
            ],
            'errors' => session('errors') ?? [],
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
                'privacy' => $image->privacy,
                'license' => $image->license,
                'album_id' => $image->album_id,
                'allow_comments' => $image->allow_comments,
                'allow_downloads' => $image->allow_downloads,
                'tags' => $image->tags->pluck('name'),
            ],
            'albums' => auth()->user()->albums()->select('id', 'title')->get(),
            'availableTags' => Tag::select('id', 'name', 'slug')->get(),
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
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
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

        // Update tags
        if ($request->has('tags')) {
            $tags = collect($request->tags)->map(function ($tagName) {
                return Tag::firstOrCreate(['name' => trim($tagName)]);
            });
            
            $image->tags()->sync($tags->pluck('id'));
            
            // Update tag usage counts
            Tag::all()->each(function ($tag) {
                $tag->updateUsageCount();
            });
        }

        // Update album image count if album changed
        if ($image->wasChanged('album_id')) {
            if ($image->getOriginal('album_id')) {
                Album::find($image->getOriginal('album_id'))?->updateImageCount();
            }
            if ($image->album_id) {
                $image->album->updateImageCount();
            }
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
        Storage::disk('s3')->delete($image->storage_path);
        
        // Delete all versions
        foreach ($image->versions as $version) {
            Storage::disk('s3')->delete($version->storage_path);
        }

        // Update user storage usage
        $image->owner->decrementStorageUsage($image->size_bytes);

        // Delete database records
        $image->delete();

        // Update album image count
        if ($albumId) {
            Album::find($albumId)?->updateImageCount();
        }

        // Update tag usage counts
        Tag::all()->each(function ($tag) {
            $tag->updateUsageCount();
        });

        return redirect()->route('images.index')
            ->with('success', 'Image deleted successfully.');
    }

    /**
     * Download the image.
     */
    public function download(Image $image, Request $request)
    {
        $this->authorize('download', $image);

        $variant = $request->get('variant', 'original');
        
        if ($variant === 'original') {
            $url = $image->getSignedUrl('original', 300);
            $filename = $image->original_filename;
        } else {
            $version = $image->versions()->where('variant', $variant)->first();
            if (!$version) {
                abort(404, 'Image variant not found.');
            }
            $url = $version->getSignedUrl(300);
            $filename = pathinfo($image->original_filename, PATHINFO_FILENAME) . "_{$variant}." . pathinfo($image->original_filename, PATHINFO_EXTENSION);
        }

        return redirect($url);
    }

    /**
     * Get signed URL for private image.
     */
    public function signedUrl(Image $image, Request $request)
    {
        $this->authorize('view', $image);

        $variant = $request->get('variant', 'original');
        $ttl = min($request->get('ttl', 300), 3600); // Max 1 hour

        $url = $image->getSignedUrl($variant, $ttl);

        return response()->json([
            'url' => $url,
            'expires_at' => now()->addSeconds($ttl),
        ]);
    }

    /**
     * Toggle like on image.
     */
    public function toggleLike(Image $image)
    {
        $user = auth()->user();
        
        $existingLike = $image->likes()
            ->where('user_id', $user->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $message = 'Like removed.';
        } else {
            $image->likes()->create([
                'user_id' => $user->id,
                'user_ip' => request()->ip(),
            ]);
            $message = 'Image liked.';
        }

        return redirect()->back()
            ->with('success', $message);
    }

    /**
     * Publish/unpublish image.
     */
    public function togglePublish(Image $image)
    {
        $this->authorize('publish', $image);

        if ($image->is_published) {
            $image->unpublish();
            $message = 'Image unpublished successfully.';
        } else {
            $image->publish();
            $message = 'Image published successfully.';
        }

        return redirect()->back()
            ->with('success', $message);
    }
}
