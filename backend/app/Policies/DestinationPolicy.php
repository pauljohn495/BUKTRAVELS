<?php

namespace App\Policies;

use App\Models\Destination;
use App\Models\User;

class DestinationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    public function create(User $user): bool
    {
        return $user->canManageContent();
    }

    public function update(User $user, Destination $destination): bool
    {
        return $user->canReviewContent() || ($user->role === 'content_editor' && $destination->status === 'draft');
    }

    public function delete(User $user, Destination $destination): bool
    {
        return $user->isAdmin();
    }

    public function publish(User $user, Destination $destination): bool
    {
        return $user->canReviewContent();
    }

    public function verify(User $user, Destination $destination): bool
    {
        return $user->canReviewContent();
    }
}
