<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use App\Models\Tag;
use Illuminate\Http\Request;

class ImageApiController extends Controller
{
    /**
     * Display a listing of images.
     */
    public function index(Request $request)
    {
        $query = Image::with(['owner', 'album', 'tags', 'versions']);

        // Only show visible images to non-owners
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            $query->visible();
        }

        // Search
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('caption', 'like', '%' . $request->q . '%')
                  ->orWhere('alt_text', 'like', '%' . $request->q . '%');
            });
        }

        // Filters
        if ($request->filled('album_id')) {
            $query->where('album_id', $request->album_id);
        }

        if ($request->filled('tags')) {
            $tags = is_array($request->tags) ? $request->tags : explode(',', $request->tags);
            $query->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('slug', $tags);
            });
        }

        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        if ($request->filled('privacy')) {
            $query->where('privacy', $request->privacy);
        }

        if ($request->filled('license')) {
            $query->where('license', $request->license);
        }

        if ($request->filled('camera_make')) {
            $query->where('camera_make', 'like', '%' . $request->camera_make . '%');
        }

        if ($request->filled('camera_model')) {
            $query->where('camera_model', 'like', '%' . $request->camera_model . '%');
        }

        // Date filters
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        if ($request->filled('taken_from')) {
            $query->where('taken_at', '>=', $request->taken_from);
        }

        if ($request->filled('taken_to')) {
            $query->where('taken_at', '<=', $request->taken_to);
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
            case 'taken_at':
                $query->orderBy('taken_at', $direction);
                break;
            default:
                $query->orderBy('created_at', $direction);
                break;
        }

        $images = $query->paginate($request->get('per_page', 24));

        return ImageResource::collection($images);
    }

    /**
     * Display the specified image.
     */
    public function show(Image $image)
    {
        $this->authorize('view', $image);

        $image->load(['owner', 'album', 'tags', 'versions']);

        // Record view for visible images
        if ($image->isVisible()) {
            $image->incrementViews();
            $image->viewCounts()->updateOrCreate(
                ['date' => now()->toDateString()],
                ['count' => \DB::raw('count + 1')]
            );
        }

        return new ImageResource($image);
    }

    /**
     * Update the specified image.
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

        $image->update($request->only([
            'title', 'caption', 'alt_text', 'privacy', 'license', 
            'album_id', 'allow_comments', 'allow_downloads'
        ]));

        // Update tags
        if ($request->has('tags')) {
            $tags = collect($request->tags)->map(function ($tagName) {
                return Tag::firstOrCreate(['name' => trim($tagName)]);
            });
            
            $image->tags()->sync($tags->pluck('id'));
        }

        return new ImageResource($image->load(['owner', 'album', 'tags', 'versions']));
    }

    /**
     * Remove the specified image.
     */
    public function destroy(Image $image)
    {
        $this->authorize('delete', $image);

        // Update user storage usage
        $image->owner->decrementStorageUsage($image->size_bytes);

        $image->delete();

        return response()->json(['message' => 'Image deleted successfully']);
    }

    /**
     * Bulk operations on images.
     */
    public function bulk(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,move,edit,publish,unpublish',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:images,id',
            'payload' => 'nullable|array',
        ]);

        $images = Image::whereIn('id', $request->ids)->get();
        $processed = 0;

        foreach ($images as $image) {
            switch ($request->action) {
                case 'delete':
                    if (auth()->user()->can('delete', $image)) {
                        $image->owner->decrementStorageUsage($image->size_bytes);
                        $image->delete();
                        $processed++;
                    }
                    break;

                case 'move':
                    if (auth()->user()->can('update', $image) && isset($request->payload['album_id'])) {
                        $image->update(['album_id' => $request->payload['album_id']]);
                        $processed++;
                    }
                    break;

                case 'edit':
                    if (auth()->user()->can('update', $image) && $request->payload) {
                        $image->update(array_intersect_key($request->payload, array_flip([
                            'privacy', 'license', 'allow_comments', 'allow_downloads'
                        ])));
                        $processed++;
                    }
                    break;

                case 'publish':
                    if (auth()->user()->can('publish', $image)) {
                        $image->publish();
                        $processed++;
                    }
                    break;

                case 'unpublish':
                    if (auth()->user()->can('publish', $image)) {
                        $image->unpublish();
                        $processed++;
                    }
                    break;
            }
        }

        return response()->json([
            'message' => "{$processed} images {$request->action}d successfully",
            'processed' => $processed,
            'total' => count($request->ids)
        ]);
    }

    /**
     * Get signed URL for image download.
     */
    public function signedUrl(Request $request, Image $image)
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
            $liked = false;
        } else {
            $image->likes()->create([
                'user_id' => $user->id,
                'user_ip' => request()->ip(),
            ]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $image->fresh()->likes_count,
        ]);
    }
}
