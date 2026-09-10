<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;

class BusinessPolicy
{
    public function view(User $user, Business $business): bool
    {
        return $this->hasRole($user, $business, ['owner', 'admin', 'member', 'viewer']);
    }

    public function create(User $user): bool
    {
        return $user->exists;
    }

    public function update(User $user, Business $business): bool
    {
        return $this->hasRole($user, $business, ['owner', 'admin', 'member']);
    }

    public function delete(User $user, Business $business): bool
    {
        return $this->hasRole($user, $business, ['owner', 'admin']);
    }

    private function hasRole(User $user, Business $business, array $roles): bool
    {
        return $business->members()
            ->whereKey($user->id)
            ->wherePivotIn('role', $roles)
            ->exists();
    }
}
