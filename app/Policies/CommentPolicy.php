<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Determine whether the user can view any comments.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the comment.
     */
    public function view(?User $user, Comment $comment): bool
    {
        // Approved comments can be viewed by anyone
        if ($comment->isApproved()) {
            return true;
        }

        // Non-approved comments require authentication
        if (!$user) {
            return false;
        }

        // Author can view their own comments
        if ($comment->user_id === $user->id) {
            return true;
        }

        // Moderators can view any comment
        if ($user->hasPermission('moderate_comments')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create comments.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('comment_on_images');
    }

    /**
     * Determine whether the user can update the comment.
     */
    public function update(User $user, Comment $comment): bool
    {
        // Users can edit their own comments within 15 minutes
        if ($comment->user_id === $user->id && $comment->created_at->diffInMinutes() <= 15) {
            return true;
        }

        // Moderators can edit any comment
        if ($user->hasPermission('moderate_comments')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the comment.
     */
    public function delete(User $user, Comment $comment): bool
    {
        // Users can delete their own comments
        if ($comment->user_id === $user->id) {
            return true;
        }

        // Moderators can delete any comment
        if ($user->hasPermission('moderate_comments')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the comment.
     */
    public function restore(User $user, Comment $comment): bool
    {
        return $user->hasPermission('moderate_comments');
    }

    /**
     * Determine whether the user can permanently delete the comment.
     */
    public function forceDelete(User $user, Comment $comment): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can moderate the comment.
     */
    public function moderate(User $user, Comment $comment): bool
    {
        return $user->hasPermission('moderate_comments');
    }

    /**
     * Determine whether the user can approve the comment.
     */
    public function approve(User $user, Comment $comment): bool
    {
        return $user->hasPermission('moderate_comments');
    }

    /**
     * Determine whether the user can reject the comment.
     */
    public function reject(User $user, Comment $comment): bool
    {
        return $user->hasPermission('moderate_comments');
    }
}
