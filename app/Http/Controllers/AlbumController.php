<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AlbumController extends Controller
{
    /**
     * Display a listing of albums.
     */
    public function index(Request $request)
    {
        $query = Album::with(['owner', 'coverImage'])
            ->withCount('images');

        // Only show visible albums to non-owners
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            $query->visible();
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
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

        $albums = $query->orderBy('updated_at', 'desc')
            ->paginate(12);

        return Inertia::render('Albums/Index', [
            'albums' => $albums,
            'filters' => $request->only(['search', 'owner']),
            'canCreate' => auth()->check() && auth()->user()->can('create', Album::class),
            'auth' => [
                'user' => auth()->user(),
                'roles' => auth()->user()?->roles ?? [],
            ],
            'errors' => session('errors') ?? [],
        ]);
    }

    /**
     * Show the form for creating a new album.
     */
    public function create()
    {
        $this->authorize('create', Album::class);

        return Inertia::render('Albums/Create');
    }

    /**
     * Store a newly created album in storage.
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

        return redirect()->route('albums.show', $album)
            ->with('success', 'Album created successfully.');
    }

    /**
     * Display the specified album.
     */
    public function show(Album $album, Request $request)
    {
        $this->authorize('view', $album);

        $query = $album->images()->with(['owner', 'tags']);

        // Search within album
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('caption', 'like', '%' . $request->search . '%');
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

        $images = $query->paginate(24);

        // Record view
        if ($album->isVisible()) {
            $album->viewCounts()->updateOrCreate(
                ['date' => now()->toDateString()],
                ['count' => \DB::raw('count + 1')]
            );
        }

        return Inertia::render('Albums/Show', [
            'album' => [
                'id' => $album->id,
                'title' => $album->title,
                'slug' => $album->slug,
                'description' => $album->description,
                'privacy' => $album->privacy,
                'is_published' => $album->is_published,
                'image_count' => $album->image_count,
                'total_size_bytes' => $album->total_size_bytes,
                'formatted_size' => $album->formatted_size,
                'created_at' => $album->created_at,
                'updated_at' => $album->updated_at,
                'owner' => $album->owner,
                'cover_image' => $album->coverImage,
            ],
            'images' => $images,
            'filters' => $request->only(['search', 'tag', 'sort', 'direction']),
            'availableTags' => $album->images()
                ->with('tags')
                ->get()
                ->pluck('tags')
                ->flatten()
                ->unique('id')
                ->values(),
            'can' => [
                'update' => auth()->user()?->can('update', $album) ?? false,
                'delete' => auth()->user()?->can('delete', $album) ?? false,
                'manageImages' => auth()->user()?->can('manageImages', $album) ?? false,
            ],
            'auth' => [
                'user' => auth()->user(),
                'roles' => auth()->user()?->roles ?? [],
            ],
            'errors' => session('errors') ?? [],
        ]);
    }

    /**
     * Show the form for editing the specified album.
     */
    public function edit(Album $album)
    {
        $this->authorize('update', $album);

        return Inertia::render('Albums/Edit', [
            'album' => [
                'id' => $album->id,
                'title' => $album->title,
                'description' => $album->description,
                'privacy' => $album->privacy,
                'is_published' => $album->is_published,
                'cover_image_id' => $album->cover_image_id,
            ],
            'images' => $album->images()
                ->select('id', 'title', 'storage_path')
                ->get(),
        ]);
    }

    /**
     * Update the specified album in storage.
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

        // Verify cover image belongs to this album
        if ($request->cover_image_id) {
            $coverImage = Image::find($request->cover_image_id);
            if (!$coverImage || $coverImage->album_id !== $album->id) {
                return redirect()->back()
                    ->withErrors(['cover_image_id' => 'Selected cover image must belong to this album.']);
            }
        }

        $album->update([
            'title' => $request->title,
            'description' => $request->description,
            'privacy' => $request->privacy,
            'cover_image_id' => $request->cover_image_id,
            'is_published' => $request->privacy !== 'private',
            'published_at' => $request->privacy !== 'private' && !$album->is_published ? now() : $album->published_at,
        ]);

        return redirect()->route('albums.show', $album)
            ->with('success', 'Album updated successfully.');
    }

    /**
     * Remove the specified album from storage.
     */
    public function destroy(Album $album)
    {
        $this->authorize('delete', $album);

        // Move images to no album
        $album->images()->update(['album_id' => null]);

        $album->delete();

        return redirect()->route('albums.index')
            ->with('success', 'Album deleted successfully. Images were preserved.');
    }

    /**
     * Reorder images in the album.
     */
    public function reorder(Request $request, Album $album)
    {
        $this->authorize('manageImages', $album);

        $request->validate([
            'image_ids' => 'required|array',
            'image_ids.*' => 'exists:images,id',
        ]);

        // Verify all images belong to this album
        $albumImageIds = $album->images()->pluck('id')->toArray();
        $invalidIds = array_diff($request->image_ids, $albumImageIds);
        
        if (!empty($invalidIds)) {
            return redirect()->back()
                ->withErrors(['image_ids' => 'Some images do not belong to this album.']);
        }

        $album->reorderImages($request->image_ids);

        return redirect()->back()
            ->with('success', 'Album images reordered successfully.');
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
            return redirect()->back()
                ->withErrors(['image_ids' => 'You can only add your own images to the album.']);
        }

        Image::whereIn('id', $request->image_ids)
            ->update(['album_id' => $album->id]);

        $album->updateImageCount();

        $count = count($request->image_ids);
        return redirect()->back()
            ->with('success', "{$count} images added to album successfully.");
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

        Image::whereIn('id', $request->image_ids)
            ->where('album_id', $album->id)
            ->update(['album_id' => null]);

        $album->updateImageCount();

        $count = count($request->image_ids);
        return redirect()->back()
            ->with('success', "{$count} images removed from album successfully.");
    }

    /**
     * Publish/unpublish album.
     */
    public function togglePublish(Album $album)
    {
        $this->authorize('publish', $album);

        if ($album->is_published) {
            $album->unpublish();
            $message = 'Album unpublished successfully.';
        } else {
            $album->publish();
            $message = 'Album published successfully.';
        }

        return redirect()->back()
            ->with('success', $message);
    }
}
