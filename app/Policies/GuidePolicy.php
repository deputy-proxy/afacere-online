<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Guide;
use App\Models\User;

class GuidePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Guide $guide): bool
    {
        return $guide->getAttribute('status') === 'published' || $this->canManage($user, $guide);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Guide $guide): bool
    {
        return $this->canManage($user, $guide);
    }

    public function publish(User $user, Guide $guide): bool
    {
        return $this->canManage($user, $guide);
    }

    private function canManage(User $user, Guide $guide): bool
    {
        return $user->isAdmin() || $guide->author_id === $user->id;
    }
}
