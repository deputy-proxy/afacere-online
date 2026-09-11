<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MarketplaceProvider;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class TrustGovernanceService
{
    public function verifyProvider(MarketplaceProvider $provider, User $admin, string $reason): void
    {
        abort_unless($admin->isAdmin(), 403);
        if ($reason === '') {
            throw ValidationException::withMessages(['reason' => 'A reason is required.']);
        }
        $provider->update(['verification_status' => 'verified', 'verified_at' => now()]);
        DB::table('trust_actions')->insert(['actor_id' => $admin->id, 'target_type' => $provider::class, 'target_id' => $provider->id, 'action' => 'verified', 'reason' => $reason, 'created_at' => now(), 'updated_at' => now()]);
    }

    public function suspendProvider(MarketplaceProvider $provider, User $admin, string $reason): void
    {
        abort_unless($admin->isAdmin(), 403);
        if ($reason === '') {
            throw ValidationException::withMessages(['reason' => 'A reason is required.']);
        }
        $provider->update(['verification_status' => 'suspended']);
        DB::table('trust_actions')->insert(['actor_id' => $admin->id, 'target_type' => $provider::class, 'target_id' => $provider->id, 'action' => 'suspended', 'reason' => $reason, 'created_at' => now(), 'updated_at' => now()]);
    }
}