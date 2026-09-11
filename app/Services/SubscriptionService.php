<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Entitlement;
use App\Models\ProductPlan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Collection;

final class SubscriptionService
{
    /** @return Collection<int, ProductPlan> */
    public function plans(): Collection
    {
        return ProductPlan::query()->where('active', true)->orderBy('price_minor')->get();
    }

    public function current(User $user): ?Subscription
    {
        return Subscription::query()->with('plan')->where('user_id', $user->id)->latest('starts_at')->get()->first(
            static fn (Subscription $subscription): bool => $subscription->isActive(),
        );
    }

    /** @return array{key:string,name:string,price_minor:int,currency:string,entitlements:array<string,mixed>,business_limit:int|null} */
    public function planSummary(ProductPlan $plan): array
    {
        $entitlements = is_array($plan->entitlements) ? $plan->entitlements : [];
        $limit = $entitlements['business_limit'] ?? 1;

        return ['key' => (string) $plan->key, 'name' => (string) $plan->name, 'price_minor' => (int) $plan->price_minor, 'currency' => (string) $plan->currency, 'entitlements' => $entitlements, 'business_limit' => $limit === 'unlimited' ? null : (int) $limit];
    }

    /** Development billing adapter. Production payment integration can replace this boundary. */
    public function selectPlan(User $user, ProductPlan $plan): Subscription
    {
        $current = $this->current($user);
        if ($current !== null && $current->product_plan_id === $plan->id) {
            return $current;
        }

        Subscription::query()->where('user_id', $user->id)->where('status', 'active')->update(['status' => 'cancelled', 'ends_at' => now()]);
        $subscription = Subscription::query()->create(['user_id' => $user->id, 'product_plan_id' => $plan->id, 'status' => 'active', 'starts_at' => now(), 'ends_at' => null]);
        foreach ((array) $plan->entitlements as $key => $value) {
            Entitlement::query()->updateOrCreate(['user_id' => $user->id, 'key' => $key], ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value, 'expires_at' => null]);
        }

        return $subscription->load('plan');
    }

    public function businessLimit(User $user): ?int
    {
        return app(EntitlementService::class)->businessLimit($user);
    }

    public function canCreateBusiness(User $user): bool
    {
        $limit = $this->businessLimit($user);

        return $limit === null || $user->businesses()->count() < $limit;
    }
}
