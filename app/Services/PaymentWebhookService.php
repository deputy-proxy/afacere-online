<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;

final class PaymentWebhookService
{
    public function __construct(private readonly ObservabilityService $observability) {}

    /** @param array<string, mixed> $payload */
    public function claim(string $provider, string $externalId, array $payload): bool
    {
        $claimed = DB::table('payment_webhook_events')->insertOrIgnore([
            'provider' => $provider,
            'external_id' => $externalId,
            'payload' => json_encode($payload, JSON_THROW_ON_ERROR),
            'status' => 'received',
            'created_at' => now(),
            'updated_at' => now(),
        ]) === 1;

        $this->observability->record($claimed ? 'payment.webhook_received' : 'payment.webhook_duplicate', [
            'provider' => $provider,
            'external_id' => $externalId,
        ], $claimed ? 'info' : 'warning');

        return $claimed;
    }

    public function markProcessed(string $provider, string $externalId): void
    {
        DB::table('payment_webhook_events')
            ->where('provider', $provider)
            ->where('external_id', $externalId)
            ->update([
                'status' => 'processed',
                'processed_at' => now(),
                'updated_at' => now(),
            ]);

        $this->observability->record('payment.webhook_processed', [
            'provider' => $provider,
            'external_id' => $externalId,
        ]);
    }

    public function markFailed(string $provider, string $externalId, string $reason): void
    {
        DB::table('payment_webhook_events')
            ->where('provider', $provider)
            ->where('external_id', $externalId)
            ->update([
                'status' => 'failed',
                'updated_at' => now(),
            ]);

        $this->observability->record('payment.webhook_failed', [
            'provider' => $provider,
            'external_id' => $externalId,
            'reason' => $reason,
        ], 'error');
    }
}
