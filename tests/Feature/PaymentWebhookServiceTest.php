<?php

declare(strict_types=1);

use App\Services\PaymentWebhookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('claims a payment webhook only once', function (): void {
    $service = app(PaymentWebhookService::class);

    expect($service->claim('test-provider', 'event-123', ['amount' => 100]))->toBeTrue();
    expect($service->claim('test-provider', 'event-123', ['amount' => 100]))->toBeFalse();
    expect(DB::table('payment_webhook_events')->count())->toBe(1);
});
