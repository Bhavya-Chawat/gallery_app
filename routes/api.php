<?php

use App\Http\Controllers\Api\AlbumApiController;
use App\Http\Controllers\Api\CommentApiController;
use App\Http\Controllers\Api\ImageApiController;
use App\Http\Controllers\Api\UploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes
Route::prefix('v1')->group(function () {
    
    // Public gallery browsing
    Route::get('/images', [ImageApiController::class, 'index']);
    Route::get('/images/{image}', [ImageApiController::class, 'show']);
    Route::get('/albums', [AlbumApiController::class, 'index']);
    Route::get('/albums/{album}', [AlbumApiController::class, 'show']);
    Route::get('/albums/{album}/images', [AlbumApiController::class, 'images']);

    // Collections
    Route::apiResource('collections', \App\Http\Controllers\Api\CollectionApiController::class)->only(['index', 'show']);

    // Tags
    Route::get('/tags', function (Request $request) {
        $query = \App\Models\Tag::query();

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('popular')) {
            $query->popular($request->get('min_usage', 1));
        }

        if ($request->filled('featured')) {
            $query->featured();
        }

        $tags = $query->orderBy('usage_count', 'desc')
            ->take($request->get('limit', 50))
            ->get();

        return response()->json([
            'data' => $tags,
        ]);
    });

    Route::get('/tags/{tag:slug}', function (\App\Models\Tag $tag) {
        return response()->json([
            'data' => $tag,
        ]);
    });

    // Search
    Route::get('/search', function (Request $request) {
        $query = $request->get('q');
        $type = $request->get('type', 'all'); // all, images, albums, collections

        if (!$query) {
            return response()->json([
                'data' => [
                    'images' => [],
                    'albums' => [],
                    'collections' => [],
                ],
                'query' => $query,
            ]);
        }

        $results = [];

        if ($type === 'all' || $type === 'images') {
            $results['images'] = \App\Models\Image::visible()
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('caption', 'like', "%{$query}%")
                      ->orWhere('alt_text', 'like', "%{$query}%");
                })
                ->with(['owner', 'album', 'versions'])
                ->take(12)
                ->get();
        }

        if ($type === 'all' || $type === 'albums') {
            $results['albums'] = \App\Models\Album::visible()
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                })
                ->with(['owner', 'coverImage'])
                ->withCount('images')
                ->take(8)
                ->get();
        }

        if ($type === 'all' || $type === 'collections') {
            $results['collections'] = \App\Models\Collection::visible()
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                })
                ->with(['curator', 'coverImage'])
                ->withCount('items')
                ->take(6)
                ->get();
        }

        return response()->json([
            'data' => $results,
            'query' => $query,
            'type' => $type,
        ]);
    });

    // Statistics
    Route::get('/stats', function () {
        return response()->json([
            'data' => [
                'total_images' => \App\Models\Image::public()->where('is_published', true)->count(),
                'total_albums' => \App\Models\Album::public()->where('is_published', true)->count(),
                'total_collections' => \App\Models\Collection::public()->where('is_published', true)->count(),
                'total_users' => \App\Models\User::where('is_active', true)->count(),
                'total_tags' => \App\Models\Tag::where('usage_count', '>', 0)->count(),
                'updated_at' => now()->toISOString(),
            ],
        ]);
    });
});

