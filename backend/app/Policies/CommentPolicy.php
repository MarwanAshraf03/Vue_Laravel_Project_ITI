<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Admins can moderate (hide/delete) any comment.
     * Authors can delete their own comments.
     */
    public function moderate(User $user, Comment $comment): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->role === 'admin' || $comment->user_id === $user->id;
    }
}
