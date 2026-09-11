<?php

declare(strict_types=1);

use App\Services\PerformanceMonitoringService;

it('is an instantiable performance monitoring service', function (): void {
    expect(new PerformanceMonitoringService)->toBeInstanceOf(PerformanceMonitoringService::class);
});
