<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Business;
use App\Models\BusinessDocument;
use App\Models\DataRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

final class DataDeletionService
{
    /** @return array{status: string, deleted_businesses: int, deleted_documents: int} */
    public function execute(DataRequest $request, User $actor): array
    {
        if ($request->type !== DataRequest::TYPE_DELETION || $request->status !== DataRequest::STATUS_APPROVED) {
            throw new RuntimeException('Only approved deletion requests can be executed.');
        }

        $user = $request->user()->first();
        if (! $user instanceof User) {
            throw new RuntimeException('The deletion request has no active user.');
        }

        $businesses = $user->businesses()->withCount('members')->get();
        $ownedBusinessIds = $businesses
            ->filter(fn (Business $business): bool => (int) $business->members_count === 1)
            ->pluck('id')
            ->all();
        $businessIds = $businesses->pluck('id')->all();
        $documents = BusinessDocument::query()
            ->where('uploaded_by', $user->id)
            ->orWhereIn('business_id', $ownedBusinessIds)
            ->get();

        $request->forceFill([
            'status' => DataRequest::STATUS_PROCESSING,
            'started_at' => now(),
            'reviewed_at' => $request->reviewed_at ?? now(),
            'reviewed_by' => $request->reviewed_by ?? $actor->id,
            'updated_at' => now(),
        ])->save();

        try {
            DB::transaction(function () use ($user, $ownedBusinessIds, $businessIds, $documents, $request, $actor): void {
                foreach ($documents as $document) {
                    $disk = Storage::disk($document->disk);
                    if ($document->path !== '' && $disk->exists($document->path)) {
                        $disk->delete($document->path);
                    }
                    $document->delete();
                }

                foreach ($ownedBusinessIds as $businessId) {
                    Business::query()->whereKey($businessId)->delete();
                }

                if ($businessIds !== []) {
                    DB::table('business_members')
                        ->where('user_id', $user->id)
                        ->whereIn('business_id', $businessIds)
                        ->delete();
                }

                AuditLog::create([
                    'actor_id' => $actor->id,
                    'subject_type' => User::class,
                    'subject_id' => $user->id,
                    'action' => 'data_deletion_started',
                    'context' => ['data_request_id' => $request->id],
                    'occurred_at' => now(),
                ]);

                User::destroy($user->id);
            });

            if (User::query()->find($user->id) !== null) {
                throw new RuntimeException('User record still exists after deletion.');
            }

            $request->refresh();
            $request->forceFill([
                'status' => DataRequest::STATUS_COMPLETED,
                'completed_at' => now(),
                'updated_at' => now(),
                'retention' => [
                    'financial_records' => 'retained with user reference removed',
                    'audit_records' => 'retained with actor reference removed',
                ],
            ])->save();

            AuditLog::create([
                'actor_id' => $actor->id,
                'subject_type' => DataRequest::class,
                'subject_id' => $request->id,
                'action' => 'data_deletion_completed',
                'context' => ['data_request_id' => $request->id],
                'occurred_at' => now(),
            ]);

            return [
                'status' => DataRequest::STATUS_COMPLETED,
                'deleted_businesses' => count($ownedBusinessIds),
                'deleted_documents' => $documents->count(),
            ];
        } catch (\Throwable $exception) {
            $request->refresh();
            $request->forceFill([
                'status' => DataRequest::STATUS_FAILED,
                'failed_at' => now(),
                'failure_reason' => mb_substr($exception->getMessage(), 0, 1000),
                'updated_at' => now(),
            ])->save();

            AuditLog::create([
                'actor_id' => $actor->id,
                'subject_type' => DataRequest::class,
                'subject_id' => $request->id,
                'action' => 'data_deletion_failed',
                'context' => ['data_request_id' => $request->id],
                'occurred_at' => now(),
            ]);

            throw $exception;
        }
    }
}
