<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlbumResource;
use App\Models\Album;
use Illuminate\Http\Request;

class AlbumApiController extends Controller
{
    /**
     * Display a listing of albums.
     */
    public function index(Request $request)
    {
        $query = Album::with(['owner', 'coverImage'])->withCount('images');

        // Only show visible albums to non-owners
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            $query->visible();
        }

        // Search
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }

        // Filters
        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        if ($request->filled('privacy')) {
            $query->where('privacy', $request->privacy);
        }

        // Sorting
        $sort = $request->get('sort', 'updated_at');
        $direction = $request->get('direction', 'desc');

        switch ($sort) {
            case 'title':
                $query->orderBy('title', $direction);
                break;
            case 'created_at':
                $query->orderBy('created_at', $direction);
                break;
            case 'image_count':
                $query->orderBy('image_count', $direction);
                break;
            default:
                $query->orderBy('updated_at', $direction);
                break;
        }

        $albums = $query->paginate($request->get('per_page', 12));

        return AlbumResource::collection($albums);
    }

    /**
     * Store a newly created album.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Album::class);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'privacy' => 'required|in:public,unlisted,private',
        ]);

        $album = Album::create([
            'owner_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'privacy' => $request->privacy,
            'is_published' => $request->privacy !== 'private',
            'published_at' => $request->privacy !== 'private' ? now() : null,
        ]);

        return new AlbumResource($album->load(['owner', 'coverImage']));
    }

    /**
     * Display the specified album.
     */
    public function show(Album $album)
    {
        $this->authorize('view', $album);

        $album->load(['owner', 'coverImage']);

        // Record view
        if ($album->isVisible()) {
            $album->viewCounts()->updateOrCreate(
                ['date' => now()->toDateString()],
                ['count' => \DB::raw('count + 1')]
            );
        }

        return new AlbumResource($album);
    }

    /**
     * Update the specified album.
     */
    public function update(Request $request, Album $album)
    {
        $this->authorize('update', $album);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'privacy' => 'required|in:public,unlisted,private',
            'cover_image_id' => 'nullable|exists:images,id',
        ]);

        $album->update($request->only([
            'title', 'description', 'privacy', 'cover_image_id'
        ]));

        return new AlbumResource($album->load(['owner', 'coverImage']));
    }

    /**
     * Remove the specified album.
     */
    public function destroy(Album $album)
    {
        $this->authorize('delete', $album);

        // Move images to no album
        $album->images()->update(['album_id' => null]);

        $album->delete();

        return response()->json(['message' => 'Album deleted successfully']);
    }

    /**
     * Get album images.
     */
    public function images(Album $album, Request $request)
    {
        $this->authorize('view', $album);

        $query = $album->images()->with(['owner', 'tags', 'versions']);

        // Search within album
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('caption', 'like', '%' . $request->q . '%');
            });
        }

        // Filter by tag
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
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
            default:
                $query->orderBy('created_at', $direction);
                break;
        }

        $images = $query->paginate($request->get('per_page', 24));

        return \App\Http\Resources\ImageResource::collection($images);
    }

    /**
     * Reorder images in album.
     */
    public function reorder(Request $request, Album $album)
    {
        $this->authorize('manageImages', $album);

        $request->validate([
            'image_ids' => 'required|array',
            'image_ids.*' => 'exists:images,id',
        ]);

        $album->reorderImages($request->image_ids);

        return response()->json(['message' => 'Images reordered successfully']);
    }

    /**
     * Add images to album.
     */
    public function addImages(Request $request, Album $album)
    {
        $this->authorize('manageImages', $album);

        $request->validate([
            'image_ids' => 'required|array|min:1',
            'image_ids.*' => 'exists:images,id',
        ]);

        // Verify user owns the images
        $userImageIds = auth()->user()->images()->pluck('id')->toArray();
        $invalidIds = array_diff($request->image_ids, $userImageIds);
        
        if (!empty($invalidIds)) {
            return response()->json([
                'message' => 'You can only add your own images to the album.',
                'errors' => ['image_ids' => ['Some images do not belong to you.']]
            ], 422);
        }

        \App\Models\Image::whereIn('id', $request->image_ids)
            ->update(['album_id' => $album->id]);

        $album->updateImageCount();

        return response()->json([
            'message' => count($request->image_ids) . ' images added to album successfully'
        ]);
    }

    /**
     * Remove images from album.
     */
    public function removeImages(Request $request, Album $album)
    {
        $this->authorize('manageImages', $album);

        $request->validate([
            'image_ids' => 'required|array|min:1',
            'image_ids.*' => 'exists:images,id',
        ]);

        \App\Models\Image::whereIn('id', $request->image_ids)
            ->where('album_id', $album->id)
            ->update(['album_id' => null]);

        $album->updateImageCount();

        return response()->json([
            'message' => count($request->image_ids) . ' images removed from album successfully'
        ]);
    }
}
