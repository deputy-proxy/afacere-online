<?php

namespace App\Actions;

use App\Enums\BusinessStage;
use App\Models\Business;
use App\Models\BusinessStageHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use DomainException;

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

            if (! $this->isMember($business, $actor)) {
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

            return $business->fresh();
        });
    }

    private function isMember(Business $business, User $actor): bool
    {
        return $business->members()->whereKey($actor->id)->exists();
    }
}
