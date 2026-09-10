<?php

namespace App\Services;

use App\Models\AnalyticsEvent;
use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Carbon;

final class AnalyticsRecorder
{
    /** @param array<string, mixed> $payload */
    public function record(string $name, ?User $user = null, ?Business $business = null, array $payload = [], ?string $idempotencyKey = null): AnalyticsEvent
    {
        if ($idempotencyKey !== null) {
            $existing = AnalyticsEvent::query()->where('idempotency_key', $idempotencyKey)->first();
            if ($existing !== null) {
                return $existing;
            }
        }

        return AnalyticsEvent::create([
            'name' => $name,
            'user_id' => $user?->id,
            'business_id' => $business?->id,
            'idempotency_key' => $idempotencyKey,
            'payload' => $payload,
            'occurred_at' => Carbon::now(),
        ]);
    }
}
