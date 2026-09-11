<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class TransactionService
{
    public function initiate(User $user, string $type, int $id, string $provider, int $amountCents, string $idempotencyKey): int
    {
        $existing = DB::table('commerce_transactions')->where('idempotency_key', $idempotencyKey)->value('id');
        if ($existing !== null) {
            return (int) $existing;
        }

        return (int) DB::table('commerce_transactions')->insertGetId([
            'user_id' => $user->id,
            'purchasable_type' => $type,
            'purchasable_id' => $id,
            'provider' => $provider,
            'status' => 'pending',
            'amount' => $amountCents / 100,
            'currency' => 'RON',
            'idempotency_key' => $idempotencyKey,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function confirm(int $transactionId, string $providerReference): void
    {
        $transaction = DB::table('commerce_transactions')->where('id', $transactionId)->first();
        abort_unless($transaction !== null, 404);
        if ($transaction->status === 'confirmed') {
            return;
        }
        if ($transaction->status !== 'pending') {
            throw ValidationException::withMessages(['transaction' => 'This transaction cannot be confirmed.']);
        }

        DB::transaction(function () use ($transactionId, $providerReference): void {
            DB::table('commerce_transactions')->where('id', $transactionId)->update(['status' => 'confirmed', 'provider_reference' => $providerReference, 'confirmed_at' => now(), 'updated_at' => now()]);
        });
    }
}
