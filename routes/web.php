<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\GalleryController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/albums/create', [AlbumController::class, 'create'])->name('albums.create');
    Route::get('/albums/{album}', [AlbumController::class, 'show'])->name('albums.show');

    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit')
        ->middleware('can:manage-settings');

    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update')
        ->middleware('can:manage-settings');

    // Gallery routes
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
    Route::get('/gallery/{image}', [GalleryController::class, 'show'])->name('gallery.show');
});

require __DIR__.'/auth.php';
