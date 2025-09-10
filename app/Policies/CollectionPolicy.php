<?php

namespace App\Policies;

use App\Models\Collection;
use App\Models\User;

class CollectionPolicy
{
    /**
     * Determine whether the user can view any collections.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the collection.
     */
    public function view(?User $user, Collection $collection): bool
    {
        // Public collections can be viewed by anyone
        if ($collection->isPublic()) {
            return true;
        }

        // Unlisted collections can be viewed by anyone with the link
        if ($collection->privacy === 'unlisted' && $collection->is_published) {
            return true;
        }

        // Private collections require authentication
        if (!$user) {
            return false;
        }

        // Curator can always view their collections
        if ($collection->curator_id === $user->id) {
            return true;
        }

        // Admin can view any collection
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create collections.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_collections') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the collection.
     */
    public function update(User $user, Collection $collection): bool
    {
        // Curator can edit their own collections
        if ($collection->curator_id === $user->id) {
            return true;
        }

        // Admin can edit any collection
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the collection.
     */
    public function delete(User $user, Collection $collection): bool
    {
        // Curator can delete their own collections
        if ($collection->curator_id === $user->id) {
            return true;
        }

        // Admin can delete any collection
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the collection.
     */
    public function restore(User $user, Collection $collection): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the collection.
     */
    public function forceDelete(User $user, Collection $collection): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage items in the collection.
     */
    public function manageItems(User $user, Collection $collection): bool
    {
        // Curator can manage items in their collections
        if ($collection->curator_id === $user->id) {
            return true;
        }

        // Admin can manage items in any collection
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }
}
