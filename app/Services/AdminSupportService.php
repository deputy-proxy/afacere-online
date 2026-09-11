<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

final class AdminSupportService
{
    /** @return array<string, mixed> */
    public function summary(User $target): array
    {
        return [
            'user' => $target->only(['id', 'name', 'email', 'email_verified_at', 'created_at']),
            'businesses' => $target->businesses()->get()->toArray(),
            'pending_data_requests' => DB::table('data_requests')
                ->where('user_id', $target->id)
                ->where('status', 'pending')
                ->count(),
        ];
    }

    public function sendPasswordRecovery(User $actor, User $target): string
    {
        $status = Password::sendResetLink(['email' => $target->email]);

        AuditLog::create([
            'actor_id' => $actor->id,
            'subject_type' => User::class,
            'subject_id' => $target->id,
            'action' => 'admin_password_recovery_requested',
            'context' => ['status' => $status],
            'occurred_at' => now(),
        ]);

        return $status;
    }
}
