<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;

final class ProductAnalyticsService
{
    /** @return array<string, float> */
    public function funnelCounts(): array
    {
        return [
            'businesses' => (float) DB::table('businesses')->count(),
            'evaluations' => (float) DB::table('evaluations')->count(),
            'action_plans' => (float) DB::table('action_plans')->count(),
            'outcomes' => (float) DB::table('action_outcomes')->count(),
        ];
    }

    /** @param array<string, mixed> $dimensions */
    public function snapshot(string $metricKey, string $scopeType, ?int $scopeId, string $start, string $end, float $value, array $dimensions = []): void
    {
        DB::table('product_metric_snapshots')->updateOrInsert(
            ['metric_key' => $metricKey, 'scope_type' => $scopeType, 'scope_id' => $scopeId, 'period_start' => $start, 'period_end' => $end],
            ['value' => $value, 'dimensions' => json_encode($dimensions, JSON_THROW_ON_ERROR), 'updated_at' => now(), 'created_at' => now()],
        );
    }
}
