<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;

final class PaymentWebhookService
{
    /** @param array<string, mixed> $payload */
    public function claim(string $provider, string $externalId, array $payload): bool
    {
        return DB::table('payment_webhook_events')->insertOrIgnore([
            'provider' => $provider,
            'external_id' => $externalId,
            'payload' => json_encode($payload, JSON_THROW_ON_ERROR),
            'status' => 'received',
            'created_at' => now(),
            'updated_at' => now(),
        ]) === 1;
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
    }
}
