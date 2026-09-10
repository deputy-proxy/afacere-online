<?php

namespace App\Services;

use App\Models\Priority;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

final class RecommendationService
{
    public function accept(Recommendation $recommendation, User $actor): Recommendation
    {
        Gate::forUser($actor)->authorize('view', $recommendation->business);
        $this->assertSuggested($recommendation);

        $recommendation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
            'rejected_at' => null,
        ]);

        return $recommendation->fresh() ?? $recommendation;
    }

    public function reject(Recommendation $recommendation, User $actor, string $reason): Recommendation
    {
        Gate::forUser($actor)->authorize('view', $recommendation->business);
        $this->assertSuggested($recommendation);

        $context = $recommendation->context ?? [];
        $context['rejection_reason'] = $reason;
        $recommendation->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'accepted_at' => null,
            'context' => $context,
        ]);

        return $recommendation->fresh() ?? $recommendation;
    }

    public function prioritize(Recommendation $recommendation, User $actor, int $position, string $reason = ''): Priority
    {
        Gate::forUser($actor)->authorize('view', $recommendation->business);
        if ($position < 1) {
            throw ValidationException::withMessages(['position' => 'Position must be at least 1.']);
        }

        return DB::transaction(function () use ($recommendation, $position, $reason): Priority {
            $priority = $recommendation->priorities()->first();
            $priority ??= new Priority(['business_id' => $recommendation->business_id, 'recommendation_id' => $recommendation->id]);
            $priority->position = $position;
            $priority->title = $recommendation->title;
            $priority->reason = $reason !== '' ? $reason : $recommendation->reason;
            $priority->status = 'active';
            $priority->save();

            return $priority->fresh() ?? $priority;
        });
    }

    private function assertSuggested(Recommendation $recommendation): void
    {
        if ($recommendation->getAttribute('status') !== 'suggested') {
            throw ValidationException::withMessages(['recommendation' => 'Only suggested recommendations can be accepted or rejected.']);
        }
    }
}
