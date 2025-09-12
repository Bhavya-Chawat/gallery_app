<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Image;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CollectionController extends Controller
{
    /**
     * Display public collections
     */
    public function index(Request $request)
    {
        $query = Collection::with(['curator', 'coverImage'])
            ->withCount('images')
            ->whereIn('privacy', ['public', 'unlisted'])
            ->where('is_published', true);

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
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

        $collections = $query->paginate(12)->withQueryString();

        $currentUser = auth()->user();
        $canCreate = $currentUser && (
            $currentUser->hasRole('admin') || 
            $currentUser->hasRole('editor')
        );

        return Inertia::render('Collections/Index', [
            'collections' => $collections,
            'filters' => $request->only(['search', 'sort', 'direction']),
            'canCreate' => $canCreate,
            'isMyCollections' => false,
        ]);
    }

    /**
     * Display user's own collections
     */
    public function myCollections(Request $request)
    {
        $user = auth()->user();
        
        $query = Collection::with(['curator', 'coverImage'])
            ->withCount('images')
            ->where('curator_id', $user->id);

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

        $collections = $query->paginate(12)->withQueryString();

        return Inertia::render('Collections/Index', [
            'collections' => $collections,
            'filters' => $request->only(['search', 'privacy', 'sort', 'direction']),
            'canCreate' => true,
            'isMyCollections' => true,
        ]);
    }

    /**
     * Show the form for creating a new collection
     */
public function create()
{
    // Remove authorization check temporarily
    // $this->authorize('create', Collection::class);
    
    // Simple auth check instead
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    
    return Inertia::render('Collections/Create');
}
    /**
     * Store a newly created collection
     */
   public function store(Request $request)
{
    // Simple auth check instead of authorization
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'privacy' => 'required|in:public,unlisted,private',
        'type' => 'required|in:manual,smart',
        'initial_images' => 'nullable|array',
        'initial_images.*' => 'exists:images,id',
    ]);

    $collection = Collection::create([
        'curator_id' => auth()->id(),
        'title' => $request->title,
        'description' => $request->description,
        'privacy' => $request->privacy,
        'is_published' => $request->privacy !== 'private',
        'published_at' => $request->privacy !== 'private' ? now() : null,
    ]);

    // Add initial images if provided
    if ($request->initial_images && count($request->initial_images) > 0) {
        $imageData = [];
        foreach ($request->initial_images as $index => $imageId) {
            $imageData[$imageId] = [
                'added_at' => now(),
                'position' => $index + 1,
            ];
        }
        
        $collection->images()->attach($imageData);
        $collection->update([
            'images_count' => count($request->initial_images),
            'cover_image_id' => $request->initial_images[0] ?? null,
        ]);
    }

    return redirect()->route('collections.show', $collection)
        ->with('success', 'Collection created successfully.');
}


    /**
     * Display the specified collection
     */
    public function show(Collection $collection)
    {
        $this->authorize('view', $collection);

        // Load relationships
        $collection->load(['curator']);

        // Get collection images with pagination
        $images = $collection->images()
            ->with(['owner'])
            ->whereIn('privacy', ['public', 'unlisted'])
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->paginate(24)
            ->withQueryString();

        return Inertia::render('Collections/Show', [
            'collection' => [
                'id' => $collection->id,
                'title' => $collection->title,
                'description' => $collection->description,
                'privacy' => $collection->privacy,
                'is_published' => $collection->is_published,
                'images_count' => $collection->images_count ?? 0,
                'created_at' => $collection->created_at,
                'updated_at' => $collection->updated_at,
                'curator' => $collection->curator,
            ],
            'images' => $images,
            'can' => [
                'update' => auth()->user()?->can('update', $collection) ?? false,
                'delete' => auth()->user()?->can('delete', $collection) ?? false,
                'addImages' => auth()->user()?->can('update', $collection) ?? false,
            ],
        ]);
    }

    /**
     * Show the form for editing the collection
     */
    public function edit(Collection $collection)
    {
        $this->authorize('update', $collection);

        return Inertia::render('Collections/Edit', [
            'collection' => [
                'id' => $collection->id,
                'title' => $collection->title,
                'description' => $collection->description,
                'privacy' => $collection->privacy,
                'is_published' => $collection->is_published,
                'images_count' => $collection->images_count ?? 0,
                'created_at' => $collection->created_at,
                'updated_at' => $collection->updated_at,
            ],
        ]);
    }

    /**
     * Update the specified collection
     */
    public function update(Request $request, Collection $collection)
    {
        $this->authorize('update', $collection);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'privacy' => 'required|in:public,unlisted,private',
        ]);

        $collection->update([
            'title' => $request->title,
            'description' => $request->description,
            'privacy' => $request->privacy,
            'is_published' => $request->privacy !== 'private',
            'published_at' => $request->privacy !== 'private' && !$collection->is_published ? now() : $collection->published_at,
        ]);

        return redirect()->route('collections.show', $collection)
            ->with('success', 'Collection updated successfully.');
    }

    /**
     * Remove the specified collection
     */
    public function destroy(Collection $collection)
    {
        $this->authorize('delete', $collection);

        $collection->delete();

        return redirect()->route('collections.index')
            ->with('success', 'Collection deleted successfully.');
    }

    /**
     * Add an image to the collection
     */
    public function addImage(Request $request, Collection $collection)
    {
        $this->authorize('update', $collection);

        $request->validate([
            'image_id' => 'required|exists:images,id',
        ]);

        $image = Image::findOrFail($request->image_id);
        
        // Check if image is already in collection
        $exists = $collection->images()->where('images.id', $image->id)->exists();

        if (!$exists) {
            $collection->images()->attach($image->id, [
                'added_at' => now(),
                'position' => $collection->images()->count() + 1,
            ]);

            // Update collection's images count
            $collection->increment('images_count');

            // Set as cover image if collection doesn't have one
            if (!$collection->cover_image_id) {
                $collection->update(['cover_image_id' => $image->id]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Image added to collection successfully.'
        ]);
    }

    /**
     * Remove an image from the collection
     */
    public function removeImage(Request $request, Collection $collection)
    {
        $this->authorize('update', $collection);

        $request->validate([
            'image_id' => 'required|exists:images,id',
        ]);

        $image = Image::findOrFail($request->image_id);
        
        $removed = $collection->images()->detach($image->id);

        if ($removed) {
            // Update collection's images count
            $collection->decrement('images_count');

            // If this was the cover image, assign a new one
            if ($collection->cover_image_id == $image->id) {
                $newCover = $collection->images()->first();
                $collection->update(['cover_image_id' => $newCover?->id]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Image removed from collection successfully.'
        ]);
    }

    /**
     * Toggle collection publish status
     */
    public function togglePublish(Collection $collection)
    {
        $this->authorize('update', $collection);

        $collection->update([
            'is_published' => !$collection->is_published,
            'published_at' => !$collection->is_published ? now() : null,
        ]);

        $status = $collection->is_published ? 'published' : 'unpublished';
        
        return back()->with('success', "Collection {$status} successfully.");
    }
}
