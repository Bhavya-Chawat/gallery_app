<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Image;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

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
        $canCreate = true;

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
        // Auth check
        if (!auth()->check()) {
            return redirect()->route('login');
        }

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
        return Inertia::render('Collections/Create');
    }

    /**
     * Store a newly created collection
     */
    public function store(Request $request)
    {
        $userId = auth()->id();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'privacy' => 'required|in:public,unlisted,private',
            'type' => 'nullable|in:manual,smart',
            'initial_images' => 'nullable|array',
            'initial_images.*' => 'exists:images,id',
        ]);

        // FIXED: Generate slug properly
        $slug = Str::slug($request->title);
        $originalSlug = $slug;
        $counter = 1;
        while (Collection::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $collection = Collection::create([
            'curator_id' => $userId,
            'title' => $request->title,
            'slug' => $slug,
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

        return redirect("/collections/{$collection->slug}")
            ->with('success', 'Collection created successfully!');
    }

    /**
     * Display the specified collection
     */
    public function show(Collection $collection)
    {
        // Simple public access check
        if ($collection->privacy === 'private' && (!auth()->check() || auth()->id() !== $collection->curator_id)) {
            abort(404);
        }

        // Load relationships
        $collection->load(['curator']);

        // FIXED: Get collection images with proper relationship
        $images = $collection->images()
            ->with(['owner'])
            ->whereIn('images.privacy', ['public', 'unlisted'])
            ->where('images.is_published', true)
            ->orderByPivot('created_at', 'desc')
            ->paginate(24)
            ->withQueryString();

        return Inertia::render('Collections/Show', [
            'collection' => [
                'id' => $collection->id,
                'slug' => $collection->slug,
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
                'update' => auth()->check() && (auth()->id() === $collection->curator_id),
                'delete' => auth()->check() && (auth()->id() === $collection->curator_id),
                'addImages' => auth()->check() && (auth()->id() === $collection->curator_id),
            ],
        ]);
    }

    /**
     * Show the form for editing the collection
     */
    public function edit(Collection $collection)
    {
        // Simple ownership check
        if (!auth()->check() || auth()->id() !== $collection->curator_id) {
            return redirect('/login');
        }

        return Inertia::render('Collections/Edit', [
            'collection' => [
                'id' => $collection->id,
                'slug' => $collection->slug,
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
        // Simple ownership check
        if (!auth()->check() || auth()->id() !== $collection->curator_id) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return redirect('/login');
        }

        // Validate input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'privacy' => 'required|in:public,unlisted,private',
        ]);

        // Update slug if title changed
        $updateData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'privacy' => $validated['privacy'],
            'is_published' => $validated['privacy'] !== 'private',
        ];

        // Update slug if title changed
        if ($collection->title !== $validated['title']) {
            $newSlug = Str::slug($validated['title']);
            
            // Ensure slug uniqueness
            $originalSlug = $newSlug;
            $counter = 1;
            while (Collection::where('slug', $newSlug)->where('id', '!=', $collection->id)->exists()) {
                $newSlug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            $updateData['slug'] = $newSlug;
        }

        // Update published_at timestamp
        if ($validated['privacy'] !== 'private' && !$collection->is_published) {
            $updateData['published_at'] = now();
        } elseif ($validated['privacy'] === 'private') {
            $updateData['published_at'] = null;
        }

        $collection->update($updateData);

        // Handle different response types
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Collection updated successfully.',
                'collection' => [
                    'slug' => $collection->slug,
                    'title' => $collection->title,
                ]
            ]);
        }

        // Use the updated slug for redirect
        $redirectSlug = $updateData['slug'] ?? $collection->slug;
        return redirect("/collections/{$redirectSlug}")
            ->with('success', 'Collection updated successfully.');
    }

    /**
     * Remove the specified collection
     */
    public function destroy(Collection $collection)
    {
        // Simple ownership check
        if (!auth()->check() || auth()->id() !== $collection->curator_id) {
            return redirect('/login');
        }

        $collectionTitle = $collection->title;
        $collection->delete();

        return redirect('/collections')
            ->with('success', "Collection '{$collectionTitle}' deleted successfully.");
    }

    /**
     * FIXED: Add an image to the collection
     */
    public function addImage(Request $request, Collection $collection)
    {
        // Simple ownership check
        if (!auth()->check() || auth()->id() !== $collection->curator_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'image_id' => 'required|exists:images,id',
        ]);

        $image = Image::findOrFail($request->image_id);
        
        // FIXED: Check if image is already in collection using pivot table
        $exists = $collection->images()->where('image_id', $image->id)->exists();

        if (!$exists) {
            // FIXED: Use proper pivot table structure
            $collection->images()->attach($image->id, [
                'added_at' => now(),
                'position' => $collection->images()->count() + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update collection's images count
            $collection->increment('images_count');

            // Set as cover image if collection doesn't have one
            if (!$collection->cover_image_id) {
                $collection->update(['cover_image_id' => $image->id]);
            }

            // Touch the collection to update timestamp
            $collection->touch();

            return response()->json([
                'success' => true,
                'message' => 'Image added to collection successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Image is already in this collection.'
        ], 409);
    }

    /**
     * FIXED: Remove an image from the collection
     */
    public function removeImage(Request $request, Collection $collection)
    {
        // Simple ownership check
        if (!auth()->check() || auth()->id() !== $collection->curator_id) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return redirect('/login');
        }

        $request->validate([
            'image_id' => 'required|exists:images,id',
        ]);

        $image = Image::findOrFail($request->image_id);
        
        // FIXED: Use proper pivot detach
        $removed = $collection->images()->detach($image->id);

        if ($removed) {
            // Update collection's images count
            $collection->decrement('images_count');

            // If this was the cover image, assign a new one
            if ($collection->cover_image_id == $image->id) {
                $newCover = $collection->images()->first();
                $collection->update(['cover_image_id' => $newCover?->id]);
            }

            // Touch the collection to update timestamp
            $collection->touch();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Image removed from collection successfully.'
                ]);
            }

            return redirect()->back()->with('success', 'Image removed from collection successfully.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Image was not found in this collection.'
            ], 404);
        }

        return redirect()->back()->withErrors(['error' => 'Image was not found in this collection.']);
    }

    /**
     * Toggle collection publish status
     */
    public function togglePublish(Collection $collection)
    {
        // Simple ownership check
        if (!auth()->check() || auth()->id() !== $collection->curator_id) {
            return redirect('/login');
        }

        $newStatus = !$collection->is_published;
        
        $collection->update([
            'is_published' => $newStatus,
            'published_at' => $newStatus ? now() : null,
        ]);

        $status = $newStatus ? 'published' : 'unpublished';
        
        return back()->with('success', "Collection {$status} successfully.");
    }
}
