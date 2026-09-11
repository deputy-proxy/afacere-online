<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Business;
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
            'user' => $user->only(['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at']),
            'profile' => $user->profile?->only(['id', 'user_id', 'bio', 'phone', 'locale', 'created_at', 'updated_at']),
            'businesses' => $user->businesses->map(fn (Business $business): array => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'description' => $business->description,
                'website' => $business->website,
                'stage' => $business->stage->value,
                'profile' => $business->profile,
                'context' => $business->context,
                'preferences' => $business->preferences,
                'membership' => DB::table('business_user')
                    ->where('business_id', $business->id)
                    ->where('user_id', $user->id)
                    ->first(['role', 'joined_at']),
            ])->values()->all(),
            'data_requests' => DB::table('data_requests')
                ->where('user_id', $user->id)
                ->orderBy('requested_at')
                ->get(['id', 'type', 'status', 'requested_at', 'completed_at'])
                ->map(static fn (object $request): array => [
                    'id' => (int) $request->id,
                    'type' => $request->type,
                    'status' => $request->status,
                    'requested_at' => $request->requested_at,
                    'completed_at' => $request->completed_at,
                ])
                ->all(),
        ];
    }

    public function requestDeletion(User $user): int
    {
        return DB::transaction(function () use ($user): int {
            $existing = DB::table('data_requests')
                ->where('user_id', $user->id)
                ->where('type', 'deletion')
                ->where('status', 'pending')
                ->lockForUpdate()
                ->value('id');

            if ($existing !== null) {
                return (int) $existing;
            }

            $now = now();
            $id = (int) DB::table('data_requests')->insertGetId([
                'user_id' => $user->id,
                'type' => 'deletion',
                'status' => 'pending',
                'requested_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            AuditLog::create([
                'actor_id' => $user->id,
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'action' => 'data_deletion_requested',
                'context' => ['data_request_id' => $id],
                'occurred_at' => $now,
            ]);

            return $id;
        });
    }
}
