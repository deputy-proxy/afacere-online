<?php

namespace App\Services;

use App\Models\Entitlement;
use App\Models\User;
use Illuminate\Support\Carbon;

final class EntitlementService
{
    public function allows(User $user, string $key, mixed $expected = true): bool
    {
        $entitlement = Entitlement::query()
            ->where('user_id', $user->id)
            ->where('key', $key)
            ->first();

        if ($entitlement === null) {
            return false;
        }

        if ($entitlement->expires_at !== null && $entitlement->expires_at->isPast()) {
            return false;
        }

        return $expected === true
            ? filter_var($entitlement->value, FILTER_VALIDATE_BOOL)
            : $entitlement->value === (string) $expected;
    }

    public function businessLimit(User $user): ?int
    {
        $value = $this->value($user, 'business_limit');

        if ($value === null) {
            return 1;
        }

        return $value === 'unlimited' ? null : (int) $value;
    }

    private function value(User $user, string $key): ?string
    {
        $entitlement = Entitlement::query()->where('user_id', $user->id)->where('key', $key)->first();

        if ($entitlement === null || ($entitlement->expires_at !== null && $entitlement->expires_at->lte(Carbon::now()))) {
            return null;
        }

        return $entitlement->value;
    }
}
