<?php

namespace App\Actions;

use App\Models\Business;
use App\Models\User;
use App\Services\EntitlementService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class CreateBusiness
{
    public function __construct(private readonly EntitlementService $entitlements) {}

    /** @param array{name:string, description?:string|null, website?:string|null} $attributes */
    public function execute(User $user, array $attributes): Business
    {
        return DB::transaction(function () use ($user, $attributes): Business {
            $limit = $this->entitlements->businessLimit($user);
            $current = $user->businesses()->count();

            if ($limit !== null && $current >= $limit) {
                throw ValidationException::withMessages(['business' => 'Your current plan does not allow another business.']);
            }

            $name = $attributes['name'];
            $business = Business::create([
                ...$attributes,
                'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
                'stage' => 'idea',
                'profile' => [],
                'preferences' => [],
            ]);

            $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => now()]);

            return $business;
        });
    }
}