// Authenticated API routes
Route::middleware(['auth:sanctum', 'active_user'])->prefix('v1')->group(function () {

    // User info
    Route::get('/user', function (Request $request) {
        $user = $request->user()->load('roles');
        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->getAvatarUrl(),
                'storage_used' => $user->storage_used_bytes,
                'storage_quota' => $user->storage_quota_bytes,
                'storage_percentage' => $user->getStorageUsagePercentage(),
                'roles' => $user->roles->pluck('slug'),
                'permissions' => $user->roles->pluck('permissions')->flatten()->unique()->values(),
                'is_active' => $user->is_active,
                'last_login_at' => $user->last_login_at,
                'created_at' => $user->created_at,
            ],
        ]);
    });

    // Image management
    Route::apiResource('images', ImageApiController::class)->except(['index', 'show']);
    Route::post('/images/bulk', [ImageApiController::class, 'bulk']);
    Route::get('/images/{image}/signed-url', [ImageApiController::class, 'signedUrl']);
    Route::post('/images/{image}/like', [ImageApiController::class, 'toggleLike']);

    // Album management
    Route::apiResource('albums', AlbumApiController::class)->except(['index', 'show']);
    Route::post('/albums/{album}/reorder', [AlbumApiController::class, 'reorder']);
    Route::post('/albums/{album}/add-images', [AlbumApiController::class, 'addImages']);
    Route::delete('/albums/{album}/remove-images', [AlbumApiController::class, 'removeImages']);

    // Collection management
    Route::apiResource('collections', \App\Http\Controllers\Api\CollectionApiController::class)
        ->except(['index', 'show']);

    // Upload management
    Route::post('/upload/presign', [UploadController::class, 'presign']);
    Route::post('/upload/complete', [UploadController::class, 'complete']);
    Route::get('/upload/status/{session}', [UploadController::class, 'status']);
    Route::post('/upload/direct', [UploadController::class, 'direct'])->name('upload.store');

    // Comments
    Route::get('/images/{image}/comments', [CommentApiController::class, 'index']);
    Route::post('/images/{image}/comments', [CommentApiController::class, 'store']);
    Route::patch('/comments/{comment}', [CommentApiController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentApiController::class, 'destroy']);
    Route::post('/comments/{comment}/like', [CommentApiController::class, 'toggleLike']);

    // User's content
    Route::get('/my/images', function (Request $request) {
        return app(ImageApiController::class)->index($request->merge(['owner_id' => auth()->id()]));
    });

    Route::get('/my/albums', function (Request $request) {
        return app(AlbumApiController::class)->index($request->merge(['owner_id' => auth()->id()]));
    });

    Route::get('/my/collections', function (Request $request) {
        return app(\App\Http\Controllers\Api\CollectionApiController::class)->index($request->merge(['curator_id' => auth()->id()]));
    });

    // Liked content
    Route::get('/my/likes', function (Request $request) {
        $likes = auth()->user()->likes()
            ->with(['likeable' => function ($morphTo) {
                $morphTo->morphWith([
                    \App\Models\Image::class => ['owner', 'album', 'versions'],
                    \App\Models\Album::class => ['owner', 'coverImage'],
                ]);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json($likes);
    });

    // Analytics (user's own content)
    Route::get('/my/analytics', function (Request $request) {
        $user = auth()->user();
        $dateRange = $request->get('days', 30);
        $startDate = now()->subDays($dateRange);

        return response()->json([
            'data' => [
                'images' => [
                    'total' => $user->images()->count(),
                    'published' => $user->images()->where('is_published', true)->count(),
                    'private' => $user->images()->where('privacy', 'private')->count(),
                    'this_period' => $user->images()->where('created_at', '>=', $startDate)->count(),
                ],
                'albums' => [
                    'total' => $user->albums()->count(),
                    'published' => $user->albums()->where('is_published', true)->count(),
                ],
                'engagement' => [
                    'total_views' => $user->images()->sum('views_count'),
                    'total_likes' => $user->images()->sum('likes_count'),
                    'total_comments' => $user->images()->sum('comments_count'),
                ],
                'storage' => [
                    'used_bytes' => $user->storage_used_bytes,
                    'quota_bytes' => $user->storage_quota_bytes,
                    'percentage' => $user->getStorageUsagePercentage(),
                    'remaining_bytes' => $user->getRemainingStorageBytes(),
                ],
                'period' => [
                    'days' => $dateRange,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => now()->toDateString(),
                ],
            ],
        ]);
    });
});

// Admin API routes
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('v1/admin')->group(function () {

    // System statistics
    Route::get('/stats', function () {
        return response()->json([
            'data' => [
                'users' => [
                    'total' => \App\Models\User::count(),
                    'active' => \App\Models\User::where('is_active', true)->count(),
                    'verified' => \App\Models\User::whereNotNull('email_verified_at')->count(),
                ],
                'content' => [
                    'images' => \App\Models\Image::count(),
                    'published_images' => \App\Models\Image::where('is_published', true)->count(),
                    'albums' => \App\Models\Album::count(),
                    'published_albums' => \App\Models\Album::where('is_published', true)->count(),
                    'collections' => \App\Models\Collection::count(),
                ],
                'engagement' => [
                    'total_views' => \App\Models\ViewCount::sum('count'),
                    'total_comments' => \App\Models\Comment::count(),
                    'pending_comments' => \App\Models\Comment::where('status', 'pending')->count(),
                ],
                'storage' => [
                    'total_size' => \App\Models\Image::sum('size_bytes'),
                    'average_size' => \App\Models\Image::avg('size_bytes') ?? 0,
                ],
            ],
        ]);
    });

    // User management
    Route::apiResource('users', \App\Http\Controllers\Api\Admin\UserApiController::class ?? UserController::class);
    
    // Content moderation
    Route::get('/moderation/comments', function (Request $request) {
        $status = $request->get('status', 'pending');
        
        $query = \App\Models\Comment::with(['user', 'image']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $comments = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json($comments);
    });

    // Audit logs
    Route::get('/audit-logs', function (Request $request) {
        $logs = \App\Models\AuditLog::with('user')
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('action'), fn($q) => $q->where('action', $request->action))
            ->when($request->filled('auditable_type'), fn($q) => $q->where('auditable_type', $request->auditable_type))
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 50));

        return response()->json($logs);
    });
});

// Health check endpoint
Route::get('/health', function () {
    try {
        // Check database
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        
        // Check storage
        \Illuminate\Support\Facades\Storage::disk('s3')->exists('health-check.txt') ?: 
            \Illuminate\Support\Facades\Storage::disk('s3')->put('health-check.txt', 'OK');
        
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'version' => config('app.version', '1.0.0'),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'error' => $e->getMessage(),
            'timestamp' => now()->toISOString(),
        ], 503);
    }
});

// Rate limiting for public API
Route::middleware('throttle:api')->group(function () {
    // Additional public endpoints that need rate limiting
});
