<?php

declare(strict_types=1);

use App\Services\PerformanceMonitoringService;

it('registers query performance monitoring inside the Laravel application', function (): void {
    config()->set('performance.slow_query_ms', 250);

    app(PerformanceMonitoringService::class)->register();

    expect(config('performance.slow_query_ms'))->toBe(250);
});
