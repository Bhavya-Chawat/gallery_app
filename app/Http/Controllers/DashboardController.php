<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Comment;
use App\Models\Image;
use App\Models\User;
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
            'storageUsed' => $user->storage_used_bytes ?? 0,
            'storageQuota' => $user->storage_quota_bytes ?? 0,
            'storageUsagePercentage' => $user->getStorageUsagePercentage(),
        ];

        // Role-specific data
        if ($user->hasRole('admin')) {
            $data = array_merge($data, $this->getAdminDashboardData($user));
        } elseif ($user->hasRole('editor')) {
            $data = array_merge($data, $this->getEditorDashboardData($user));
        } else {
            $data = array_merge($data, $this->getVisitorDashboardData($user));
        }

        return Inertia::render('Dashboard', array_merge($data, [
            'upload_request_needed' => session('upload_request_needed', false),
        ]));
    }

    /**
     * Get admin-specific dashboard data.
     */
    private function getAdminDashboardData(User $user): array
    {
        return [
            'stats' => [
                // Personal stats for admin
                'myImages' => $user->images()->count(),
                'myAlbums' => $user->albums()->count(),
                'totalViews' => $user->images()->sum('views_count'),
                'totalLikes' => $user->images()->sum('likes_count'),
                
                // System-wide stats
                'totalUsers' => User::where('is_active', true)->count(),
                'totalImages' => Image::whereIn('privacy', ['public', 'unlisted'])->where('is_published', true)->count(),
                'totalAlbums' => Album::whereIn('privacy', ['public', 'unlisted'])->where('is_published', true)->count(),
                'pendingComments' => 0, // Simplified for now
            ],
            'recentImages' => $user->images()
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get(),
            'recentAlbums' => $user->albums()
                ->orderBy('updated_at', 'desc')
                ->take(6)
                ->withCount('images')
                ->get(),
            'recentActivities' => $this->getRecentActivities(),
            'systemStatus' => $this->getSystemStatus(),
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
                'myAlbums' => $user->albums()->count(),
                'totalViews' => $user->images()->sum('views_count'),
                'totalLikes' => $user->images()->sum('likes_count'),
            ],
            'recentImages' => $user->images()
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get(),
            'recentAlbums' => $user->albums()
                ->orderBy('updated_at', 'desc')
                ->take(6)
                ->withCount('images')
                ->get(),
            'recentActivities' => [],
            'systemStatus' => [],
        ];
    }

    /**
     * Get visitor-specific dashboard data.
     */
    private function getVisitorDashboardData(User $user): array
    {
        return [
            'stats' => [
                'myImages' => $user->images()->count(),
                'myAlbums' => $user->albums()->count(),
                'totalViews' => $user->images()->sum('views_count'),
                'totalLikes' => $user->images()->sum('likes_count'),
            ],
            'recentImages' => $user->images()
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get(),
            'recentAlbums' => $user->albums()
                ->orderBy('updated_at', 'desc')
                ->take(6)
                ->withCount('images')
                ->get(),
            'recentActivities' => [],
            'systemStatus' => [],
        ];
    }

    /**
     * Get recent system activities.
     */
    private function getRecentActivities(): array
    {
        $activities = collect();

        try {
            // Recent image uploads
            $recentImages = Image::with('owner')
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get()
                ->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'type' => 'image_uploaded',
                        'message' => "{$image->owner->name} uploaded \"{$image->title}\"",
                        'timestamp' => $image->created_at,
                    ];
                });

            // Recent albums
            $recentAlbums = Album::with('owner')
                ->orderBy('created_at', 'desc')
                ->take(2)
                ->get()
                ->map(function ($album) {
                    return [
                        'id' => $album->id,
                        'type' => 'album_created',
                        'message' => "{$album->owner->name} created album \"{$album->title}\"",
                        'timestamp' => $album->created_at,
                    ];
                });

            return $activities
                ->merge($recentImages)
                ->merge($recentAlbums)
                ->sortByDesc('timestamp')
                ->take(5)
                ->values()
                ->all();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get system status information.
     */
    private function getSystemStatus(): array
    {
        try {
            DB::connection()->getPdo();
            $dbStatus = 'healthy';
        } catch (\Exception $e) {
            $dbStatus = 'error';
        }

        return [
            'overall' => $dbStatus === 'healthy' ? 'Healthy' : 'Issues',
            'color' => $dbStatus === 'healthy' ? 'green' : 'red',
        ];
    }
}
