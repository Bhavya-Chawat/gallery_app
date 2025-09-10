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
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Show system overview dashboard.
     */
    public function index()
    {
        return Inertia::render('Admin/System/Index', [
            'stats' => $this->getSystemStats(),
            'health' => $this->getHealthChecks(),
            'storage' => $this->getStorageStats(),
            'queue' => $this->getQueueStats(),
            'recentLogs' => $this->getRecentAuditLogs(),
        ]);
    }

    /**
     * Show detailed analytics.
     */
    public function analytics(Request $request)
    {
        $dateRange = $request->get('range', '30'); // days
        $startDate = now()->subDays($dateRange);

        return Inertia::render('Admin/System/Analytics', [
            'uploads' => $this->getUploadAnalytics($startDate),
            'views' => $this->getViewAnalytics($startDate),
            'users' => $this->getUserAnalytics($startDate),
            'storage' => $this->getStorageAnalytics($startDate),
            'topImages' => $this->getTopImages(),
            'topUsers' => $this->getTopUsers(),
            'dateRange' => $dateRange,
        ]);
    }

    /**
     * Show system settings.
     */
    public function settings()
    {
        return Inertia::render('Admin/System/Settings', [
            'settings' => $this->getCurrentSettings(),
            'diskUsage' => $this->getDiskUsage(),
            'cacheStats' => $this->getCacheStats(),
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
            'default_privacy' => 'required|in:public,unlisted,private',
        ]);

        // Update settings in cache or database
        foreach ($request->only([
            'max_upload_size', 'allowed_mimes', 'enable_registration',
            'enable_comments', 'comment_moderation', 'default_privacy'
        ]) as $key => $value) {
            Cache::forever("setting.{$key}", $value);
        }

        return redirect()->back()
            ->with('success', 'Settings updated successfully.');
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

            return redirect()->back()
                ->with('success', ucfirst($cacheType) . ' cache cleared successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Run system maintenance tasks.
     */
    public function maintenance(Request $request)
    {
        $task = $request->get('task');

        try {
            switch ($task) {
                case 'cleanup_temp':
                    $this->cleanupTempFiles();
                    $message = 'Temporary files cleaned up successfully.';
                    break;
                    
                case 'recalculate_storage':
                    $this->recalculateStorageUsage();
                    $message = 'Storage usage recalculated successfully.';
                    break;
                    
                case 'cleanup_logs':
                    $this->cleanupOldLogs();
                    $message = 'Old logs cleaned up successfully.';
                    break;
                    
                case 'optimize_database':
                    $this->optimizeDatabase();
                    $message = 'Database optimized successfully.';
                    break;
                    
                default:
                    throw new \InvalidArgumentException('Invalid maintenance task.');
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Maintenance task failed: ' . $e->getMessage());
        }
    }

    /**
     * Export system data.
     */
    public function export(Request $request)
    {
        $type = $request->get('type');

        switch ($type) {
            case 'users':
                return $this->exportUsers();
            case 'images':
                return $this->exportImages();
            case 'analytics':
                return $this->exportAnalytics();
            default:
                return redirect()->back()
                    ->with('error', 'Invalid export type.');
        }
    }

    // Private helper methods

    private function getSystemStats(): array
    {
        return [
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
                'total_views' => ViewCount::sum('count'),
                'this_week_views' => ViewCount::thisWeek()->sum('count'),
                'total_comments' => Comment::count(),
                'pending_comments' => Comment::where('status', 'pending')->count(),
            ],
            'storage' => [
                'total_size' => Image::sum('size_bytes'),
                'average_size' => Image::avg('size_bytes') ?? 0,
                'total_files' => Image::count(),
            ],
        ];
    }

    private function getHealthChecks(): array
    {
        return [
            'database' => $this->checkDatabaseHealth(),
            'storage' => $this->checkStorageHealth(),
            'queue' => $this->checkQueueHealth(),
            'cache' => $this->checkCacheHealth(),
        ];
    }

    private function checkDatabaseHealth(): array
    {
        try {
            DB::connection()->getPdo();
            $tableCount = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE()")[0]->count;
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

    private function checkStorageHealth(): array
    {
        try {
            Storage::disk('s3')->exists('health-check.txt') ?: Storage::disk('s3')->put('health-check.txt', 'OK');
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
                'message' => 'Queue status unknown',
            ];
        }
    }

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

    private function getStorageStats(): array
    {
        return [
            'total_size' => Image::sum('size_bytes'),
            'by_mime_type' => Image::select('mime_type', DB::raw('SUM(size_bytes) as total_size'), DB::raw('COUNT(*) as count'))
                ->groupBy('mime_type')
                ->get(),
            'by_month' => Image::select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('SUM(size_bytes) as total_size'))
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->take(12)
                ->get(),
        ];
    }

    private function getQueueStats(): array
    {
        return [
            'pending' => Queue::size(),
            'failed' => DB::table('failed_jobs')->count(),
        ];
    }

    private function getRecentAuditLogs(): array
    {
        return AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->toArray();
    }

    private function getCurrentSettings(): array
    {
        return [
            'max_upload_size' => Cache::get('setting.max_upload_size', 52428800),
            'allowed_mimes' => Cache::get('setting.allowed_mimes', 'jpg,jpeg,png,webp,avif'),
            'enable_registration' => Cache::get('setting.enable_registration', true),
            'enable_comments' => Cache::get('setting.enable_comments', true),
            'comment_moderation' => Cache::get('setting.comment_moderation', true),
            'default_privacy' => Cache::get('setting.default_privacy', 'unlisted'),
        ];
    }

    private function getDiskUsage(): array
    {
        try {
            $total = disk_total_space(storage_path());
            $free = disk_free_space(storage_path());
            $used = $total - $free;
            
            return [
                'total' => $total,
                'used' => $used,
                'free' => $free,
                'percentage' => $total > 0 ? ($used / $total) * 100 : 0,
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'used' => 0,
                'free' => 0,
                'percentage' => 0,
                'error' => 'Could not determine disk usage',
            ];
        }
    }

    private function getCacheStats(): array
    {
        // This is simplified - in production you'd get actual cache metrics
        return [
            'driver' => config('cache.default'),
            'status' => 'operational',
        ];
    }

    // Analytics methods
    private function getUploadAnalytics($startDate)
    {
        return Image::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getViewAnalytics($startDate)
    {
        return ViewCount::select('date', DB::raw('SUM(count) as total'))
            ->where('date', '>=', $startDate->toDateString())
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getUserAnalytics($startDate)
    {
        return User::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getStorageAnalytics($startDate)
    {
        return Image::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(size_bytes) as total_size'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getTopImages()
    {
        return Image::select('id', 'title', 'views_count', 'likes_count')
            ->orderBy('views_count', 'desc')
            ->take(10)
            ->get();
    }

    private function getTopUsers()
    {
        return User::withCount(['images', 'albums'])
            ->orderBy('images_count', 'desc')
            ->take(10)
            ->get();
    }

    // Maintenance methods
    private function cleanupTempFiles()
    {
        // Clean up temporary files older than 24 hours
        $tempPath = storage_path('app/temp');
        if (is_dir($tempPath)) {
            $files = glob($tempPath . '/*');
            $cutoff = time() - (24 * 60 * 60);
            
            foreach ($files as $file) {
                if (filemtime($file) < $cutoff) {
                    unlink($file);
                }
            }
        }
    }

    private function recalculateStorageUsage()
    {
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                $actualUsage = $user->images()->sum('size_bytes');
                $user->update(['storage_used_bytes' => $actualUsage]);
            }
        });
    }

    private function cleanupOldLogs()
    {
        AuditLog::where('created_at', '<', now()->subMonths(6))->delete();
        ViewCount::where('date', '<', now()->subYear())->delete();
    }

    private function optimizeDatabase()
    {
        // Run database optimization commands
        DB::statement('OPTIMIZE TABLE images');
        DB::statement('OPTIMIZE TABLE albums');
        DB::statement('OPTIMIZE TABLE users');
    }

    private function exportUsers()
    {
        $users = User::with('roles')->get();
        // Implementation would generate CSV/Excel file
        // This is a placeholder
        return response()->json(['message' => 'User export started']);
    }

    private function exportImages()
    {
        $images = Image::with(['owner', 'album'])->get();
        // Implementation would generate CSV/Excel file
        return response()->json(['message' => 'Image export started']);
    }

    private function exportAnalytics()
    {
        // Implementation would generate analytics report
        return response()->json(['message' => 'Analytics export started']);
    }
}
