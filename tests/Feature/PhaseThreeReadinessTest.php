<?php

declare(strict_types=1);

use App\Services\PhaseThreeReadinessService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('reports the phase three integration surface as ready after migrations', function (): void {
    $service = app(PhaseThreeReadinessService::class);

    expect($service->isReady())->toBeTrue();
    expect($service->checks())->toHaveCount(8)->each->toBeTrue();
});