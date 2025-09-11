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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public routes
Route::get('/', function () {
    $featuredImages = \App\Models\Image::public()
        ->where('is_published', true)
        ->orderBy('views_count', 'desc')
        ->take(8)
        ->with('owner')
        ->get();

    $featuredAlbums = \App\Models\Album::public()
        ->where('is_published', true)
        ->orderBy('updated_at', 'desc')
        ->take(6)
        ->withCount('images')
        ->get();

    $stats = [
        'total_images' => \App\Models\Image::public()->where('is_published', true)->count(),
        'total_albums' => \App\Models\Album::public()->where('is_published', true)->count(),
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

// Public album and image viewing
// Add constraints so wildcard slug doesn't eat reserved paths like "create" or action endpoints
Route::get('/albums/{album:slug}', [AlbumController::class, 'show'])
    ->where('album', '^(?!create$|edit$|reorder$|add-images$|remove-images$|toggle-publish$).+')
    ->name('albums.show');
Route::get('/collections/{collection:slug}', [CollectionController::class, 'show'])
    ->where('collection', '^(?!create$|edit$|add-item$|remove-item$|reorder$|toggle-publish$).+')
    ->name('collections.show');
Route::get('/images/{image:slug}', [ImageController::class, 'show'])->name('images.show');

// Image download
Route::get('/images/{image:slug}/download', [ImageController::class, 'download'])
    ->name('images.download');

// Authentication routes (Breeze)
require __DIR__.'/auth.php';

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Image Management
    Route::get('/my/images', [ImageController::class, 'index'])
        ->defaults('owner', 'mine')
        ->name('my.images');
    
    Route::get('/images/{image:slug}/edit', [ImageController::class, 'edit'])
        ->name('images.edit');
    Route::patch('/images/{image:slug}', [ImageController::class, 'update'])
        ->name('images.update');
    Route::delete('/images/{image:slug}', [ImageController::class, 'destroy'])
        ->name('images.destroy');

    // Image interactions
    Route::post('/images/{image:slug}/like', [ImageController::class, 'toggleLike'])
        ->name('images.like');
    Route::post('/images/{image:slug}/toggle-publish', [ImageController::class, 'togglePublish'])
        ->name('images.toggle-publish');

    // Album Management
    Route::get('/my/albums', [AlbumController::class, 'index'])
        ->defaults('owner', 'mine')
        ->name('my.albums');
    
    Route::resource('albums', AlbumController::class)->except(['index', 'show']);
    
    // Album specific actions
    Route::post('/albums/{album}/reorder', [AlbumController::class, 'reorder'])
        ->name('albums.reorder');
    Route::post('/albums/{album}/add-images', [AlbumController::class, 'addImages'])
        ->name('albums.add-images');
    Route::delete('/albums/{album}/remove-images', [AlbumController::class, 'removeImages'])
        ->name('albums.remove-images');
    Route::post('/albums/{album}/toggle-publish', [AlbumController::class, 'togglePublish'])
        ->name('albums.toggle-publish');

    // Collection Management
    Route::get('/my/collections', [CollectionController::class, 'index'])
        ->defaults('curator', 'mine')
        ->name('my.collections');
    
    Route::resource('collections', CollectionController::class)->except(['index', 'show']);
    
    // Collection specific actions
    Route::post('/collections/{collection}/add-item', [CollectionController::class, 'addItem'])
        ->name('collections.add-item');
    Route::delete('/collections/{collection}/remove-item', [CollectionController::class, 'removeItem'])
        ->name('collections.remove-item');
    Route::post('/collections/{collection}/reorder', [CollectionController::class, 'reorderItems'])
        ->name('collections.reorder');
    Route::post('/collections/{collection}/toggle-publish', [CollectionController::class, 'togglePublish'])
        ->name('collections.toggle-publish');

    // Upload Routes
    Route::get('/upload', function () {
        return Inertia::render('Images/Upload', [
            'albums' => auth()->user()->albums()->select('id', 'title')->get(),
            'maxUploadSize' => config('filesystems.gallery.max_upload_size', 52428800),
            'allowedMimes' => implode(',', config('filesystems.gallery.allowed_extensions', ['jpg','jpeg','png','webp','avif'])),
            'allowedExtensions' => implode(',', config('filesystems.gallery.allowed_extensions', ['jpg','jpeg','png','webp','avif'])),
            'defaultPrivacy' => config('filesystems.gallery.default_privacy', 'unlisted'),
            'storageUsage' => [
                'used' => auth()->user()->storage_used_bytes,
                'quota' => auth()->user()->storage_quota_bytes,
                'percentage' => auth()->user()->getStorageUsagePercentage(),
                'remaining' => auth()->user()->getRemainingStorageBytes(),
            ],
        ]);
    })->name('upload')->middleware('can:create,App\Models\Image');

    // Handle direct uploads from the Upload page (maps to existing API logic)
    Route::post('/upload', [\App\Http\Controllers\Api\UploadController::class, 'direct'])
        ->name('upload.store');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // User Management
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/assign-role', [UserController::class, 'assignRole'])
        ->name('users.assign-role');
    Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])
        ->name('users.toggle-active');
    Route::post('/users/{user}/reset-storage', [UserController::class, 'resetStorage'])
        ->name('users.reset-storage');

    // System Management
    Route::get('/system', [SystemController::class, 'index'])->name('system.index');
    Route::get('/system/analytics', [SystemController::class, 'analytics'])->name('system.analytics');
    Route::get('/system/settings', [SystemController::class, 'settings'])->name('system.settings');
    Route::post('/system/settings', [SystemController::class, 'updateSettings'])->name('system.update-settings');
    Route::post('/system/clear-cache', [SystemController::class, 'clearCache'])->name('system.clear-cache');
    Route::post('/system/maintenance', [SystemController::class, 'maintenance'])->name('system.maintenance');
    Route::get('/system/export/{type}', [SystemController::class, 'export'])->name('system.export');

    // Moderation
    Route::get('/moderation', [ModerationController::class, 'index'])->name('moderation.index');
    Route::get('/moderation/comments', [ModerationController::class, 'comments'])->name('moderation.comments');
    Route::get('/moderation/reports', [ModerationController::class, 'reports'])->name('moderation.reports');
    
    // Comment moderation actions
    Route::post('/comments/{comment}/approve', [ModerationController::class, 'approveComment'])
        ->name('comments.approve');
    Route::post('/comments/{comment}/reject', [ModerationController::class, 'rejectComment'])
        ->name('comments.reject');
    Route::post('/comments/{comment}/spam', [ModerationController::class, 'markSpam'])
        ->name('comments.spam');
    Route::post('/comments/bulk-moderate', [ModerationController::class, 'bulkModerate'])
        ->name('comments.bulk-moderate');
});

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

    $images = \App\Models\Image::visible()
        ->where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('caption', 'like', "%{$query}%")
              ->orWhere('alt_text', 'like', "%{$query}%");
        })
        ->with(['owner', 'album'])
        ->take(12)
        ->get();

    $albums = \App\Models\Album::visible()
        ->where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        })
        ->with(['owner'])
        ->withCount('images')
        ->take(8)
        ->get();

    $collections = \App\Models\Collection::visible()
        ->where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        })
        ->with(['curator'])
        ->withCount('items')
        ->take(6)
        ->get();

    return Inertia::render('Search', [
        'query' => $query,
        'results' => [
            'images' => $images,
            'albums' => $albums,
            'collections' => $collections,
        ],
    ]);
})->name('search');

// Tag browsing
Route::get('/tags', function () {
    $tags = \App\Models\Tag::popular(1)
        ->take(50)
        ->get();

    return Inertia::render('Tags/Index', [
        'tags' => $tags,
    ]);
})->name('tags.index');

Route::get('/tags/{tag:slug}', function (\App\Models\Tag $tag, Illuminate\Http\Request $request) {
    $images = \App\Models\Image::visible()
        ->whereHas('tags', function ($q) use ($tag) {
            $q->where('tag_id', $tag->id);
        })
        ->with(['owner', 'album', 'tags'])
        ->orderBy('created_at', 'desc')
        ->paginate(24);

    return Inertia::render('Tags/Show', [
        'tag' => $tag,
        'images' => $images,
    ]);
})->name('tags.show');

// Privacy Policy & Terms (if needed)
Route::get('/privacy', function () {
    return Inertia::render('Legal/Privacy');
})->name('privacy');

Route::get('/terms', function () {
    return Inertia::render('Legal/Terms');
})->name('terms');
