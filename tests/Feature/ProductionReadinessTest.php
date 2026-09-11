<?php

declare(strict_types=1);

use App\Services\ProductionReadinessService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('reports production dependencies as configured after application setup', function (): void {
    $service = app(ProductionReadinessService::class);

    expect($service->isReady())->toBeTrue();
    expect($service->checks())->toHaveCount(9)->each->toBeTrue();
});
