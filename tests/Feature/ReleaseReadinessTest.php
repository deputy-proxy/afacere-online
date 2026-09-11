<?php

declare(strict_types=1);

use App\Services\ReleaseReadinessService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('reports the complete release foundation as ready after migrations', function (): void {
    $service = app(ReleaseReadinessService::class);

    expect($service->isReady())->toBeTrue();
    expect($service->checks())->toHaveCount(6)->each->toBeTrue();
});
