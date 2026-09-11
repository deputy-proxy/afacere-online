<?php

declare(strict_types=1);

use App\Services\ProductAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('produces canonical funnel counts from authoritative records', function (): void {
    DB::table('businesses')->insert(['name' => 'B', 'stage' => 'idea', 'created_at' => now(), 'updated_at' => now()]);
    $counts = app(ProductAnalyticsService::class)->funnelCounts();
    expect($counts['businesses'])->toBe(1.0);
});

it('upserts metric snapshots deterministically', function (): void {
    $service = app(ProductAnalyticsService::class);
    $service->snapshot('activation', 'platform', null, '2026-09-01', '2026-09-30', 10.0);
    $service->snapshot('activation', 'platform', null, '2026-09-01', '2026-09-30', 12.0);
    expect(DB::table('product_metric_snapshots')->count())->toBe(1);
    expect((float) DB::table('product_metric_snapshots')->value('value'))->toBe(12.0);
});
