<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class DataLifecycleService
{
    /** @return array<string, mixed> */
    public function export(User $user): array
    {
        $user->load(['profile', 'businesses']);

        return [
            'exported_at' => now()->toIso8601String(),
            'user' => $user->toArray(),
            'profile' => $user->profile?->toArray(),
            'businesses' => $user->businesses->toArray(),
        ];
    }

    public function requestDeletion(User $user): int
    {
        $existing = DB::table('data_requests')
            ->where('user_id', $user->id)
            ->where('type', 'deletion')
            ->where('status', 'pending')
            ->value('id');

        if ($existing !== null) {
            return (int) $existing;
        }

        $id = (int) DB::table('data_requests')->insertGetId([
            'user_id' => $user->id,
            'type' => 'deletion',
            'status' => 'pending',
            'requested_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AuditLog::create([
            'actor_id' => $user->id,
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'action' => 'data_deletion_requested',
            'context' => ['data_request_id' => $id],
            'occurred_at' => now(),
        ]);

        return $id;
    }
}
