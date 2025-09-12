<?php

use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes - CLEAN VERSION WITH NO DUPLICATES
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

// Search Routes
Route::get('/search', function (Illuminate\Http\Request $request) {
    $query = $request->get('q');
    $tag = $request->get('tag');
    
    if (!$query && !$tag) {
        return Inertia::render('Search', [
            'query' => $query,
            'tag' => $tag,
            'results' => [
                'images' => collect([]),
                'albums' => collect([]),
                'collections' => collect([]),
            ],
        ]);
    }

    $imagesQuery = \App\Models\Image::with(['owner', 'album', 'tags'])
        ->whereIn('privacy', ['public', 'unlisted'])
        ->where('is_published', true);
    
    if ($query) {
        $imagesQuery->where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('caption', 'like', "%{$query}%")
              ->orWhere('alt_text', 'like', "%{$query}%")
              ->orWhereHas('tags', function ($tagQuery) use ($query) {
                  $tagQuery->where('name', 'like', "%{$query}%");
              });
        });
    }
    
    if ($tag) {
        $imagesQuery->whereHas('tags', function ($tagQuery) use ($tag) {
            $tagQuery->where('name', $tag);
        });
    }

    $images = $imagesQuery->take(12)->get();

    $albumsQuery = \App\Models\Album::with(['owner'])
        ->withCount('images')
        ->whereIn('privacy', ['public', 'unlisted'])
        ->where('is_published', true);
    
    if ($query) {
        $albumsQuery->where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        });
    }
    
    $albums = $albumsQuery->take(8)->get();

    try {
        $collectionsQuery = \App\Models\Collection::with(['curator'])
            ->whereIn('privacy', ['public', 'unlisted'])
            ->where('is_published', true);
        
        if ($query) {
            $collectionsQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            });
        }
        
        $collections = $collectionsQuery->take(6)->get();
    } catch (\Exception $e) {
        $collections = collect([]);
    }

    return Inertia::render('Search', [
        'query' => $query,
        'tag' => $tag,
        'results' => [
            'images' => $images,
            'albums' => $albums,
            'collections' => $collections,
        ],
    ]);
})->name('search');

// Image download (before image show route to avoid conflicts)
Route::get('/images/{image:slug}/download', [ImageController::class, 'download'])->name('images.download');

// Public viewing routes
Route::get('/images/{image:slug}', [ImageController::class, 'show'])->name('images.show');
Route::get('/collections/{collection:id}', [CollectionController::class, 'show'])->name('collections.show');

// Albums routes - SPECIFIC ROUTES FIRST
Route::get('/albums/create', [AlbumController::class, 'create'])->name('albums.create');
Route::get('/albums/{album:slug}', [AlbumController::class, 'show'])->name('albums.show');

// Comment routes (public POST, authenticated DELETE)
Route::post('/images/{image:slug}/comments', [CommentController::class, 'store'])->middleware('auth')->name('images.comments.store');
Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->middleware('auth')->name('comments.destroy');

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
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');
    Route::patch('/profile/privacy', [ProfileController::class, 'updatePrivacy'])->name('profile.privacy');

    // My Content Routes
    Route::prefix('my')->name('my.')->group(function () {
        Route::get('/images', [ImageController::class, 'myImages'])->name('images');
        Route::get('/albums', [AlbumController::class, 'myAlbums'])->name('albums');
        Route::get('/collections', [CollectionController::class, 'myCollections'])->name('collections');
    });
    
    // Image Management
    Route::get('/images/{image:slug}/edit', [ImageController::class, 'edit'])->name('images.edit');
    Route::patch('/images/{image:slug}', [ImageController::class, 'update'])->name('images.update');
    Route::delete('/images/{image:slug}', [ImageController::class, 'destroy'])->name('images.destroy');
    Route::post('/images/bulk-action', [ImageController::class, 'bulkAction'])->name('images.bulk-action');
    Route::post('/images/{image:slug}/toggle-publish', [ImageController::class, 'togglePublish'])->name('images.toggle-publish');
    Route::post('/images/{image:slug}/like', [ImageController::class, 'like'])->name('images.like');

    // Album Management
    Route::post('/albums', [AlbumController::class, 'store'])->name('albums.store');
    Route::get('/albums/{album}/edit', [AlbumController::class, 'edit'])->name('albums.edit');
    Route::patch('/albums/{album}', [AlbumController::class, 'update'])->name('albums.update');
    Route::delete('/albums/{album}', [AlbumController::class, 'destroy'])->name('albums.destroy');
    Route::post('/albums/bulk-action', [AlbumController::class, 'bulkAction'])->name('albums.bulk-action');
    
    // Album Image Management
    Route::get('/albums/{album}/add-images', [AlbumController::class, 'addImagesForm'])->name('albums.add-images-form');
    Route::post('/albums/{album}/add-images', [AlbumController::class, 'addImages'])->name('albums.add-images');
    Route::delete('/albums/{album}/remove-images', [AlbumController::class, 'removeImages'])->name('albums.remove-images');

    // Collection Management
    Route::post('/collections', [CollectionController::class, 'store'])->name('collections.store');
    Route::get('/collections/create', [CollectionController::class, 'create'])->name('collections.create');
    Route::get('/collections/{collection}/edit', [CollectionController::class, 'edit'])->name('collections.edit');
    Route::patch('/collections/{collection}', [CollectionController::class, 'update'])->name('collections.update');
    Route::delete('/collections/{collection}', [CollectionController::class, 'destroy'])->name('collections.destroy');
    Route::post('/collections/{collection}/add-image', [CollectionController::class, 'addImage'])->name('collections.add-image');
    Route::delete('/collections/{collection}/remove-image', [CollectionController::class, 'removeImage'])->name('collections.remove-image');
    Route::post('/collections/{collection}/toggle-publish', [CollectionController::class, 'togglePublish'])->name('collections.toggle-publish');

    // Likes System (works for images, comments, etc.)
    Route::post('/likes/toggle', [LikeController::class, 'toggle'])->name('likes.toggle');

    // Tags System
    Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
    Route::post('/tags/attach', [TagController::class, 'attach'])->name('tags.attach');

