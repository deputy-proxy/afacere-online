<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class BusinessContextService
{
    private const SESSION_KEY = 'afacere.current_business_id';

    public function forUser(User $user): array
    {
        return $user->businesses()
            ->orderBy('name')
            ->get()
            ->all();
    }

    public function current(User $user): ?Business
    {
        $businessId = Session::get(self::SESSION_KEY);

        if (is_int($businessId) || is_string($businessId)) {
            $business = $user->businesses()->whereKey($businessId)->first();

            if ($business !== null) {
                return $business;
            }
        }

        $business = $user->businesses()->orderBy('name')->first();

        if ($business !== null) {
            $this->select($business, $user);
        }

        return $business;
    }

    public function select(Business $business, User $user): Business
    {
        $this->ensureAccess($business, $user);
        Session::put(self::SESSION_KEY, $business->id);

        return $business;
    }

    public function create(User $user, string $name, ?string $description = null): Business
    {
        $name = trim($name);

        if ($name === '') {
            throw ValidationException::withMessages(['name' => 'Business name is required.']);
        }

        return DB::transaction(function () use ($user, $name, $description): Business {
            $business = Business::query()->create([
                'name' => $name,
                'slug' => $this->uniqueSlug($name),
                'description' => $description !== null ? trim($description) : null,
                'profile' => [],
                'context' => [],
                'preferences' => [],
            ]);

            $business->members()->attach($user->id, [
                'role' => 'owner',
                'joined_at' => now(),
            ]);

            $this->select($business, $user);

            return $business;
        });
    }

    public function ensureAccess(Business $business, User $user): void
    {
        abort_unless(
            $user->isAdmin() || $business->members()->whereKey($user->id)->exists(),
            403,
        );
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 2;

        while (Business::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
