<?php

declare(strict_types=1);

use App\Services\ObservabilityService;
use Illuminate\Support\Facades\Log;

it('records structured telemetry with request and deployment context', function (): void {
    config()->set('performance.deployment_id', 'test-deployment');
    Log::spy();

    $response = $this->withHeader('X-Request-ID', 'request-123');
    $response->get('/up');

    app(ObservabilityService::class)->record('test.event', [
        'business_id' => 42,
    ]);

    Log::shouldHaveReceived('info')->once()->withArgs(function (string $event, array $payload): bool {
        return $event === 'test.event'
            && $payload['deployment_id'] === 'test-deployment'
            && $payload['request_id'] === 'request-123'
            && $payload['context']['business_id'] === 42;
    });
});

it('redacts sensitive telemetry context recursively', function (): void {
    Log::spy();

    app(ObservabilityService::class)->record('security.test', [
        'password' => 'secret-value',
        'nested' => [
            'access_token' => 'token-value',
            'safe' => 'visible',
        ],
    ]);

    Log::shouldHaveReceived('info')->once()->withArgs(function (string $event, array $payload): bool {
        return $event === 'security.test'
            && $payload['context']['password'] === '[REDACTED]'
            && $payload['context']['nested']['access_token'] === '[REDACTED]'
            && $payload['context']['nested']['safe'] === 'visible';
    });
});
