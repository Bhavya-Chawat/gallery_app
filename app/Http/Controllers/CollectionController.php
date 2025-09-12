<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Image;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $collections = Collection::with(['curator', 'coverImage'])
            ->visible()
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->curator === 'mine', function ($query) {
                $query->where('curator_id', auth()->id());
            })
            ->latest()
            ->paginate(12);

        return Inertia::render('Collections/Index', [
            'collections' => $collections,
            'filters' => $request->only(['search', 'curator']),
            'canCreate' => auth()->check(),
        ]);
    }

    public function show(Collection $collection)
    {
        $collection->load(['curator', 'items.collectable']);

        return Inertia::render('Collections/Show', [
            'collection' => $collection,
            'canEdit' => auth()->id() === $collection->curator_id,
        ]);
    }

    public function create()
    {
        return Inertia::render('Collections/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'privacy' => 'required|in:public,unlisted,private',
        ]);

        $collection = Collection::create([
            'curator_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'privacy' => $request->privacy,
            'is_published' => true,
            'published_at' => now(),
        ]);

        return redirect()->route('collections.show', $collection);
    }

    public function addImage(Request $request, Collection $collection)
    {
        $request->validate([
            'image_id' => 'required|exists:images,id',
        ]);

        $image = Image::findOrFail($request->image_id);
        
        // Check if already added
        $exists = $collection->items()
            ->where('collectable_type', Image::class)
            ->where('collectable_id', $image->id)
            ->exists();

        if (!$exists) {
            $collection->addItem($image);
        }

        return response()->json(['success' => true]);
    }

    public function removeImage(Request $request, Collection $collection)
    {
        $request->validate([
            'image_id' => 'required|exists:images,id',
        ]);

        $image = Image::findOrFail($request->image_id);
        $collection->removeItem($image);

        return response()->json(['success' => true]);
    }
}
