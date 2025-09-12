<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Image;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AlbumController extends Controller
{
    /**
     * Display a listing of albums.
     */
    public function index(Request $request)
    {
        $query = Album::with(['owner', 'coverImage'])->withCount('images');
        
        // Check if this is "My Albums" request
        $isMyAlbums = $request->get('owner') === 'mine' || $request->get('show_all');
        $currentUser = auth()->user();
        
        // Apply visibility scope based on context
        if ($isMyAlbums && $currentUser) {
            // For "My Albums" - show ALL user's albums (including private/unpublished)
            $query->where('owner_id', $currentUser->id);
        } else {
            // For public albums - ONLY show published public/unlisted albums
            $query->whereIn('privacy', ['public', 'unlisted'])
                  ->where('is_published', true);
        }
        
        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by privacy (only for "My Albums")
        if ($isMyAlbums && $request->filled('privacy')) {
            $query->where('privacy', $request->privacy);
        }
        
        // Filter by published status (only for "My Albums")  
        if ($isMyAlbums && $request->filled('published')) {
            $query->where('is_published', $request->published === 'published');
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
            case 'images_count':
                $query->orderBy('images_count', $direction);
                break;
            default:
                $query->orderBy('updated_at', $direction);
        }
        
        $albums = $query->paginate(12)->withQueryString();
        
        // Check create permission
        $canCreate = $currentUser && (
            $currentUser->hasRole('admin') || 
            $currentUser->hasRole('editor')
        );
        
        return Inertia::render('Albums/Index', [
            'albums' => $albums,
            'filters' => $request->only(['search', 'privacy', 'published', 'sort', 'direction']),
            'canCreate' => $canCreate,
            'isMyAlbums' => $isMyAlbums,
        ]);
    }

    /**
     * Bulk operations for user's albums
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,publish,unpublish,privacy',
            'album_ids' => 'required|array',
            'album_ids.*' => 'exists:albums,id',
            'privacy_level' => 'required_if:action,privacy|in:public,unlisted,private'
        ]);

        $albumIds = $request->album_ids;
        $currentUser = auth()->user();
        
        // Only allow bulk operations on user's own albums
        $albums = Album::whereIn('id', $albumIds)
                      ->where('owner_id', $currentUser->id)
                      ->get();

        if ($albums->isEmpty()) {
            return back()->withErrors(['albums' => 'No valid albums selected.']);
        }

        $count = $albums->count();

        switch ($request->action) {
            case 'delete':
                foreach ($albums as $album) {
                    $this->authorize('delete', $album);
                    $album->images()->update(['album_id' => null]);
                    $album->delete();
                }
                $message = "{$count} albums deleted successfully.";
                break;

            case 'publish':
                $albums->each(function ($album) {
                    $this->authorize('update', $album);
                    $album->update([
                        'is_published' => true,
                        'published_at' => now(),
                    ]);
                });
                $message = "{$count} albums published successfully.";
                break;

            case 'unpublish':
                $albums->each(function ($album) {
                    $this->authorize('update', $album);
                    $album->update([
                        'is_published' => false,
                        'published_at' => null,
                    ]);
                });
                $message = "{$count} albums unpublished successfully.";
                break;

            case 'privacy':
                $albums->each(function ($album) use ($request) {
                    $this->authorize('update', $album);
                    $album->update([
                        'privacy' => $request->privacy_level,
                        'is_published' => $request->privacy_level !== 'private',
                    ]);
                });
                $message = "{$count} albums privacy updated successfully.";
                break;
        }

        return back()->with('success', $message);
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

        $query = $album->images()->with(['owner']);
        
        // For public album viewing, only show published images
        // For album owners, show all images
        if (!auth()->check() || auth()->id() !== $album->owner_id) {
            $query->whereIn('privacy', ['public', 'unlisted'])
                  ->where('is_published', true);
        }

        // Search within album
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('caption', 'like', '%' . $request->search . '%');
            });
        }

        // Sort options
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        
        switch ($sort) {
            case 'title':
                $query->orderBy('title', $direction);
                break;
            case 'size':
                $query->orderBy('file_size', $direction);
                break;
            default:
                $query->orderBy('created_at', $direction);
                break;
        }

        $images = $query->paginate(24)->withQueryString();

        return Inertia::render('Albums/Show', [
            'album' => [
                'id' => $album->id,
                'title' => $album->title,
                'slug' => $album->slug,
                'description' => $album->description,
                'privacy' => $album->privacy,
                'is_published' => $album->is_published,
                'images_count' => $album->images_count ?? 0,
                'created_at' => $album->created_at,
                'updated_at' => $album->updated_at,
                'owner' => $album->owner,
                'cover_image' => $album->coverImage,
            ],
            'images' => $images,
            'filters' => $request->only(['search', 'sort', 'direction']),
            'can' => [
                'update' => auth()->user()?->can('update', $album) ?? false,
                'delete' => auth()->user()?->can('delete', $album) ?? false,
                'manageImages' => auth()->user()?->can('update', $album) ?? false,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified album.
     */
    public function edit(Album $album)
    {
        $this->authorize('update', $album);

        $album->load(['coverImage']);

        return Inertia::render('Albums/Edit', [
            'album' => [
                'id' => $album->id,
                'title' => $album->title,
                'description' => $album->description,
                'privacy' => $album->privacy,
                'is_published' => $album->is_published,
                'cover_image_id' => $album->cover_image_id,
                'images_count' => $album->images_count ?? 0,
                'created_at' => $album->created_at,
                'updated_at' => $album->updated_at,
            ],
            'images' => $album->images()
                ->select('id', 'title', 'storage_path', 'original_filename')
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
                return back()->withErrors(['cover_image_id' => 'Selected cover image must belong to this album.']);
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
     * Show form to add images to album.
     */
    public function addImagesForm(Album $album)
    {
        $this->authorize('update', $album);

        // Get user's images that are not in this album
        $availableImages = auth()->user()->images()
            ->whereNull('album_id')
            ->orWhere('album_id', '!=', $album->id)
            ->select('id', 'title', 'storage_path', 'original_filename', 'album_id')
            ->get();

        return Inertia::render('Albums/AddImages', [
            'album' => [
                'id' => $album->id,
                'title' => $album->title,
                'slug' => $album->slug,
            ],
            'availableImages' => $availableImages,
        ]);
    }

    /**
     * Add images to album.
     */
    public function addImages(Request $request, Album $album)
    {
        $this->authorize('update', $album);

        $request->validate([
            'image_ids' => 'required|array|min:1',
            'image_ids.*' => 'exists:images,id',
        ]);

        // Verify user owns the images
        $userImageIds = auth()->user()->images()->pluck('id')->toArray();
        $invalidIds = array_diff($request->image_ids, $userImageIds);
        
        if (!empty($invalidIds)) {
            return back()->withErrors(['image_ids' => 'You can only add your own images to the album.']);
        }

        Image::whereIn('id', $request->image_ids)
            ->update(['album_id' => $album->id]);

        // Auto-assign cover image if none exists
        if (!$album->cover_image_id) {
            $album->update(['cover_image_id' => $request->image_ids[0]]);
        }

        $count = count($request->image_ids);
        return redirect()->route('albums.show', $album)
            ->with('success', "{$count} images added to album successfully.");
    }

    /**
     * Remove images from album.
     */
    public function removeImages(Request $request, Album $album)
    {
        $this->authorize('update', $album);

        $request->validate([
            'image_ids' => 'required|array|min:1',
            'image_ids.*' => 'exists:images,id',
        ]);

        Image::whereIn('id', $request->image_ids)
            ->where('album_id', $album->id)
            ->update(['album_id' => null]);

        $count = count($request->image_ids);
        return back()->with('success', "{$count} images removed from album successfully.");
    }

/**
 * Display user's own albums with all statuses
 */
public function myAlbums(Request $request)
{
    $user = auth()->user();
    
    $query = Album::with(['owner', 'coverImage'])
        ->withCount('images')
        ->where('owner_id', $user->id);
    
    // Search
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%')
              ->orWhere('description', 'like', '%' . $request->search . '%');
        });
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
        case 'images_count':
            $query->orderBy('images_count', $direction);
            break;
        default:
            $query->orderBy('updated_at', $direction);
    }
    
    $albums = $query->paginate(12)->withQueryString();
    
    return Inertia::render('Albums/Index', [
        'albums' => $albums,
        'filters' => $request->only(['search', 'privacy', 'published', 'sort', 'direction']),
        'canCreate' => $user->hasRole('admin') || $user->hasRole('editor'),
        'isMyAlbums' => true,
    ]);
}


}