// Upload Routes
Route::get('/upload', function () {
    $user = auth()->user();
    
    return Inertia::render('Images/Upload', [
        'albums' => $user->albums()->select('id', 'title')->get(),
        'maxUploadSize' => 52428800,
        'allowedMimes' => 'jpg,jpeg,png,webp,avif',
        'defaultPrivacy' => 'unlisted',
        'storageUsage' => [
            'used' => $user->storage_used_bytes ?? 0,
            'quota' => $user->storage_quota_bytes ?? 1073741824,
            'percentage' => $user->getStorageUsagePercentage(),
            'remaining' => $user->getRemainingStorageBytes(),
        ],
        'recentUploads' => $user->images()
            ->where('is_published', true)
            ->latest()
            ->limit(12)
            ->select('id', 'title', 'slug', 'storage_path', 'alt_text')
            ->get(),
    ]);
})->name('upload');



Route::post('/upload', [App\Http\Controllers\Api\UploadController::class, 'direct'])
    ->middleware('auth')
    ->name('upload.store');

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
    Route::get('/', [SystemController::class, 'index'])->name('system.index');
    Route::get('/analytics', [SystemController::class, 'analytics'])->name('system.analytics');
    Route::get('/settings', [SystemController::class, 'settings'])->name('system.settings');
    Route::post('/settings', [SystemController::class, 'updateSettings'])->name('system.update-settings');
    Route::post('/clear-cache', [SystemController::class, 'clearCache'])->name('system.clear-cache');

    // User Management Routes
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/assign-role', [UserController::class, 'assignRole'])->name('users.assign-role');
    Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('/users/{user}/reset-storage', [UserController::class, 'resetStorage'])->name('users.reset-storage');
    Route::post('/users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk-action');
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    Route::get('/users/stats', [UserController::class, 'getStats'])->name('users.stats');

    // Moderation Routes
    Route::get('/moderation', [ModerationController::class, 'index'])->name('moderation.index');
    Route::get('/moderation/comments', [ModerationController::class, 'comments'])->name('moderation.comments');
    Route::get('/moderation/reports', [ModerationController::class, 'reports'])->name('moderation.reports');
    
    // Comment Moderation Actions
    Route::post('/comments/{comment}/approve', [ModerationController::class, 'approveComment'])->name('moderation.approve-comment');
    Route::post('/comments/{comment}/reject', [ModerationController::class, 'rejectComment'])->name('moderation.reject-comment');
    Route::post('/comments/{comment}/spam', [ModerationController::class, 'markSpam'])->name('moderation.mark-spam');
    Route::post('/comments/bulk-moderate', [ModerationController::class, 'bulkModerate'])->name('moderation.bulk-moderate');
    
    // Image/Album Moderation Actions
    Route::post('/images/{image}/approve', [ModerationController::class, 'approveImage'])->name('moderation.approve-image');
    Route::post('/images/{image}/reject', [ModerationController::class, 'rejectImage'])->name('moderation.reject-image');
    Route::post('/albums/{album}/approve', [ModerationController::class, 'approveAlbum'])->name('moderation.approve-album');
    Route::post('/albums/{album}/reject', [ModerationController::class, 'rejectAlbum'])->name('moderation.reject-album');
    
    // Bulk Moderation Actions
    Route::post('/images/bulk-moderate', [ModerationController::class, 'bulkModerateImages'])->name('moderation.bulk-moderate-images');
    Route::post('/albums/bulk-moderate', [ModerationController::class, 'bulkModerateAlbums'])->name('moderation.bulk-moderate-albums');
    
    // Moderation API Endpoints
    Route::get('/moderation/stats', [ModerationController::class, 'getModerationStats'])->name('moderation.stats');
    Route::get('/moderation/pending-summary', [ModerationController::class, 'getPendingSummary'])->name('moderation.pending-summary');
});




