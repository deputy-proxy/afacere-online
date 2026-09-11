<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AnalyticsConversion;
use App\Models\AnalyticsEvent;
use App\Models\AnalyticsSnapshot;
use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class AnalyticsService
{
    public function __construct(private readonly AnalyticsRecorder $recorder) {}

    /** @param array<string, mixed> $payload */
    public function event(string $name, ?User $user = null, ?Business $business = null, array $payload = [], ?string $idempotencyKey = null): AnalyticsEvent
    {
        return $this->recorder->record($name, $user, $business, $payload, $idempotencyKey);
    }

    public function conversion(string $funnel, string $step, ?User $user = null, ?Business $business = null, ?string $idempotencyKey = null): AnalyticsConversion
    {
        $key = $idempotencyKey ?? hash('sha256', implode(':', [$funnel, $step, $user?->id ?? 0, $business?->id ?? 0]));

        return AnalyticsConversion::query()->firstOrCreate(['idempotency_key' => $key], ['user_id' => $user?->id, 'business_id' => $business?->id, 'funnel' => $funnel, 'step' => $step, 'occurred_at' => now()]);
    }

    public function snapshot(string $key, Carbon $start, Carbon $end): AnalyticsSnapshot
    {
        $events = AnalyticsEvent::query()->whereBetween('occurred_at', [$start, $end])->get();
        $counts = $events->groupBy('name')->map->count()->all();

        return AnalyticsSnapshot::query()->updateOrCreate(['key' => $key, 'period_start' => $start->toDateString(), 'period_end' => $end->toDateString()], ['payload' => ['events' => $counts, 'total' => $events->count()]]);
    }

    public function duplicateSafe(string $name, string $key): bool
    {
        return DB::transaction(fn (): bool => $this->recorder->record($name, null, null, [], $key)->wasRecentlyCreated);
    }
}
