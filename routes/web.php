<?php

use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes - FIXED VERSION WITH PROPER ORDERING
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    $featuredImages = \App\Models\Image::whereIn('privacy', ['public', 'unlisted'])
        ->where('is_published', true)
        ->orderBy('views_count', 'desc')
        ->take(8)
        ->with('owner')
        ->get();

    $featuredAlbums = \App\Models\Album::whereIn('privacy', ['public', 'unlisted'])
        ->where('is_published', true)
        ->orderBy('updated_at', 'desc')
        ->take(6)
        ->withCount('images')
        ->get();

    $stats = [
        'total_images' => \App\Models\Image::whereIn('privacy', ['public', 'unlisted'])->where('is_published', true)->count(),
        'total_albums' => \App\Models\Album::whereIn('privacy', ['public', 'unlisted'])->where('is_published', true)->count(),
        'total_users' => \App\Models\User::where('is_active', true)->count(),
    ];

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register') && config('gallery.enable_registration', true),
        'featuredImages' => $featuredImages,
        'featuredAlbums' => $featuredAlbums,
        'stats' => $stats,
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

// Gallery browsing (public)
Route::get('/gallery', [ImageController::class, 'index'])->name('gallery.index');
Route::get('/albums', [AlbumController::class, 'index'])->name('albums.index');
Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');

// Image download (before image show route)
Route::get('/images/{image:slug}/download', [ImageController::class, 'download'])->name('images.download');

// Public viewing routes
Route::get('/images/{image:slug}', [ImageController::class, 'show'])->name('images.show');
Route::get('/collections/{collection:slug}', [CollectionController::class, 'show'])->name('collections.show');

// FIXED: Albums routes - SPECIFIC ROUTES FIRST
Route::get('/albums/create', [AlbumController::class, 'create'])->name('albums.create');
Route::get('/albums/{album:slug}', [AlbumController::class, 'show'])->name('albums.show');

// Search Routes
Route::get('/search', function (Illuminate\Http\Request $request) {
    $query = $request->get('q');
    
    if (!$query) {
        return Inertia::render('Search', [
            'query' => $query,
            'results' => [
                'images' => collect([]),
                'albums' => collect([]),
                'collections' => collect([]),
            ],
        ]);
    }

    $images = \App\Models\Image::whereIn('privacy', ['public', 'unlisted'])
        ->where('is_published', true)
        ->where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('caption', 'like', "%{$query}%")
              ->orWhere('alt_text', 'like', "%{$query}%");
        })
        ->with(['owner', 'album'])
        ->take(12)
        ->get();

    $albums = \App\Models\Album::whereIn('privacy', ['public', 'unlisted'])
        ->where('is_published', true)
        ->where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        })
        ->with(['owner'])
        ->withCount('images')
        ->take(8)
        ->get();

    try {
        $collections = \App\Models\Collection::whereIn('privacy', ['public', 'unlisted'])
            ->where('is_published', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->with(['curator'])
            ->take(6)
            ->get();
    } catch (\Exception $e) {
        $collections = collect([]);
    }

    return Inertia::render('Search', [
        'query' => $query,
        'results' => [
            'images' => $images,
            'albums' => $albums,
            'collections' => $collections,
        ],
    ]);
})->name('search');

