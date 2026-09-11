<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('exposes a machine-readable readiness endpoint', function (): void {
    $response = $this->getJson('/health/ready');

    $response->assertOk()
        ->assertJsonPath('status', 'ready')
        ->assertJsonStructure(['status', 'checks']);
});
