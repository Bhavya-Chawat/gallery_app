<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\AuditLog;
use App\Models\Comment;
use App\Models\Image;
use App\Models\User;
use App\Models\ViewCount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SystemController extends Controller
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
     * Show system overview dashboard.
     */
    public function index()
    {
        // System overview stats
        $stats = [
            'users' => [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'this_month' => User::whereMonth('created_at', now()->month)->count(),
            ],
            'content' => [
                'images' => Image::count(),
                'published_images' => Image::where('is_published', true)->count(),
                'albums' => Album::count(),
                'published_albums' => Album::where('is_published', true)->count(),
            ],
            'engagement' => [
                'total_views' => $this->safeSum(ViewCount::class, 'count'),
                'this_week_views' => $this->getThisWeekViews(),
                'total_comments' => Comment::count(),
                'pending_comments' => Comment::where('status', 'pending')->count(),
            ],
            'storage' => [
                'total_size' => Image::sum('size_bytes') ?: 0,
                'average_size' => Image::avg('size_bytes') ?: 0,
                'total_files' => Image::count(),
            ],
        ];

        // System health
        $health = [
            'database' => $this->checkDatabaseHealth(),
            'storage' => $this->checkStorageHealth(),
            'queue' => $this->checkQueueHealth(),
            'cache' => $this->checkCacheHealth(),
        ];

        // Storage stats
        $storage = $this->getStorageStats();

        // Queue stats  
        $queue = $this->getQueueStats();

        // Recent logs with safety check
        $recentLogs = $this->getRecentAuditLogs();

        // System info
        $systemInfo = [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'environment' => app()->environment(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'health' => $health,
            'storage' => $storage,
            'queue' => $queue,
            'recentLogs' => $recentLogs,
            'systemInfo' => $systemInfo,
        ]);
    }

    /**
     * Show detailed analytics.
     */
    public function analytics(Request $request)
    {
        $dateRange = $request->get('range', '30');
        $startDate = now()->subDays($dateRange);

        $analytics = [
            'uploads' => [
                'total' => Image::count(),
                'this_period' => Image::where('created_at', '>=', $startDate)->count(),
                'by_day' => $this->getUploadAnalytics($startDate),
            ],
            'users' => [
                'total' => User::count(),
                'new_users' => User::where('created_at', '>=', $startDate)->count(),
                'active_users' => User::where('is_active', true)->count(),
            ],
            'storage' => [
                'total_size' => Image::sum('size_bytes') ?: 0,
                'total_files' => Image::count(),
                'average_size' => Image::avg('size_bytes') ?: 0,
            ],
            'engagement' => [
                'total_views' => $this->safeSum(ViewCount::class, 'count'),
                'total_comments' => Comment::count(),
                'pending_comments' => Comment::where('status', 'pending')->count(),
            ],
            'views' => $this->getViewAnalytics($startDate),
            'topImages' => $this->getTopImages(),
            'topUsers' => $this->getTopUsers(),
            'dateRange' => $dateRange,
        ];

        return Inertia::render('Admin/System/Analytics', [
            'analytics' => $analytics,
        ]);
    }

    /**
     * Show system settings.
     */
    public function settings()
    {
        return Inertia::render('Admin/System/Settings', [
            'settings' => $this->getCurrentSettings(),
        ]);
    }

    /**
     * Update system settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'max_upload_size' => 'required|integer|min:1',
            'allowed_mimes' => 'required|string',
            'enable_registration' => 'boolean',
            'enable_comments' => 'boolean',
            'comment_moderation' => 'boolean',
        ]);

        // Update settings in cache
        foreach ($request->only([
            'max_upload_size', 'allowed_mimes', 'enable_registration',
            'enable_comments', 'comment_moderation'
        ]) as $key => $value) {
            Cache::forever("setting.{$key}", $value);
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Clear application caches.
     */
    public function clearCache(Request $request)
    {
        $cacheType = $request->get('type', 'all');

        try {
            switch ($cacheType) {
                case 'config':
                    Artisan::call('config:clear');
                    break;
                case 'route':
                    Artisan::call('route:clear');
                    break;
                case 'view':
                    Artisan::call('view:clear');
                    break;
                case 'application':
                    Cache::flush();
                    break;
                case 'all':
                default:
                    Artisan::call('optimize:clear');
                    Cache::flush();
                    break;
            }

            return redirect()->back()->with('success', ucfirst($cacheType) . ' cache cleared successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    // Private helper methods

    /**
     * Safe sum method to handle potential ViewCount model issues
     */
    private function safeSum($model, $column)
    {
        try {
            if (class_exists($model)) {
                return $model::sum($column) ?: 0;
            }
        } catch (\Exception $e) {
            // Log error if needed
        }
        return 0;
    }

    /**
     * Get this week's views safely
     */
    private function getThisWeekViews()
    {
        try {
            if (class_exists(ViewCount::class)) {
                return ViewCount::whereBetween('date', [
                    now()->startOfWeek()->format('Y-m-d'),
                    now()->endOfWeek()->format('Y-m-d')
                ])->sum('count') ?: 0;
            }
        } catch (\Exception $e) {
            // Log error if needed
        }
        return 0;
    }

    /**
     * Check database health - FIXED FOR POSTGRESQL
     */
    private function checkDatabaseHealth(): array
    {
        try {
            DB::connection()->getPdo();
            // PostgreSQL compatible query
            $tableCount = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = current_schema()")[0]->count;
            return [
                'status' => 'healthy',
                'message' => "Database operational ({$tableCount} tables)",
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check storage health - FIXED FOR MINIO
     */
    private function checkStorageHealth(): array
    {
        try {
            // Use minio disk instead of s3
            $disk = Storage::disk('minio');
            $disk->exists('health-check.txt') ?: $disk->put('health-check.txt', 'OK');
            return [
                'status' => 'healthy',
                'message' => 'Storage system operational',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Storage check failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check queue health
     */
    private function checkQueueHealth(): array
    {
        try {
            $size = Queue::size();
            return [
                'status' => 'healthy',
                'message' => "Queue operational ({$size} jobs pending)",
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'warning',
                'message' => 'Queue status unknown: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check cache health
     */
    private function checkCacheHealth(): array
    {
        try {
            Cache::put('health_check', 'ok', 60);
            $test = Cache::get('health_check');
            return [
                'status' => $test === 'ok' ? 'healthy' : 'warning',
                'message' => $test === 'ok' ? 'Cache operational' : 'Cache may have issues',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Cache check failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get storage statistics - FIXED POSTGRESQL COMPATIBILITY
     */
    private function getStorageStats(): array
    {
        try {
            return [
                'total_size' => Image::sum('size_bytes') ?: 0,
                'by_mime_type' => Image::select('mime_type', DB::raw('SUM(size_bytes) as total_size'), DB::raw('COUNT(*) as count'))
                    ->groupBy('mime_type')
                    ->get(),
                'by_month' => Image::select(
                        DB::raw('EXTRACT(YEAR FROM created_at) as year'), 
                        DB::raw('EXTRACT(MONTH FROM created_at) as month'), 
                        DB::raw('SUM(size_bytes) as total_size')
                    )
                    ->groupBy(DB::raw('EXTRACT(YEAR FROM created_at)'), DB::raw('EXTRACT(MONTH FROM created_at)'))
                    ->orderBy(DB::raw('EXTRACT(YEAR FROM created_at)'), 'desc')
                    ->orderBy(DB::raw('EXTRACT(MONTH FROM created_at)'), 'desc')
                    ->take(12)
                    ->get(),
            ];
        } catch (\Exception $e) {
            return [
                'total_size' => 0,
                'by_mime_type' => collect(),
                'by_month' => collect(),
            ];
        }
    }

    /**
     * Get queue statistics
     */
    private function getQueueStats(): array
    {
        try {
            return [
                'pending' => Queue::size(),
                'failed' => DB::table('failed_jobs')->count(),
            ];
        } catch (\Exception $e) {
            return [
                'pending' => 0,
                'failed' => 0,
            ];
        }
    }

    /**
     * Get recent audit logs safely
     */
    private function getRecentAuditLogs(): array
    {
        try {
            if (class_exists(\App\Models\AuditLog::class)) {
                return AuditLog::with('user')
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get()
                    ->toArray();
            }
        } catch (\Exception $e) {
            // Log error if needed
        }
        
        return [];
    }

    /**
     * Get current system settings
     */
    private function getCurrentSettings(): array
    {
        return [
            'max_upload_size' => Cache::get('setting.max_upload_size', 50),
            'allowed_mimes' => Cache::get('setting.allowed_mimes', 'jpg,jpeg,png,webp,avif'),
            'enable_registration' => Cache::get('setting.enable_registration', true),
            'enable_comments' => Cache::get('setting.enable_comments', true),
            'comment_moderation' => Cache::get('setting.comment_moderation', true),
        ];
    }

    // Analytics methods - ALL FIXED FOR POSTGRESQL

    /**
     * Get upload analytics
     */
    private function getUploadAnalytics($startDate)
    {
        try {
            return Image::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->where('created_at', '>=', $startDate)
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy(DB::raw('DATE(created_at)'))
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get view analytics
     */
    private function getViewAnalytics($startDate)
    {
        try {
            if (class_exists(ViewCount::class)) {
                return ViewCount::select('date', DB::raw('SUM(count) as total'))
                    ->where('date', '>=', $startDate->toDateString())
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
            }
        } catch (\Exception $e) {
            // Log error if needed
        }
        
        return collect();
    }

    /**
     * Get user analytics
     */
    private function getUserAnalytics($startDate)
    {
        try {
            return User::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->where('created_at', '>=', $startDate)
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy(DB::raw('DATE(created_at)'))
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get storage analytics
     */
    private function getStorageAnalytics($startDate)
    {
        try {
            return Image::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(size_bytes) as total_size'))
                ->where('created_at', '>=', $startDate)
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy(DB::raw('DATE(created_at)'))
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get top images by views
     */
    private function getTopImages()
    {
        try {
            return Image::select('id', 'title', 'views_count', 'likes_count')
                ->orderBy('views_count', 'desc')
                ->take(10)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get top users by content
     */
    private function getTopUsers()
    {
        try {
            return User::withCount(['images', 'albums'])
                ->orderBy('images_count', 'desc')
                ->take(10)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    // Maintenance methods - FIXED FOR POSTGRESQL

    /**
     * Clean up temporary files
     */
    private function cleanupTempFiles()
    {
        $tempPath = storage_path('app/temp');
        if (is_dir($tempPath)) {
            $files = glob($tempPath . '/*');
            $cutoff = time() - (24 * 60 * 60);
            
            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < $cutoff) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Recalculate storage usage for all users
     */
    private function recalculateStorageUsage()
    {
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                $actualUsage = $user->images()->sum('size_bytes');
                $user->update(['storage_used_bytes' => $actualUsage]);
            }
        });
    }

    /**
     * Clean up old logs
     */
    private function cleanupOldLogs()
    {
        try {
            if (class_exists(\App\Models\AuditLog::class)) {
                AuditLog::where('created_at', '<', now()->subMonths(6))->delete();
            }
            
            if (class_exists(ViewCount::class)) {
                ViewCount::where('date', '<', now()->subYear())->delete();
            }
        } catch (\Exception $e) {
            // Log error if needed
        }
    }

    /**
     * Optimize database - FIXED FOR POSTGRESQL
     */
    private function optimizeDatabase()
    {
        try {
            // PostgreSQL optimization commands
            DB::statement('VACUUM ANALYZE images');
            DB::statement('VACUUM ANALYZE albums');
            DB::statement('VACUUM ANALYZE users');
            DB::statement('VACUUM ANALYZE comments');
        } catch (\Exception $e) {
            // Log error if needed - PostgreSQL may not have permissions for VACUUM
        }
    }

    /**
     * Export users data
     */
    private function exportUsers()
    {
        try {
            $users = User::with('roles')->get();
            // TODO: Implement actual CSV/Excel export
            return response()->json([
                'message' => 'User export started',
                'count' => $users->count()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export images data
     */
    private function exportImages()
    {
        try {
            $images = Image::with(['owner', 'album'])->get();
            // TODO: Implement actual CSV/Excel export
            return response()->json([
                'message' => 'Image export started',
                'count' => $images->count()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export analytics data
     */
    private function exportAnalytics()
    {
        try {
            // TODO: Implement actual analytics export
            return response()->json(['message' => 'Analytics export started']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }
}
