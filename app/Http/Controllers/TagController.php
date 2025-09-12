<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Image;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::popular()->take(50)->get();
        return response()->json($tags);
    }

    public function attach(Request $request)
    {
        $request->validate([
            'image_id' => 'required|exists:images,id',
            'tags' => 'required|array',
            'tags.*' => 'string|max:50',
        ]);

        $image = Image::findOrFail($request->image_id);
        $tagIds = [];

        foreach ($request->tags as $tagName) {
            $tag = Tag::firstOrCreate(
                ['name' => trim($tagName)],
                ['usage_count' => 0]
            );
            $tagIds[] = $tag->id;
        }

        $image->tags()->sync($tagIds);
        
        // Update usage counts
        Tag::whereIn('id', $tagIds)->each(function ($tag) {
            $tag->updateUsageCount();
        });

        return response()->json(['success' => true]);
    }
}
