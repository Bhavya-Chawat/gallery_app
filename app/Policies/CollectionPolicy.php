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
        if ($collection->privacy === 'public' && $collection->is_published) {
            return true;
        }
        
        // Unlisted collections can be viewed by anyone with the link
        if ($collection->privacy === 'unlisted' && $collection->is_published) {
            return true;
        }

        // Private collections only by owner or admin
        if (!$user) {
            return false;
        }

        return $user->id === $collection->curator_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create collections.
     */
    public function create(User $user): bool
    {
        // Allow all authenticated users to create collections
        return true;
    }

    /**
     * Determine whether the user can update the collection.
     */
    public function update(User $user, Collection $collection): bool
    {
        return $user->id === $collection->curator_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the collection.
     */
    public function delete(User $user, Collection $collection): bool
    {
        return $user->id === $collection->curator_id || $user->hasRole('admin');
    }
}
    