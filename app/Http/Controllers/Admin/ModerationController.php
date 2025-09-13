<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Comment;
use App\Models\Image;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ModerationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole('admin')) {
                abort(403, 'Admin access required.');
            }
            return $next($request);
        });
    }

    /**
     * Show moderation dashboard.
     */
    public function index()
    {
        try {
            // Pending content
            $pendingImages = Image::where('is_published', false)
                ->with(['owner:id,name'])
                ->latest()
                ->take(10)
                ->get();

            $pendingAlbums = Album::where('is_published', false)
                ->with(['owner:id,name'])
                ->latest()
                ->take(10)
                ->get();

            // Recent content for review
            $recentImages = Image::where('is_published', true)
                ->with(['owner:id,name'])
                ->latest()
                ->take(15)
                ->get();

            $recentAlbums = Album::where('is_published', true)
                ->with(['owner:id,name'])
                ->latest()
                ->take(15)
                ->get();

            $stats = [
                'pending_images' => Image::where('is_published', false)->count(),
                'pending_albums' => Album::where('is_published', false)->count(),
                'total_images' => Image::count(),
                'total_albums' => Album::count(),
            ];

            return Inertia::render('Admin/Moderation/Index', [
                'pendingImages' => $pendingImages,
                'pendingAlbums' => $pendingAlbums,
                'recentImages' => $recentImages,
                'recentAlbums' => $recentAlbums,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Admin/Moderation/Index', [
                'pendingImages' => collect(),
                'pendingAlbums' => collect(),
                'recentImages' => collect(),
                'recentAlbums' => collect(),
                'stats' => [
                    'pending_images' => 0,
                    'pending_albums' => 0,
                    'total_images' => 0,
                    'total_albums' => 0,
                ],
                'error' => 'Failed to load moderation data: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Show comments moderation queue.
     */
    public function comments(Request $request)
    {
        try {
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

            $comments = $query->orderBy('created_at', 'desc')->paginate(20);

            return Inertia::render('Admin/Moderation/Comments', [
                'comments' => $comments,
                'filters' => $request->only(['status', 'search']),
                'statuses' => [
                    'pending' => Comment::where('status', 'pending')->count(),
                    'approved' => Comment::where('status', 'approved')->count(),
                    'rejected' => Comment::where('status', 'rejected')->count(),
                    'spam' => Comment::where('status', 'spam')->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Admin/Moderation/Comments', [
                'comments' => collect(),
                'filters' => $request->only(['status', 'search']),
                'statuses' => [
                    'pending' => 0,
                    'approved' => 0,
                    'rejected' => 0,
                    'spam' => 0,
                ],
                'error' => 'Failed to load comments: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Approve a comment
     */
    public function approveComment(Comment $comment)
    {
        try {
            $comment->approve(auth()->user());
            return redirect()->back()->with('success', 'Comment approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to approve comment: ' . $e->getMessage());
        }
    }

    /**
     * Reject a comment
     */
    public function rejectComment(Comment $comment)
    {
        try {
            $comment->reject();
            return redirect()->back()->with('success', 'Comment rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reject comment: ' . $e->getMessage());
        }
    }

    /**
     * Mark comment as spam
     */
    public function markSpam(Comment $comment)
    {
        try {
            $comment->markAsSpam();
            return redirect()->back()->with('success', 'Comment marked as spam.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to mark comment as spam: ' . $e->getMessage());
        }
    }

    /**
     * Bulk moderate comments
     */
    public function bulkModerate(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,spam,delete',
            'comment_ids' => 'required|array|min:1',
            'comment_ids.*' => 'exists:comments,id',
        ]);

        try {
            $comments = Comment::whereIn('id', $request->comment_ids)->get();
            $count = 0;

            foreach ($comments as $comment) {
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

            return redirect()->back()->with('success', "{$count} comments {$request->action}d successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Bulk moderation failed: ' . $e->getMessage());
        }
    }

    /**
     * Approve an image
     */
    public function approveImage(Image $image)
    {
        try {
            $image->update([
                'is_published' => true,
                'published_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Image approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to approve image: ' . $e->getMessage());
        }
    }

    /**
     * Reject an image
     */
    public function rejectImage(Image $image)
    {
        try {
            $image->update([
                'is_published' => false,
                'published_at' => null,
            ]);

            return redirect()->back()->with('success', 'Image rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reject image: ' . $e->getMessage());
        }
    }

    /**
     * Approve an album
     */
    public function approveAlbum(Album $album)
    {
        try {
            $album->update([
                'is_published' => true,
                'published_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Album approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to approve album: ' . $e->getMessage());
        }
    }

    /**
     * Reject an album
     */
    public function rejectAlbum(Album $album)
    {
        try {
            $album->update([
                'is_published' => false,
                'published_at' => null,
            ]);

            return redirect()->back()->with('success', 'Album rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reject album: ' . $e->getMessage());
        }
    }

    /**
     * Bulk moderate images
     */
    public function bulkModerateImages(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'image_ids' => 'required|array|min:1',
            'image_ids.*' => 'exists:images,id',
        ]);

        try {
            $images = Image::whereIn('id', $request->image_ids)->get();
            $count = 0;

            foreach ($images as $image) {
                switch ($request->action) {
                    case 'approve':
                        $image->update([
                            'is_published' => true,
                            'published_at' => now(),
                        ]);
                        break;
                    case 'reject':
                        $image->update([
                            'is_published' => false,
                            'published_at' => null,
                        ]);
                        break;
                    case 'delete':
                        $image->delete();
                        break;
                }
                $count++;
            }

            return redirect()->back()->with('success', "{$count} images {$request->action}d successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Bulk image moderation failed: ' . $e->getMessage());
        }
    }

    /**
     * Bulk moderate albums
     */
    public function bulkModerateAlbums(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'album_ids' => 'required|array|min:1',
            'album_ids.*' => 'exists:albums,id',
        ]);

        try {
            $albums = Album::whereIn('id', $request->album_ids)->get();
            $count = 0;

            foreach ($albums as $album) {
                switch ($request->action) {
                    case 'approve':
                        $album->update([
                            'is_published' => true,
                            'published_at' => now(),
                        ]);
                        break;
                    case 'reject':
                        $album->update([
                            'is_published' => false,
                            'published_at' => null,
                        ]);
                        break;
                    case 'delete':
                        $album->delete();
                        break;
                }
                $count++;
            }

            return redirect()->back()->with('success', "{$count} albums {$request->action}d successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Bulk album moderation failed: ' . $e->getMessage());
        }
    }

    /**
     * Show content reports
     */
    public function reports(Request $request)
    {
        return Inertia::render('Admin/Moderation/Reports', [
            'reports' => collect(),
            'filters' => $request->only(['status', 'type']),
            'stats' => [
                'pending' => 0,
                'resolved' => 0,
                'dismissed' => 0,
            ],
        ]);
    }

    /**
     * Get moderation statistics
     */
    public function getModerationStats()
    {
        try {
            return response()->json([
                'comments' => [
                    'pending' => Comment::where('status', 'pending')->count(),
                    'approved' => Comment::where('status', 'approved')->count(),
                    'rejected' => Comment::where('status', 'rejected')->count(),
                    'spam' => Comment::where('status', 'spam')->count(),
                ],
                'content' => [
                    'pending_images' => Image::where('is_published', false)->count(),
                    'pending_albums' => Album::where('is_published', false)->count(),
                    'total_images' => Image::count(),
                    'total_albums' => Album::count(),
                ],
                'recent_activity' => $this->getRecentModerationActions(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load moderation stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent moderation actions - FIXED NULL COALESCING
     */
    private function getRecentModerationActions()
    {
        try {
            $recentComments = Comment::whereNotNull('approved_by')
                ->with(['approver', 'user', 'image'])
                ->orderBy('approved_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($comment) {
                    $approverName = $comment->approver ? $comment->approver->name : 'System';
                    $userName = $comment->user ? $comment->user->name : 'Unknown';
                    
                    return [
                        'id' => $comment->id,
                        'type' => 'comment',
                        'action' => 'approved',
                        'moderator' => $approverName,
                        'target' => "Comment by {$userName}",
                        'timestamp' => $comment->approved_at,
                    ];
                });

            $recentImages = Image::where('is_published', true)
                ->whereNotNull('published_at')
                ->latest('published_at')
                ->take(5)
                ->get()
                ->map(function ($image) {
                    $imageTitle = $image->title ? $image->title : 'Untitled Image';
                    
                    return [
                        'id' => $image->id,
                        'type' => 'image',
                        'action' => 'published',
                        'moderator' => 'Admin',
                        'target' => $imageTitle,
                        'timestamp' => $image->published_at,
                    ];
                });

            return $recentComments->merge($recentImages)
                ->sortByDesc('timestamp')
                ->take(10)
                ->values();

        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get pending content summary
     */
    public function getPendingSummary()
    {
        try {
            $pendingComments = Comment::where('status', 'pending')->count();
            $pendingImages = Image::where('is_published', false)->count();
            $pendingAlbums = Album::where('is_published', false)->count();
            
            return response()->json([
                'pending_comments' => $pendingComments,
                'pending_images' => $pendingImages,
                'pending_albums' => $pendingAlbums,
                'flagged_content' => 0, // Future implementation
                'total_pending' => $pendingComments + $pendingImages + $pendingAlbums,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load pending summary: ' . $e->getMessage()
            ], 500);
        }
    }
}
