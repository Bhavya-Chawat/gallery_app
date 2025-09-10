<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('manage_users');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Users can view their own profile
        if ($user->id === $model->id) {
            return true;
        }

        // Admin can view any user
        if ($user->hasPermission('manage_users')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('manage_users');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Users can update their own profile (limited fields)
        if ($user->id === $model->id) {
            return true;
        }

        // Admin can update any user
        if ($user->hasPermission('manage_users')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Users cannot delete themselves
        if ($user->id === $model->id) {
            return false;
        }

        // Admin can delete any user (except other admins)
        if ($user->hasPermission('manage_users') && !$model->hasRole('admin')) {
            return true;
        }

        // Super admin can delete anyone including admins
        if ($user->hasRole('admin') && $user->hasPermission('manage_roles')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasPermission('manage_users');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasRole('admin') && $user->hasPermission('manage_users');
    }

    /**
     * Determine whether the user can assign roles.
     */
    public function assignRole(User $user, User $model): bool
    {
        // Cannot assign roles to yourself
        if ($user->id === $model->id) {
            return false;
        }

        return $user->hasPermission('manage_roles');
    }

    /**
     * Determine whether the user can manage storage quota.
     */
    public function manageStorage(User $user, User $model): bool
    {
        return $user->hasPermission('manage_users');
    }

    /**
     * Determine whether the user can impersonate another user.
     */
    public function impersonate(User $user, User $model): bool
    {
        // Cannot impersonate yourself
        if ($user->id === $model->id) {
            return false;
        }

        // Only admin can impersonate
        if (!$user->hasRole('admin')) {
            return false;
        }

        // Cannot impersonate other admins
        if ($model->hasRole('admin')) {
            return false;
        }

        return true;
    }
}
