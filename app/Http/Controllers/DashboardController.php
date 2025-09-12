<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Comment;
use App\Models\Image;
use App\Models\User;
use App\Models\ViewCount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the dashboard based on user role.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Common data for all users
        $data = [
            'user' => $user,
            'storageUsed' => $user->storage_used_bytes,
            'storageQuota' => $user->storage_quota_bytes,
            'storageUsagePercentage' => $user->getStorageUsagePercentage(),
        ];

        // Role-specific data
        if ($user->hasRole('admin')) {
            $data = array_merge($data, $this->getAdminDashboardData());
        } elseif ($user->hasRole('editor')) {
            $data = array_merge($data, $this->getEditorDashboardData($user));
        } else {
            $data = array_merge($data, $this->getVisitorDashboardData($user));
        }

        $data['auth'] = [
            'user' => $user,
            'roles' => method_exists($user, 'roles') ? $user->roles : [],
        ];
        $data['errors'] = session('errors') ?? [];
        return Inertia::render('Dashboard', [
          $data,
         'upload_request_needed' => session('upload_request_needed', false),]);
    }

    /**
     * Get admin-specific dashboard data.
     */
    private function getAdminDashboardData(): array
    {
        return [
            'stats' => [
                'totalUsers' => User::count(),
                'activeUsers' => User::where('is_active', true)->count(),
                'totalImages' => Image::count(),
                'publishedImages' => Image::where('is_published', true)->count(),
                'totalAlbums' => Album::count(),
                'publishedAlbums' => Album::where('is_published', true)->count(),
                'pendingComments' => Comment::where('status', 'pending')->count(),
                'totalStorage' => Image::sum('size_bytes'),
                'todayUploads' => Image::whereDate('created_at', today())->count(),
                'weeklyViews' => ViewCount::thisWeek()->sum('count'),
            ],
            'recentActivities' => $this->getRecentActivities(),
            'systemStatus' => $this->getSystemStatus(),
            'topImages' => Image::published()
                ->visible()
                ->orderBy('views_count', 'desc')
                ->take(5)
                ->with('owner')
                ->get(),
            'recentUsers' => User::active()
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->with('roles')
                ->get(),
        ];
    }

    /**
     * Get editor-specific dashboard data.
     */
    private function getEditorDashboardData(User $user): array
    {
        return [
            'stats' => [
                'myImages' => $user->images()->count(),
                'publishedImages' => $user->images()->where('is_published', true)->count(),
                'myAlbums' => $user->albums()->count(),
                'totalViews' => $user->images()->sum('views_count'),
                'totalLikes' => $user->images()->sum('likes_count'),
                'thisMonthUploads' => $user->images()
                    ->whereMonth('created_at', now()->month)
                    ->count(),
            ],
            'recentImages' => $user->images()
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->with(['album', 'tags'])
                ->get(),
            'recentAlbums' => $user->albums()
                ->orderBy('updated_at', 'desc')
                ->take(6)
                ->withCount('images')
                ->get(),
            'popularImages' => $user->images()
                ->published()
                ->orderBy('views_count', 'desc')
                ->take(5)
                ->get(),
            'recentComments' => Comment::whereHas('image', function ($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })
                ->approved()
                ->with(['user', 'image'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
        ];
    }

    /**
     * Get visitor-specific dashboard data.
     */
    private function getVisitorDashboardData(User $user): array
    {
        return [
            'stats' => [
                'likedImages' => $user->likes()->where('likeable_type', Image::class)->count(),
                'comments' => $user->comments()->approved()->count(),
                'favoriteAlbums' => 0, // Placeholder for future feature
            ],
            'recentlyLiked' => Image::whereHas('likes', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['owner', 'likes' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }])
                ->take(8)
                ->get(),
            'featuredImages' => Image::public()
                ->where('is_published', true)
                ->orderBy('views_count', 'desc')
                ->take(8)
                ->with('owner')
                ->get(),
            'recentComments' => $user->comments()
                ->approved()
                ->with('image')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
            'recommendedAlbums' => Album::public()
                ->where('is_published', true)
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->withCount('images')
                ->get(),
        ];
    }

    /**
     * Get recent system activities.
     */
    private function getRecentActivities(): array
    {
        $activities = collect();

        // Recent image uploads
        $recentImages = Image::with('owner')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($image) {
                return [
                    'type' => 'image_uploaded',
                    'message' => "{$image->owner->name} uploaded \"{$image->title}\"",
                    'timestamp' => $image->created_at,
                    'user' => $image->owner,
                ];
            });

        // Recent comments
        $recentComments = Comment::approved()
            ->with(['user', 'image'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($comment) {
                return [
                    'type' => 'comment_added',
                    'message' => "{$comment->user->name} commented on \"{$comment->image->title}\"",
                    'timestamp' => $comment->created_at,
                    'user' => $comment->user,
                ];
            });

        // Recent user registrations
        $recentUsers = User::active()
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'user_registered',
                    'message' => "{$user->name} joined the gallery",
                    'timestamp' => $user->created_at,
                    'user' => $user,
                ];
            });

        return $activities
            ->merge($recentImages)
            ->merge($recentComments)
            ->merge($recentUsers)
            ->sortByDesc('timestamp')
            ->take(10)
            ->values()
            ->all();
    }

    /**
     * Get system status information.
     */
    private function getSystemStatus(): array
    {
        return [
            'database' => $this->checkDatabaseStatus(),
            'storage' => $this->checkStorageStatus(),
            'queue' => $this->checkQueueStatus(),
            'cache' => $this->checkCacheStatus(),
        ];
    }

    private function checkDatabaseStatus(): array
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'healthy', 'message' => 'Database connection is working'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed'];
        }
    }

    private function checkStorageStatus(): array
    {
        try {
            $totalSize = Image::sum('size_bytes');
            $formattedSize = $this->formatBytes($totalSize);
            return [
                'status' => 'healthy',
                'message' => "Total storage used: {$formattedSize}"
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Storage check failed'];
        }
    }

    private function checkQueueStatus(): array
    {
        // This is a simplified check - in production you'd check actual queue metrics
        return [
            'status' => 'healthy',
            'message' => 'Queue system operational'
        ];
    }

    private function checkCacheStatus(): array
    {
        try {
            cache()->put('system_check', 'test', 60);
            $test = cache()->get('system_check');
            
            return [
                'status' => $test === 'test' ? 'healthy' : 'warning',
                'message' => $test === 'test' ? 'Cache is working' : 'Cache may have issues'
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Cache check failed'];
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unitIndex = floor(log($bytes, 1024));
        
        return round($bytes / pow(1024, $unitIndex), 2) . ' ' . $units[$unitIndex];
    }
}
