<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Entitlement;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\Subscription;
use App\Models\SubscriptionEvent;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class SubscriptionService
{
    public function provisionMvpPlans(): void
    {
        $product = Product::query()->firstOrCreate(['key' => 'afacere-online'], ['name' => 'afacere.online']);
        $plans = [
            ['key' => 'free', 'name' => 'Free', 'price_minor' => 0, 'entitlements' => ['business_limit' => '1', 'monitor' => false]],
            ['key' => 'single', 'name' => 'Single Business', 'price_minor' => 2900, 'entitlements' => ['business_limit' => '1', 'monitor' => true]],
            ['key' => 'unlimited', 'name' => 'Unlimited', 'price_minor' => 7900, 'entitlements' => ['business_limit' => 'unlimited', 'monitor' => true]],
        ];
        foreach ($plans as $plan) {
            ProductPlan::query()->updateOrCreate(['product_id' => $product->id, 'key' => $plan['key']], $plan + ['currency' => 'EUR', 'active' => true]);
        }
    }

    public function subscribe(User $user, ProductPlan $plan, ?string $idempotencyKey = null): Subscription
    {
        if (! $plan->active) {
            throw ValidationException::withMessages(['plan' => 'The selected plan is inactive.']);
        }
        $key = $idempotencyKey ?? 'subscription-'.$user->id.'-'.$plan->id.'-'.now()->timestamp;

        return DB::transaction(function () use ($user, $plan, $key): Subscription {
            $existing = SubscriptionEvent::query()->where('idempotency_key', $key)->first();
            if ($existing !== null) {
                return $existing->subscription()->firstOrFail();
            }
            $subscription = Subscription::query()->create(['user_id' => $user->id, 'product_plan_id' => $plan->id, 'status' => 'active', 'starts_at' => now()]);
            foreach ($plan->entitlements ?? [] as $entitlement => $value) {
                Entitlement::query()->updateOrCreate(['user_id' => $user->id, 'key' => $entitlement], ['value' => (string) $value, 'expires_at' => null]);
            }
            SubscriptionEvent::create(['subscription_id' => $subscription->id, 'type' => 'started', 'idempotency_key' => $key, 'payload' => ['plan' => $plan->key], 'occurred_at' => now()]);
            if ((int) $plan->price_minor > 0) {
                Invoice::create(['user_id' => $user->id, 'subscription_id' => $subscription->id, 'number' => 'INV-'.strtoupper(substr(hash('sha256', $key), 0, 12)), 'amount_minor' => $plan->price_minor, 'currency' => $plan->currency, 'status' => 'open', 'issued_at' => now(), 'due_at' => now()->addDays(14)]);
            }

            return $subscription;
        });
    }

    public function recordPayment(User $user, Subscription $subscription, int $amountMinor, string $idempotencyKey, ?string $providerReference = null): Payment
    {
        abort_unless($subscription->user_id === $user->id || $user->isAdmin(), 403);

        return DB::transaction(function () use ($user, $subscription, $amountMinor, $idempotencyKey, $providerReference): Payment {
            $payment = Payment::query()->firstOrCreate(['idempotency_key' => $idempotencyKey], ['user_id' => $user->id, 'subscription_id' => $subscription->id, 'amount_minor' => $amountMinor, 'currency' => 'EUR', 'status' => 'paid', 'provider_reference' => $providerReference, 'paid_at' => now()]);
            Invoice::query()->where('subscription_id', $subscription->id)->where('status', 'open')->latest()->first()?->update(['status' => 'paid', 'paid_at' => now()]);

            return $payment;
        });
    }

    public function cancel(Subscription $subscription, User $actor): Subscription
    {
        abort_unless($actor->isAdmin() || $subscription->user_id === $actor->id, 403);
        $subscription->update(['status' => 'cancelled', 'ends_at' => Carbon::now()]);

        return $subscription->fresh() ?? $subscription;
    }
}
