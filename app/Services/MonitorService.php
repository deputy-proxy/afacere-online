<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Business;
use App\Models\HealthIndicator;
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
        $this->authorizeManager($business, $user);
        abort_unless($user->isAdmin() || app(EntitlementService::class)->allows($user, 'monitor'), 403);
        abort_unless(in_array($cadence, ['daily', 'weekly', 'monthly'], true), 422);
        $configuration = MonitorConfiguration::query()->updateOrCreate(['business_id' => $business->id], ['enabled' => $enabled, 'cadence' => $cadence, 'next_check_in_at' => $enabled ? $this->next($cadence) : null]);
        $this->track($business, $user, 'monitor.configured', ['enabled' => $enabled, 'cadence' => $cadence]);
        return $configuration;
    }

    /** @param array<string, mixed> $responses */
    public function checkIn(Business $business, User $user, array $responses): MonitorCheckIn
    {
        $this->authorizeMember($business, $user);
        abort_unless($user->isAdmin() || app(EntitlementService::class)->allows($user, 'monitor'), 403);
        $configuration = $business->monitorConfiguration()->first();
        abort_unless($configuration?->enabled === true, 422);
        return DB::transaction(function () use ($business, $user, $responses, $configuration): MonitorCheckIn {
            $checkIn = MonitorCheckIn::create(['business_id' => $business->id, 'user_id' => $user->id, 'responses' => $responses, 'recorded_at' => Carbon::now()]);
            $configuration->update(['next_check_in_at' => $this->next($configuration->cadence)]);
            $this->recordHealthIndicators($business, $responses);
            $this->evaluateThresholds($business);
            $this->track($business, $user, 'monitor.check_in_completed', ['check_in_id' => $checkIn->id]);
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
            if (MonitorAlert::query()->where('business_id', $business->id)->where('monitor_threshold_id', $threshold->id)->whereNull('resolved_at')->exists()) {
                continue;
            }
            MonitorAlert::create(['business_id' => $business->id, 'monitor_threshold_id' => $threshold->id, 'type' => 'threshold', 'severity' => $threshold->severity, 'message' => "Metric {$threshold->metric_key} crossed its {$threshold->operator} threshold.", 'context' => ['value' => $value], 'triggered_at' => Carbon::now()]);
        }
    }

    public function summarize(Business $business, Carbon $start, Carbon $end): MonitorSummary
    {
        $checkIns = MonitorCheckIn::query()->where('business_id', $business->id)->whereBetween('recorded_at', [$start, $end])->orderBy('recorded_at')->get();
        $latest = $checkIns->last();
        return MonitorSummary::query()->updateOrCreate(['business_id' => $business->id, 'period_start' => $start->toDateString(), 'period_end' => $end->toDateString()], ['summary' => ['check_ins' => $checkIns->count(), 'latest' => $latest?->responses ?? [], 'trend' => $this->trend($checkIns->all())]]);
    }

    /** @param list<MonitorCheckIn> $checkIns */
    private function trend(array $checkIns): array
    {
        if (count($checkIns) < 2) {
            return [];
        }
        $first = $checkIns[0]->responses ?? [];
        $last = $checkIns[count($checkIns) - 1]->responses ?? [];
        $result = [];
        foreach (['revenue', 'customers', 'cash', 'confidence'] as $key) {
            if (!is_numeric($first[$key] ?? null) || !is_numeric($last[$key] ?? null)) {
                continue;
            }
            $result[$key] = (float) $last[$key] - (float) $first[$key];
        }
        return $result;
    }

    /** @param array<string, mixed> $responses */
    private function recordHealthIndicators(Business $business, array $responses): void
    {
        foreach (['revenue', 'customers', 'cash', 'confidence'] as $key) {
            if (!is_numeric($responses[$key] ?? null)) {
                continue;
            }
            HealthIndicator::create(['business_id' => $business->id, 'key' => $key, 'status' => $this->status($key, (float) $responses[$key]), 'value' => (float) $responses[$key], 'measured_at' => Carbon::now(), 'context' => ['source' => 'monitor_check_in']]);
        }
    }

    private function status(string $key, float $value): string
    {
        if (in_array($key, ['cash', 'confidence'], true)) {
            return $value >= 4 ? 'healthy' : ($value >= 3 ? 'watch' : 'critical');
        }
        return $value > 0 ? 'healthy' : 'critical';
    }

    private function authorizeManager(Business $business, User $user): void
    {
        abort_unless($user->isAdmin() || $business->members()->whereKey($user->id)->wherePivotIn('role', ['owner', 'admin'])->exists(), 403);
    }

    private function authorizeMember(Business $business, User $user): void
    {
        abort_unless($user->isAdmin() || $business->members()->whereKey($user->id)->exists(), 403);
    }

    private function next(string $cadence): Carbon
    {
        return match ($cadence) {
            'daily' => Carbon::now()->addDay(),
            'monthly' => Carbon::now()->addMonth(),
            default => Carbon::now()->addWeek(),
        };
    }

    private function passes(float $value, string $operator, float $threshold): bool
    {
        return match ($operator) {
            '>' => $value > $threshold,
            '>=' => $value >= $threshold,
            '<' => $value < $threshold,
            '<=' => $value <= $threshold,
            '=' => $value === $threshold,
            default => false,
        };
    }

    /** @param array<string, mixed> $payload */
    private function track(Business $business, User $user, string $type, array $payload): void
    {
        DB::table('domain_events')->insert(['actor_id' => $user->id, 'business_id' => $business->id, 'type' => $type, 'subject_type' => Business::class, 'subject_id' => $business->id, 'payload' => json_encode($payload, JSON_THROW_ON_ERROR), 'occurred_at' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
    }
}
