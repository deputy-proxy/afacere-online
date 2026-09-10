<?php

namespace App\Actions;

use App\Enums\BusinessStage;
use App\Events\BusinessStageChanged;
use App\Models\Business;
use App\Models\BusinessStageHistory;
use App\Models\User;
use DomainException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class TransitionBusinessStage
{
    public function execute(Business $business, BusinessStage $next, User $actor, ?string $reason = null): Business
    {
        return DB::transaction(function () use ($business, $next, $actor, $reason): Business {
            $business->refresh();
            $current = $business->stage;

            if ($current === $next) {
                return $business;
            }

            if (! $business->members()->whereKey($actor->id)->exists()) {
                throw new DomainException('The actor is not a member of this business.');
            }

            if (! in_array($next, $current->allowedNext(), true)) {
                throw new DomainException(sprintf('Invalid business stage transition: %s -> %s.', $current->value, $next->value));
            }

            $business->update(['stage' => $next]);

            BusinessStageHistory::create([
                'business_id' => $business->id,
                'from_stage' => $current,
                'to_stage' => $next,
                'changed_by' => $actor->id,
                'reason' => $reason,
                'changed_at' => Carbon::now(),
            ]);

            BusinessStageChanged::dispatch($business, $current->value, $next->value);

            return $business->fresh() ?? $business;
        });
    }
}
