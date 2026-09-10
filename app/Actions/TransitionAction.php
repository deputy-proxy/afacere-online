<?php

namespace App\Actions;

use App\Enums\ActionStatus;
use App\Models\Action;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use DomainException;

final class TransitionAction
{
    public function execute(Action $action, ActionStatus $next, User $actor, ?string $reason = null): Action
    {
        return DB::transaction(function () use ($action, $next, $actor, $reason): Action {
            $action->refresh();
            $business = $action->plan()->firstOrFail()->business()->firstOrFail();
            abort_unless($business->members()->whereKey($actor->id)->exists(), 403);

            if (! $action->status->canTransitionTo($next)) {
                throw new DomainException(sprintf('Invalid action transition: %s -> %s.', $action->status->value, $next->value));
            }

            $attributes = ['status' => $next, 'resolution_reason' => $reason];
            if ($next === ActionStatus::Accepted) {
                $attributes['accepted_at'] = Carbon::now();
            } elseif ($next === ActionStatus::Active) {
                $attributes['started_at'] = Carbon::now();
            } elseif ($next === ActionStatus::Completed) {
                $attributes['completed_at'] = Carbon::now();
            }

            $action->update($attributes);

            AuditLog::create([
                'actor_id' => $actor->id,
                'subject_type' => Action::class,
                'subject_id' => $action->id,
                'action' => 'status_changed',
                'context' => ['from' => $action->getOriginal('status'), 'to' => $next->value, 'reason' => $reason],
                'occurred_at' => Carbon::now(),
            ]);

            return $action->fresh();
        });
    }
}
