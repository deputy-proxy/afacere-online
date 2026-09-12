<?php

declare(strict_types=1);

use App\Services\ObservabilityService;
use App\Services\PerformanceMonitoringService;

it('is an instantiable performance monitoring service', function (): void {
    expect(new PerformanceMonitoringService(app(ObservabilityService::class)))
        ->toBeInstanceOf(PerformanceMonitoringService::class);
});
