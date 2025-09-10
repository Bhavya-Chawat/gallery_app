<?php

namespace App\Policies;

use App\Models\Album;
use App\Models\User;

class AlbumPolicy
{
    /**
     * Determine whether the user can view any albums.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the album.
     */
    public function view(?User $user, Album $album): bool
    {
        // Public albums can be viewed by anyone
        if ($album->isPublic()) {
            return true;
        }

        // Unlisted albums can be viewed by anyone with the link
        if ($album->privacy === 'unlisted' && $album->is_published) {
            return true;
        }

        // Private albums require authentication
        if (!$user) {
            return false;
        }

        // Owner can always view their albums
        if ($album->owner_id === $user->id) {
            return true;
        }

        // Admin can view any album
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create albums.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('upload_images') || $user->hasPermission('manage_own_albums');
    }

    /**
     * Determine whether the user can update the album.
     */
    public function update(User $user, Album $album): bool
    {
        // Owner can edit their own albums
        if ($album->owner_id === $user->id && $user->hasPermission('manage_own_albums')) {
            return true;
        }

        // Admin can edit any album
        if ($user->hasPermission('manage_albums')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the album.
     */
    public function delete(User $user, Album $album): bool
    {
        // Owner can delete their own albums
        if ($album->owner_id === $user->id && $user->hasPermission('manage_own_albums')) {
            return true;
        }

        // Admin can delete any album
        if ($user->hasPermission('manage_albums')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the album.
     */
    public function restore(User $user, Album $album): bool
    {
        return $user->hasPermission('manage_albums');
    }

    /**
     * Determine whether the user can permanently delete the album.
     */
    public function forceDelete(User $user, Album $album): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage images in the album.
     */
    public function manageImages(User $user, Album $album): bool
    {
        // Owner can manage images in their albums
        if ($album->owner_id === $user->id) {
            return true;
        }

        // Admin can manage images in any album
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can publish/unpublish the album.
     */
    public function publish(User $user, Album $album): bool
    {
        // Owner can publish their own albums if they have permission
        if ($album->owner_id === $user->id && $user->hasPermission('publish_images')) {
            return true;
        }

        // Admin can publish any album
        if ($user->hasPermission('manage_albums')) {
            return true;
        }

        return false;
    }
}
