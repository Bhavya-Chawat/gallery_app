<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Image;
use Illuminate\Http\Request;

class CommentApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('throttle:comments,5')->only(['store']);
    }

    /**
     * Get comments for an image.
     */
    public function index(Request $request, Image $image)
    {
        $query = $image->comments()
            ->with(['user', 'replies.user'])
            ->whereNull('parent_id');

        // Only show approved comments to non-moderators
        if (!auth()->user()->hasPermission('moderate_comments')) {
            $query->approved();
        }

        $comments = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return CommentResource::collection($comments);
    }

    /**
     * Store a new comment.
     */
    public function store(Request $request, Image $image)
    {
        if (!$image->allow_comments) {
            return response()->json(['message' => 'Comments are disabled for this image'], 403);
        }

        $request->validate([
            'body' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        // Verify parent comment belongs to this image
        if ($request->parent_id) {
            $parentComment = Comment::find($request->parent_id);
            if (!$parentComment || $parentComment->image_id !== $image->id) {
                return response()->json(['message' => 'Invalid parent comment'], 422);
            }
        }

        $comment = Comment::create([
            'image_id' => $image->id,
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
            'body' => $request->body,
            'status' => config('gallery.comment_moderation', true) ? 'pending' : 'approved',
            'user_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Auto-approve if moderation is disabled
        if (!config('gallery.comment_moderation', true)) {
            $comment->approve();
        }

        return new CommentResource($comment->load('user'));
    }

    /**
     * Update a comment.
     */
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment->update(['body' => $request->body]);

        return new CommentResource($comment->load('user'));
    }

    /**
     * Delete a comment.
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
    /**
     * Toggle like on comment.
     */
    public function toggleLike(Comment $comment)
    {
        $user = auth()->user();
        
        $existingLike = $comment->likes()
            ->where('user_id', $user->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            $comment->likes()->create([
                'user_id' => $user->id,
                'user_ip' => request()->ip(),
            ]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $comment->fresh()->likes()->count(),
        ]);
    }
}
