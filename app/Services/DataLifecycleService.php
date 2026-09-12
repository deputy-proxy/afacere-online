<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Business;
use App\Models\DataRequest;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class DataLifecycleService
{
    /** @return array<string, mixed> */
    public function export(User $user): array
    {
        $user->load(['profile', 'businesses']);

        $dataRequests = [];
        foreach (DataRequest::query()
            ->where('user_id', $user->id)
            ->orderBy('requested_at')
            ->get(['id', 'type', 'status', 'requested_at', 'completed_at']) as $request) {
            $dataRequests[] = [
                'id' => $request->id,
                'type' => $request->type,
                'status' => $request->status,
                'requested_at' => $this->formatDate($request->requested_at),
                'completed_at' => $this->formatDate($request->completed_at),
            ];
        }

        return [
            'schema_version' => 1,
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
                'membership' => DB::table('business_members')
                    ->where('business_id', $business->id)
                    ->where('user_id', $user->id)
                    ->first(['role', 'joined_at']),
            ])->values()->all(),
            'data_requests' => $dataRequests,
        ];
    }

    public function requestDeletion(User $user): DataRequest
    {
        return DB::transaction(function () use ($user): DataRequest {
            $request = DataRequest::query()
                ->where('user_id', $user->id)
                ->where('type', DataRequest::TYPE_DELETION)
                ->whereIn('status', [DataRequest::STATUS_PENDING, DataRequest::STATUS_APPROVED, DataRequest::STATUS_PROCESSING])
                ->lockForUpdate()
                ->first();

            if ($request instanceof DataRequest) {
                return $request;
            }

            $now = now();
            $request = DataRequest::query()->create([
                'user_id' => $user->id,
                'type' => DataRequest::TYPE_DELETION,
                'status' => DataRequest::STATUS_PENDING,
                'requested_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            AuditLog::create([
                'actor_id' => $user->id,
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'action' => 'data_deletion_requested',
                'context' => ['data_request_id' => $request->id],
                'occurred_at' => $now,
            ]);

            return $request;
        });
    }

    public function approveDeletion(DataRequest $request, User $actor): DataRequest
    {
        return $this->transition($request, $actor, DataRequest::STATUS_APPROVED, 'data_deletion_approved');
    }

    public function rejectDeletion(DataRequest $request, User $actor): DataRequest
    {
        return $this->transition($request, $actor, DataRequest::STATUS_REJECTED, 'data_deletion_rejected');
    }

    private function transition(DataRequest $request, User $actor, string $status, string $auditAction): DataRequest
    {
        if ($request->type !== DataRequest::TYPE_DELETION || $request->status !== DataRequest::STATUS_PENDING) {
            throw new RuntimeException('Only pending deletion requests can be reviewed.');
        }

        $request->forceFill([
            'status' => $status,
            'reviewed_by' => $actor->id,
            'reviewed_at' => now(),
            'updated_at' => now(),
        ])->save();

        AuditLog::create([
            'actor_id' => $actor->id,
            'subject_type' => DataRequest::class,
            'subject_id' => $request->id,
            'action' => $auditAction,
            'context' => ['data_request_id' => $request->id],
            'occurred_at' => now(),
        ]);

        return $request->refresh();
    }

    private function formatDate(CarbonInterface|string|null $value): ?string
    {
        if ($value instanceof CarbonInterface) {
            return $value->toIso8601String();
        }

        return $value === null ? null : Carbon::parse($value)->toIso8601String();
    }
}
