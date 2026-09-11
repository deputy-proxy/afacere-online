<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Business;
use App\Models\MonitorAlert;
use App\Models\MonitorCheckIn;
use App\Models\MonitorConfiguration;
use App\Models\MonitorSummary;
use App\Models\MonitorThreshold;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class MonitorService
{
    public function configure(Business $business, User $user, bool $enabled = true, string $cadence = 'weekly'): MonitorConfiguration
    {
        abort_unless($user->isAdmin() || $business->members()->whereKey($user->id)->wherePivotIn('role', ['owner', 'admin'])->exists(), 403);
        abort_unless($user->isAdmin() || app(EntitlementService::class)->allows($user, 'monitor'), 403);
        abort_unless(in_array($cadence, ['daily', 'weekly', 'monthly'], true), 422);
        return MonitorConfiguration::query()->updateOrCreate(['business_id' => $business->id], ['enabled' => $enabled, 'cadence' => $cadence, 'next_check_in_at' => $this->next($cadence)]);
    }

    /** @param array<string, mixed> $responses */
    public function checkIn(Business $business, User $user, array $responses): MonitorCheckIn
    {
        abort_unless($user->isAdmin() || $business->members()->whereKey($user->id)->exists(), 403);
        $configuration = $business->monitorConfiguration()->first();
        abort_unless($configuration?->enabled === true, 422);
        return DB::transaction(function () use ($business, $user, $responses, $configuration): MonitorCheckIn {
            $checkIn = MonitorCheckIn::create(['business_id' => $business->id, 'user_id' => $user->id, 'responses' => $responses, 'recorded_at' => now()]);
            $configuration->update(['next_check_in_at' => $this->next($configuration->cadence)]);
            $this->evaluateThresholds($business);
            return $checkIn;
        });
    }

    public function evaluateThresholds(Business $business): void
    {
        foreach (MonitorThreshold::query()->where('business_id', $business->id)->where('enabled', true)->get() as $threshold) {
            $metric = $business->metrics()->where('key', $threshold->metric_key)->first();
            $value = $metric?->values()->latest('measured_at')->value('value');
            if ($value === null || ! $this->passes((float) $value, $threshold->operator, (float) $threshold->threshold)) {
                continue;
            }
            MonitorAlert::create(['business_id' => $business->id, 'monitor_threshold_id' => $threshold->id, 'type' => 'threshold', 'severity' => $threshold->severity, 'message' => "Metric {$threshold->metric_key} crossed its {$threshold->operator} threshold.", 'context' => ['value' => $value], 'triggered_at' => now()]);
        }
    }

    public function summarize(Business $business, Carbon $start, Carbon $end): MonitorSummary
    {
        abort_unless($business->exists, 404);
        $checkIns = MonitorCheckIn::query()->where('business_id', $business->id)->whereBetween('recorded_at', [$start, $end])->orderBy('recorded_at')->get();
        return MonitorSummary::query()->updateOrCreate(['business_id' => $business->id, 'period_start' => $start->toDateString(), 'period_end' => $end->toDateString()], ['summary' => ['check_ins' => $checkIns->count(), 'latest' => $checkIns->last()?->responses ?? []]]);
    }

    private function next(string $cadence): Carbon
    {
        return match ($cadence) { 'daily' => now()->addDay(), 'monthly' => now()->addMonth(), default => now()->addWeek() };
    }

    private function passes(float $value, string $operator, float $threshold): bool
    {
        return match ($operator) { '>' => $value > $threshold, '>=' => $value >= $threshold, '<' => $value < $threshold, '<=' => $value <= $threshold, '=' => $value === $threshold, default => false };
    }
}
