<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        
        // Get user statistics
        $stats = [
            'images' => $user->images()->count(),
            'albums' => $user->albums()->count(),
            'views' => $user->images()->sum('views_count'),
            'likes' => $user->images()->sum('likes_count'),
        ];

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    /**
     * Display public profile view
     */
    public function show(User $user): Response
    {
        // Check if profile is private
        if ($user->profile_visibility === 'private' && auth()->id() !== $user->id) {
            abort(404);
        }

        // Get public albums and images
        $publicAlbums = $user->albums()
            ->where('is_published', true)
            ->whereIn('privacy', ['public', 'unlisted'])
            ->with('coverImage')
            ->withCount('images')
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        $publicImages = $user->images()
            ->where('is_published', true)
            ->whereIn('privacy', ['public', 'unlisted'])
            ->orderBy('created_at', 'desc')
            ->take(12)
            ->get();

        // Calculate public stats
        $stats = [];
        if ($user->show_stats_publicly || auth()->id() === $user->id) {
            $stats = [
                'images' => $user->images()->where('is_published', true)->count(),
                'albums' => $user->albums()->where('is_published', true)->count(),
                'views' => $user->images()->sum('views_count'),
                'likes' => $user->images()->sum('likes_count'),
            ];
        }

        return Inertia::render('Profile/Show', [
            'profileUser' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->show_email_publicly ? $user->email : null,
                'avatar_path' => $user->avatar_path,
                'bio' => $user->bio,
                'website' => $user->website,
                'social_links' => $user->social_links,
                'created_at' => $user->created_at,
                'show_stats_publicly' => $user->show_stats_publicly,
            ],
            'publicAlbums' => $publicAlbums,
            'publicImages' => $publicImages,
            'stats' => $stats,
            'isOwnProfile' => auth()->id() === $user->id,
        ]);
    }

    /**
     * Update the user's profile information.
     */
   public function update(ProfileUpdateRequest $request): RedirectResponse
{
    $user = $request->user();
    $validated = $request->validated();

    // Handle social_links properly
    if (isset($validated['social_links'])) {
        // Filter out empty values
        $validated['social_links'] = array_filter($validated['social_links']);
        if (empty($validated['social_links'])) {
            $validated['social_links'] = null;
        }
    }

    $user->fill($validated);

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    $user->save();

    return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
}

    /**
     * Upload user avatar
     */
    public function uploadAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => 'required|image|max:2048', // 2MB max
        ]);

        $user = $request->user();

        // Delete old avatar if exists
        if ($user->avatar_path) {
            Storage::disk('minio')->delete($user->avatar_path);
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'minio');
        
        $user->update(['avatar_path' => $path]);

        return Redirect::route('profile.edit')->with('success', 'Avatar updated successfully.');
    }

    /**
     * Update privacy settings
     */
    public function updatePrivacy(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_visibility' => 'required|in:public,private',
            'show_email_publicly' => 'boolean',
            'show_stats_publicly' => 'boolean',
        ]);

        $request->user()->update($request->only([
            'profile_visibility',
            'show_email_publicly', 
            'show_stats_publicly'
        ]));

        return Redirect::route('profile.edit')->with('success', 'Privacy settings updated successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Delete user avatar
        if ($user->avatar_path) {
            Storage::disk('minio')->delete($user->avatar_path);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
