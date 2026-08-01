<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function update(User $user, Post $post): bool
    {
        return (int) $post->user_id === (int) $user->id || $this->isAdmin($user);
    }

    public function delete(User $user, Post $post): bool
    {
        return $this->update($user, $post);
    }

    private function isAdmin(User $user): bool
    {
        return in_array($user->user_type, ['admin', 'superadmin'], true);
    }
}