// Comment routes
Route::post('/images/{image:slug}/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('images.comments.store');
Route::delete('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');

// Legal pages
Route::get('/privacy', function () {
    return Inertia::render('Legal/Privacy');
})->name('privacy');

Route::get('/terms', function () {
    return Inertia::render('Legal/Terms');
})->name('terms');

// Authentication routes
require __DIR__.'/auth.php';

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Profile routes (add these in the authenticated middleware group)
Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');
Route::patch('/profile/privacy', [ProfileController::class, 'updatePrivacy'])->name('profile.privacy');


    // FIXED: My Content Routes - COMPLETELY SEPARATE FROM PUBLIC ROUTES
    Route::prefix('my')->name('my.')->group(function () {
        // My Images - dedicated route and controller method
        Route::get('/images', [ImageController::class, 'myImages'])->name('images');
        
        // My Albums - dedicated route and controller method  
        Route::get('/albums', [AlbumController::class, 'myAlbums'])->name('albums');
        
        // My Collections
        Route::get('/collections', [CollectionController::class, 'myCollections'])->name('collections');
    });
    
    // Image Management
    Route::get('/images/{image:slug}/edit', [ImageController::class, 'edit'])->name('images.edit');
    Route::patch('/images/{image:slug}', [ImageController::class, 'update'])->name('images.update');
    Route::delete('/images/{image:slug}', [ImageController::class, 'destroy'])->name('images.destroy');
    Route::post('/images/bulk', [ImageController::class, 'bulkAction'])->name('images.bulk');
    Route::post('/images/{image:slug}/toggle-publish', [ImageController::class, 'togglePublish'])->name('images.toggle-publish');

    // Album Management - THESE COME AFTER /albums/create
    Route::post('/albums', [AlbumController::class, 'store'])->name('albums.store');
    Route::post('/albums/bulk', [AlbumController::class, 'bulkAction'])->name('albums.bulk');
    
    // Album Image Management - SPECIFIC ROUTES
    Route::get('/albums/{album}/add-images', [AlbumController::class, 'addImagesForm'])->name('albums.add-images-form');
    Route::post('/albums/{album}/add-images', [AlbumController::class, 'addImages'])->name('albums.add-images');
    Route::delete('/albums/{album}/remove-images', [AlbumController::class, 'removeImages'])->name('albums.remove-images');
    
    // Album CRUD - These use ID not slug to avoid conflicts
    Route::get('/albums/{album}/edit', [AlbumController::class, 'edit'])->name('albums.edit');
    Route::patch('/albums/{album}', [AlbumController::class, 'update'])->name('albums.update');
    Route::delete('/albums/{album}', [AlbumController::class, 'destroy'])->name('albums.destroy');

    // Collection Management
    Route::resource('collections', CollectionController::class)->except(['index', 'show']);
    Route::post('/collections/{collection}/add-item', [CollectionController::class, 'addItem'])->name('collections.add-item');
    Route::delete('/collections/{collection}/remove-item', [CollectionController::class, 'removeItem'])->name('collections.remove-item');
    Route::post('/collections/{collection}/reorder', [CollectionController::class, 'reorderItems'])->name('collections.reorder');
    Route::post('/collections/{collection}/toggle-publish', [CollectionController::class, 'togglePublish'])->name('collections.toggle-publish');

    // Upload Routes
    Route::get('/upload', function () {
        $user = auth()->user();
        
        if (!$user->can('create', App\Models\Image::class)) {
            return redirect('/dashboard')->with('upload_request_needed', true);
        }
        
        return Inertia::render('Images/Upload', [
            'albums' => $user->albums()->select('id', 'title')->get(),
            'maxUploadSize' => config('filesystems.gallery.max_upload_size', 52428800),
            'allowedMimes' => implode(',', config('filesystems.gallery.allowed_extensions', ['jpg','jpeg','png','webp','avif'])),
            'allowedExtensions' => implode(',', config('filesystems.gallery.allowed_extensions', ['jpg','jpeg','png','webp','avif'])),
            'defaultPrivacy' => config('filesystems.gallery.default_privacy', 'unlisted'),
            'storageUsage' => [
                'used' => $user->storage_used_bytes ?? 0,
                'quota' => $user->storage_quota_bytes ?? 0,
                'percentage' => $user->getStorageUsagePercentage(),
                'remaining' => $user->getRemainingStorageBytes(),
            ],
        ]);
    })->name('upload');

    Route::post('/upload', [\App\Http\Controllers\Api\UploadController::class, 'direct'])->name('upload.store');

    // Request editor access
    Route::post('/request-editor-access', function () {
        $user = auth()->user();
        \Log::info("User {$user->name} ({$user->email}) requested editor access");
        return redirect('/dashboard')->with('success', 'Request sent to administrators');
    })->name('request-editor-access');
});

// Admin Routes - Complete version
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    
    // System Routes
    Route::get('/', [App\Http\Controllers\Admin\SystemController::class, 'index'])->name('system.index');
    Route::get('/analytics', [App\Http\Controllers\Admin\SystemController::class, 'analytics'])->name('system.analytics');
    Route::get('/settings', [App\Http\Controllers\Admin\SystemController::class, 'settings'])->name('system.settings');
    Route::post('/settings', [App\Http\Controllers\Admin\SystemController::class, 'updateSettings'])->name('system.update-settings');
    Route::post('/clear-cache', [App\Http\Controllers\Admin\SystemController::class, 'clearCache'])->name('system.clear-cache');

    // User Management Routes
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::post('/users/{user}/assign-role', [App\Http\Controllers\Admin\UserController::class, 'assignRole'])->name('users.assign-role');
    Route::post('/users/{user}/toggle-active', [App\Http\Controllers\Admin\UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('/users/{user}/reset-storage', [App\Http\Controllers\Admin\UserController::class, 'resetStorage'])->name('users.reset-storage');

    // Moderation Routes
    Route::get('/moderation', [App\Http\Controllers\Admin\ModerationController::class, 'index'])->name('moderation.index');
    Route::get('/moderation/comments', [App\Http\Controllers\Admin\ModerationController::class, 'comments'])->name('moderation.comments');
    
    // Comment Moderation Actions
    Route::post('/comments/{comment}/approve', [App\Http\Controllers\Admin\ModerationController::class, 'approveComment'])->name('moderation.approve-comment');
    Route::post('/comments/{comment}/reject', [App\Http\Controllers\Admin\ModerationController::class, 'rejectComment'])->name('moderation.reject-comment');
    Route::post('/comments/{comment}/spam', [App\Http\Controllers\Admin\ModerationController::class, 'markSpam'])->name('moderation.mark-spam');
    Route::post('/comments/bulk-moderate', [App\Http\Controllers\Admin\ModerationController::class, 'bulkModerate'])->name('moderation.bulk-moderate');
    
    // Image/Album Moderation Actions
    Route::post('/images/{image}/approve', [App\Http\Controllers\Admin\ModerationController::class, 'approveImage'])->name('moderation.approve-image');
    Route::post('/images/{image}/reject', [App\Http\Controllers\Admin\ModerationController::class, 'rejectImage'])->name('moderation.reject-image');
    Route::post('/albums/{album}/approve', [App\Http\Controllers\Admin\ModerationController::class, 'approveAlbum'])->name('moderation.approve-album');
    Route::post('/albums/{album}/reject', [App\Http\Controllers\Admin\ModerationController::class, 'rejectAlbum'])->name('moderation.reject-album');
    // Additional user management routes
    Route::post('/users/bulk-action', [App\Http\Controllers\Admin\UserController::class, 'bulkAction'])->name('users.bulk-action');
    Route::get('/users/export', [App\Http\Controllers\Admin\UserController::class, 'export'])->name('users.export');
    Route::get('/users/stats', [App\Http\Controllers\Admin\UserController::class, 'getStats'])->name('users.stats');

});
