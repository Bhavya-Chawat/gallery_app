<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Image;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Image $image)
    {
        // Ensure user is authenticated
        if (!auth()->check()) {
            return back()->withErrors(['comment' => 'You must be logged in to comment.']);
        }

        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        if (!$image->allow_comments) {
            return back()->withErrors(['comment' => 'Comments are not allowed on this image.']);
        }

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'image_id' => $image->id,
            'body' => $request->body,
            'status' => 'approved', // Auto-approve for now
            'user_ip' => $request->ip(),
        ]);

        // Load the user relationship for immediate display
        $comment->load('user');

        // Increment comment count
        $image->increment('comments_count');

        // Pass the new comment back to the frontend
        return back()->with([
            'success' => 'Comment posted successfully!',
            'newComment' => [
                'id' => $comment->id,
                'body' => $comment->body,
                'created_at' => $comment->created_at,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                ],
                'likes_count' => 0,
                'user_has_liked' => false,
            ]
        ]);
    }

    public function destroy(Comment $comment)
    {
        // Check if user can delete the comment
        if (!auth()->check() || (auth()->id() !== $comment->user_id && !auth()->user()->hasRole(['admin', 'moderator']))) {
            return back()->withErrors(['comment' => 'You are not authorized to delete this comment.']);
        }
        
        // Decrement comment count
        if ($comment->image) {
            $comment->image->decrement('comments_count');
        }
        
        $comment->delete();

        return back()->with('success', 'Comment deleted successfully!');
    }

    /**
     * Toggle like on a comment
     */
    public function toggleLike(Request $request, Comment $comment)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $user = auth()->user();
        $existingLike = $comment->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            $existingLike->delete();
            $comment->decrement('likes_count');
            $liked = false;
        } else {
            $comment->likes()->create(['user_id' => $user->id]);
            $comment->increment('likes_count');
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $comment->fresh()->likes_count,
        ]);
    }

    /**
     * Load more comments for pagination
     */
    public function loadMore(Request $request, Image $image)
    {
        $page = $request->get('page', 1);
        $perPage = 10;

        $comments = $image->comments()
            ->with('user')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json([
            'comments' => $comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'body' => $comment->body,
                    'created_at' => $comment->created_at,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                    ],
                    'likes_count' => $comment->likes_count ?? 0,
                    'user_has_liked' => auth()->check() ? $comment->likes()->where('user_id', auth()->id())->exists() : false,
                ];
            }),
            'hasMore' => $comments->count() === $perPage,
        ]);
    }
}
