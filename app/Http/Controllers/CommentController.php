<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Image;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Image $image)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        if (!$image->allow_comments) {
            return back()->withErrors(['comment' => 'Comments are not allowed on this image.']);
        }

        Comment::create([
            'user_id' => auth()->id(),
            'image_id' => $image->id,
            'body' => $request->body,
            'status' => 'approved', // Auto-approve for now
            'user_ip' => $request->ip(),
        ]);

        // Increment comment count
        $image->increment('comments_count');

        return back()->with('success', 'Comment posted successfully!');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        
        $comment->image->decrement('comments_count');
        $comment->delete();

        return back()->with('success', 'Comment deleted successfully!');
    }
}
