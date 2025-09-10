<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Image;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ModerationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:moderate_comments']);
    }

    /**
     * Show moderation dashboard.
     */
    public function index()
    {
        return Inertia::render('Admin/Moderation/Index', [
            'stats' => [
                'pending_comments' => Comment::pending()->count(),
                'reported_images' => 0, // Placeholder for reporting feature
                'spam_comments' => Comment::where('status', 'spam')->count(),
                'rejected_comments' => Comment::where('status', 'rejected')->count(),
            ],
            'pendingComments' => Comment::pending()
                ->with(['user', 'image'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get(),
            'recentActions' => $this->getRecentModerationActions(),
        ]);
    }

    /**
     * Show comments moderation queue.
     */
    public function comments(Request $request)
    {
        $query = Comment::with(['user', 'image']);

        // Filter by status
        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('body', 'like', '%' . $request->search . '%');
        }

        $comments = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Moderation/Comments', [
            'comments' => $comments,
            'filters' => $request->only(['status', 'search']),
            'statuses' => [
                'pending' => Comment::pending()->count(),
                'approved' => Comment::where('status', 'approved')->count(),
                'rejected' => Comment::where('status', 'rejected')->count(),
                'spam' => Comment::where('status', 'spam')->count(),
            ],
        ]);
    }

    /**
     * Approve a comment.
     */
    public function approveComment(Comment $comment)
    {
        $this->authorize('approve', $comment);

        $comment->approve(auth()->user());

        return redirect()->back()
            ->with('success', 'Comment approved successfully.');
    }

    /**
     * Reject a comment.
     */
    public function rejectComment(Comment $comment)
    {
        $this->authorize('reject', $comment);

        $comment->reject();

        return redirect()->back()
            ->with('success', 'Comment rejected successfully.');
    }

    /**
     * Mark comment as spam.
     */
    public function markSpam(Comment $comment)
    {
        $this->authorize('moderate', $comment);

        $comment->markAsSpam();

        return redirect()->back()
            ->with('success', 'Comment marked as spam.');
    }

    /**
     * Bulk moderate comments.
     */
    public function bulkModerate(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,spam,delete',
            'comment_ids' => 'required|array|min:1',
            'comment_ids.*' => 'exists:comments,id',
        ]);

        $comments = Comment::whereIn('id', $request->comment_ids)->get();
        $count = 0;

        foreach ($comments as $comment) {
            if (!auth()->user()->can('moderate', $comment)) {
                continue;
            }

            switch ($request->action) {
                case 'approve':
                    $comment->approve(auth()->user());
                    break;
                case 'reject':
                    $comment->reject();
                    break;
                case 'spam':
                    $comment->markAsSpam();
                    break;
                case 'delete':
                    $comment->delete();
                    break;
            }
            $count++;
        }

        return redirect()->back()
            ->with('success', "{$count} comments {$request->action}d successfully.");
    }

    /**
     * Show reported content.
     */
    public function reports()
    {
        // Placeholder for future reporting feature
        return Inertia::render('Admin/Moderation/Reports', [
            'reports' => [],
        ]);
    }

    private function getRecentModerationActions()
    {
        return Comment::whereNotNull('approved_by')
            ->with(['approver', 'user', 'image'])
            ->orderBy('approved_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'action' => 'approved',
                    'moderator' => $comment->approver->name,
                    'target' => "Comment by {$comment->user->name}",
                    'timestamp' => $comment->approved_at,
                ];
            });
    }
}
