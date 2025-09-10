<?php

namespace App\Policies;

use App\Models\Image;
use App\Models\User;

class ImagePolicy
{
    /**
     * Determine whether the user can view any images.
     */
    public function viewAny(?User $user): bool
    {
        // Anyone can view public images
        return true;
    }

    /**
     * Determine whether the user can view the image.
     */
    public function view(?User $user, Image $image): bool
    {
        // Public images can be viewed by anyone
        if ($image->isPublic()) {
            return true;
        }

        // Unlisted images can be viewed by anyone with the link
        if ($image->privacy === 'unlisted' && $image->is_published) {
            return true;
        }

        // Private images require authentication
        if (!$user) {
            return false;
        }

        // Owner can always view their images
        if ($image->owner_id === $user->id) {
            return true;
        }

        // Admin can view any image
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create images.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('upload_images');
    }

    /**
     * Determine whether the user can update the image.
     */
    public function update(User $user, Image $image): bool
    {
        // Owner can edit their own images
        if ($image->owner_id === $user->id && $user->hasPermission('edit_own_images')) {
            return true;
        }

        // Admin and editors with permission can edit any image
        if ($user->hasPermission('edit_any_image')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the image.
     */
    public function delete(User $user, Image $image): bool
    {
        // Owner can delete their own images
        if ($image->owner_id === $user->id && $user->hasPermission('delete_own_images')) {
            return true;
        }

        // Admin can delete any image
        if ($user->hasPermission('delete_any_image')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the image.
     */
    public function restore(User $user, Image $image): bool
    {
        return $user->hasPermission('delete_any_image');
    }

    /**
     * Determine whether the user can permanently delete the image.
     */
    public function forceDelete(User $user, Image $image): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can download the image.
     */
    public function download(?User $user, Image $image): bool
    {
        // Check if downloads are allowed for this image
        if (!$image->allow_downloads) {
            return false;
        }

        // If image is public and downloads allowed
        if ($image->isPublic()) {
            return true;
        }

        // If image is unlisted/private, user must be authenticated
        if (!$user) {
            return false;
        }

        // Owner can always download
        if ($image->owner_id === $user->id) {
            return true;
        }

        // Admin can download any image
        if ($user->hasRole('admin')) {
            return true;
        }

        // For unlisted images, anyone with link can download
        if ($image->privacy === 'unlisted' && $image->is_published) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can publish/unpublish the image.
     */
    public function publish(User $user, Image $image): bool
    {
        // Owner can publish their own images if they have permission
        if ($image->owner_id === $user->id && $user->hasPermission('publish_images')) {
            return true;
        }

        // Admin can publish any image
        if ($user->hasPermission('edit_any_image')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can moderate the image.
     */
    public function moderate(User $user): bool
    {
        return $user->hasPermission('moderate_content');
    }
}
