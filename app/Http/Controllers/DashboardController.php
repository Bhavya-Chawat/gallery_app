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
        
        // Common data for all users - Fix user serialization
        $data = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->toArray(), // Convert to array
            ],
            'storageUsed' => (int) ($user->storage_used_bytes ?? 0),
            'storageQuota' => (int) ($user->storage_quota_bytes ?? 0),
            'storageUsagePercentage' => (float) $user->getStorageUsagePercentage(),
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
                'myImages' => (int) $user->images()->count(),
                'myAlbums' => (int) $user->albums()->count(),
                'totalViews' => (int) $user->images()->sum('views_count'),
                'totalLikes' => (int) $user->images()->sum('likes_count'),
                
                // System-wide stats
                'totalUsers' => (int) User::where('is_active', true)->count(),
                'totalImages' => (int) Image::whereIn('privacy', ['public', 'unlisted'])->where('is_published', true)->count(),
                'totalAlbums' => (int) Album::whereIn('privacy', ['public', 'unlisted'])->where('is_published', true)->count(),
                'pendingComments' => 0,
            ],
            'recentImages' => $this->getSerializedImages($user),
            'recentAlbums' => $this->getSerializedAlbums($user),
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
                'myImages' => (int) $user->images()->count(),
                'myAlbums' => (int) $user->albums()->count(),
                'totalViews' => (int) $user->images()->sum('views_count'),
                'totalLikes' => (int) $user->images()->sum('likes_count'),
            ],
            'recentImages' => $this->getSerializedImages($user),
            'recentAlbums' => $this->getSerializedAlbums($user),
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
                'myImages' => (int) $user->images()->count(),
                'myAlbums' => (int) $user->albums()->count(),
                'totalViews' => (int) $user->images()->sum('views_count'),
                'totalLikes' => (int) $user->images()->sum('likes_count'),
            ],
            'recentImages' => $this->getSerializedImages($user),
            'recentAlbums' => $this->getSerializedAlbums($user),
            'recentActivities' => [],
            'systemStatus' => [],
        ];
    }

    /**
     * Get properly serialized images.
     */
    private function getSerializedImages(User $user): array
    {
        return $user->images()
            ->select(['id', 'title', 'slug', 'storage_path', 'views_count', 'likes_count', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get()
            ->map(function ($image) {
                return [
                    'id' => $image->id,
                    'title' => $image->title,
                    'slug' => $image->slug,
                    'storage_path' => $image->storage_path,
                    'views_count' => (int) $image->views_count,
                    'likes_count' => (int) $image->likes_count,
                    'created_at' => $image->created_at->toISOString(),
                ];
            })
            ->toArray();
    }

    /**
     * Get properly serialized albums.
     */
    private function getSerializedAlbums(User $user): array
    {
        return $user->albums()
            ->select(['id', 'title', 'slug', 'description', 'updated_at'])
            ->withCount('images')
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get()
            ->map(function ($album) {
                return [
                    'id' => $album->id,
                    'title' => $album->title,
                    'slug' => $album->slug,
                    'description' => $album->description,
                    'images_count' => (int) $album->images_count,
                    'updated_at' => $album->updated_at->toISOString(),
                ];
            })
            ->toArray();
    }

    /**
     * Get recent system activities.
     */
    private function getRecentActivities(): array
    {
        try {
            // Recent image uploads
            $recentImages = Image::select(['id', 'title', 'created_at', 'user_id'])
                ->with(['owner:id,name']) // Only select needed fields
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get()
                ->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'type' => 'image_uploaded',
                        'message' => ($image->owner->name ?? 'Unknown') . " uploaded \"{$image->title}\"",
                        'timestamp' => $image->created_at->toISOString(),
                    ];
                });

            // Recent albums
            $recentAlbums = Album::select(['id', 'title', 'created_at', 'user_id'])
                ->with(['owner:id,name']) // Only select needed fields
                ->orderBy('created_at', 'desc')
                ->take(2)
                ->get()
                ->map(function ($album) {
                    return [
                        'id' => $album->id,
                        'type' => 'album_created',
                        'message' => ($album->owner->name ?? 'Unknown') . " created album \"{$album->title}\"",
                        'timestamp' => $album->created_at->toISOString(),
                    ];
                });

            return collect()
                ->merge($recentImages)
                ->merge($recentAlbums)
                ->sortByDesc('timestamp')
                ->take(5)
                ->values()
                ->toArray();

        } catch (\Exception $e) {
            \Log::error('Dashboard activities error: ' . $e->getMessage());
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
            \Log::error('Database connection error: ' . $e->getMessage());
        }

        return [
            'overall' => $dbStatus === 'healthy' ? 'Healthy' : 'Issues',
            'color' => $dbStatus === 'healthy' ? 'green' : 'red',
        ];
    }
}
