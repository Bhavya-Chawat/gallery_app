<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Collection;
use App\Models\Image;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CollectionController extends Controller
{
    /**
     * Display a listing of collections.
     */
    public function index(Request $request)
    {
        $query = Collection::with(['curator', 'coverImage'])
            ->withCount('items');

        // Only show visible collections to non-curators
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

        // Filter by curator
        if ($request->filled('curator') && auth()->check()) {
            if ($request->curator === 'mine') {
                $query->where('curator_id', auth()->id());
            } else {
                $query->where('curator_id', $request->curator);
            }
        }

        $collections = $query->orderBy('updated_at', 'desc')
            ->paginate(12);

        return Inertia::render('Collections/Index', [
            'collections' => $collections,
            'filters' => $request->only(['search', 'curator']),
            'canCreate' => auth()->check() && auth()->user()->can('create', Collection::class),
        ]);
    }

    /**
     * Show the form for creating a new collection.
     */
    public function create()
    {
        $this->authorize('create', Collection::class);

        return Inertia::render('Collections/Create');
    }

    /**
     * Store a newly created collection in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Collection::class);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'privacy' => 'required|in:public,unlisted,private',
        ]);

        $collection = Collection::create([
            'curator_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'privacy' => $request->privacy,
            'is_published' => $request->privacy !== 'private',
            'published_at' => $request->privacy !== 'private' ? now() : null,
        ]);

        return redirect()->route('collections.show', $collection)
            ->with('success', 'Collection created successfully.');
    }

    /**
     * Display the specified collection.
     */
    public function show(Collection $collection)
    {
        $this->authorize('view', $collection);

        $collection->load([
            'curator',
            'items.collectable',
            'coverImage'
        ]);

        // Record view
        if ($collection->isVisible()) {
            $collection->viewCounts()->updateOrCreate(
                ['date' => now()->toDateString()],
                ['count' => \DB::raw('count + 1')]
            );
        }

        return Inertia::render('Collections/Show', [
            'collection' => [
                'id' => $collection->id,
                'title' => $collection->title,
                'slug' => $collection->slug,
                'description' => $collection->description,
                'privacy' => $collection->privacy,
                'is_published' => $collection->is_published,
                'items_count' => $collection->items_count,
                'created_at' => $collection->created_at,
                'updated_at' => $collection->updated_at,
                'curator' => $collection->curator,
                'cover_image' => $collection->coverImage,
                'items' => $collection->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'description' => $item->description,
                        'sort_order' => $item->sort_order,
                        'added_at' => $item->added_at,
                        'collectable_type' => $item->collectable_type,
                        'collectable' => $item->collectable,
                    ];
                }),
            ],
            'can' => [
                'update' => auth()->user()?->can('update', $collection) ?? false,
                'delete' => auth()->user()?->can('delete', $collection) ?? false,
                'manageItems' => auth()->user()?->can('manageItems', $collection) ?? false,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified collection.
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
                'cover_image_id' => $collection->cover_image_id,
            ],
        ]);
    }

    /**
     * Update the specified collection in storage.
     */
    public function update(Request $request, Collection $collection)
    {
        $this->authorize('update', $collection);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'privacy' => 'required|in:public,unlisted,private',
            'cover_image_id' => 'nullable|exists:images,id',
        ]);

        $collection->update([
            'title' => $request->title,
            'description' => $request->description,
            'privacy' => $request->privacy,
            'cover_image_id' => $request->cover_image_id,
            'is_published' => $request->privacy !== 'private',
            'published_at' => $request->privacy !== 'private' && !$collection->is_published ? now() : $collection->published_at,
        ]);

        return redirect()->route('collections.show', $collection)
            ->with('success', 'Collection updated successfully.');
    }

    /**
     * Remove the specified collection from storage.
     */
    public function destroy(Collection $collection)
    {
        $this->authorize('delete', $collection);

        $collection->delete();

        return redirect()->route('collections.index')
            ->with('success', 'Collection deleted successfully.');
    }

    /**
     * Add item to collection.
     */
    public function addItem(Request $request, Collection $collection)
    {
        $this->authorize('manageItems', $collection);

        $request->validate([
            'item_type' => 'required|in:album,image',
            'item_id' => 'required|string',
            'description' => 'nullable|string|max:500',
        ]);

        $modelClass = $request->item_type === 'album' ? Album::class : Image::class;
        $item = $modelClass::findOrFail($request->item_id);

        // Check if item is accessible
        if (!$item->isVisible() && $item->owner_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return redirect()->back()
                ->withErrors(['item_id' => 'Selected item is not accessible.']);
        }

        // Check if item already exists in collection
        $exists = $collection->items()
            ->where('collectable_type', $modelClass)
            ->where('collectable_id', $item->id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['item_id' => 'Item is already in this collection.']);
        }

        $collection->addItem($item, $request->description);

        return redirect()->back()
            ->with('success', ucfirst($request->item_type) . ' added to collection successfully.');
    }

    /**
     * Remove item from collection.
     */
    public function removeItem(Request $request, Collection $collection)
    {
        $this->authorize('manageItems', $collection);

        $request->validate([
            'item_id' => 'required|exists:collection_items,id',
        ]);

        $item = $collection->items()->findOrFail($request->item_id);
        $item->delete();

        $collection->decrement('items_count');

        return redirect()->back()
            ->with('success', 'Item removed from collection successfully.');
    }

    /**
     * Reorder items in collection.
     */
    public function reorderItems(Request $request, Collection $collection)
    {
        $this->authorize('manageItems', $collection);

        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:collection_items,id',
        ]);

        // Verify all items belong to this collection
        $collectionItemIds = $collection->items()->pluck('id')->toArray();
        $invalidIds = array_diff($request->item_ids, $collectionItemIds);
        
        if (!empty($invalidIds)) {
            return redirect()->back()
                ->withErrors(['item_ids' => 'Some items do not belong to this collection.']);
        }

        $collection->reorderItems($request->item_ids);

        return redirect()->back()
            ->with('success', 'Collection items reordered successfully.');
    }

    /**
     * Publish/unpublish collection.
     */
    public function togglePublish(Collection $collection)
    {
        $this->authorize('update', $collection);

        if ($collection->is_published) {
            $collection->unpublish();
            $message = 'Collection unpublished successfully.';
        } else {
            $collection->publish();
            $message = 'Collection published successfully.';
        }

        return redirect()->back()
            ->with('success', $message);
    }
}
