<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Business;
use App\Models\Guide;
use App\Models\GuideProgress;
use App\Models\GuideProgressEvent;
use App\Models\GuideStep;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class GuideExecutionService
{
    /** @return Builder<Guide> */
    public function published(): Builder
    {
        return Guide::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->with('sections.steps')
            ->orderBy('title');
    }

    /** @return Builder<Guide> */
    public function relevantForBusiness(Business $business, User $user): Builder
    {
        $this->authorizeBusiness($business, $user);

        return $this->published()
            ->where(function (Builder $query) use ($business): void {
                $query
                    ->whereHas('priorities', fn (Builder $priorities): Builder => $priorities->where('priorities.business_id', $business->id)->where('priorities.status', 'active'))
                    ->orWhereHas('actions.plan', fn (Builder $plans): Builder => $plans->where('action_plans.business_id', $business->id)->where('action_plans.status', 'active'));
            });
    }

    public function start(Guide $guide, Business $business, User $user): GuideProgress
    {
        $this->authorizeBusiness($business, $user);
        $this->ensurePublished($guide);

        $progress = GuideProgress::query()->firstOrCreate(
            [
                'guide_id' => $guide->id,
                'user_id' => $user->id,
                'business_id' => $business->id,
                'guide_version' => (int) $guide->getAttribute('version'),
            ],
            [
                'started_at' => now(),
                'completed_steps' => [],
            ],
        );

        if ($progress->wasRecentlyCreated) {
            $this->recordEvent($progress, 'started');
        }

        return $progress;
    }

    public function completeStep(Guide $guide, Business $business, User $user, int $stepId): GuideProgress
    {
        $this->authorizeBusiness($business, $user);
        $this->ensurePublished($guide);

        $step = GuideStep::query()
            ->whereKey($stepId)
            ->whereHas('section', fn (Builder $sections): Builder => $sections->where('guide_id', $guide->id))
            ->firstOrFail();

        return DB::transaction(function () use ($guide, $business, $user, $step): GuideProgress {
            $progress = $this->start($guide, $business, $user);
            $rawCompletedSteps = $progress->getAttribute('completed_steps');
            $completed = collect(is_array($rawCompletedSteps) ? $rawCompletedSteps : [])
                ->map(static fn ($id): int => (int) $id);
            $wasCompleted = $completed->contains($step->id);

            if (! $wasCompleted) {
                $completed->push($step->id);
            }

            $allStepIds = GuideStep::query()
                ->whereHas('section', fn (Builder $sections): Builder => $sections->where('guide_id', $guide->id))
                ->pluck('id');
            $isComplete = $allStepIds->isNotEmpty() && $allStepIds->every(fn ($id): bool => $completed->contains((int) $id));
            $nextStepId = $allStepIds->first(fn ($id): bool => ! $completed->contains((int) $id));

            $progress->update([
                'current_step_id' => $nextStepId,
                'completed_steps' => $completed->unique()->values()->all(),
                'completed_at' => $isComplete ? ($progress->completed_at ?? now()) : null,
            ]);

            if (! $wasCompleted) {
                $this->recordEvent($progress, 'step_completed', $step);
            }

            if ($isComplete && $progress->wasChanged('completed_at')) {
                $this->recordEvent($progress, 'completed');
            }

            return $progress->fresh('currentStep');
        });
    }

    public function progress(Guide $guide, Business $business, User $user): ?GuideProgress
    {
        $this->authorizeBusiness($business, $user);
        $this->ensurePublished($guide);

        return GuideProgress::query()
            ->where('guide_id', $guide->id)
            ->where('business_id', $business->id)
            ->where('user_id', $user->id)
            ->where('guide_version', (int) $guide->getAttribute('version'))
            ->with('currentStep')
            ->first();
    }

    private function recordEvent(GuideProgress $progress, string $type, ?GuideStep $step = null): void
    {
        GuideProgressEvent::query()->create([
            'guide_progress_id' => $progress->id,
            'guide_id' => $progress->guide_id,
            'business_id' => $progress->business_id,
            'user_id' => $progress->user_id,
            'guide_version' => $progress->guide_version,
            'event_type' => $type,
            'guide_step_id' => $step?->id,
            'occurred_at' => now(),
        ]);
    }

    private function ensurePublished(Guide $guide): void
    {
        if ($guide->getAttribute('status') !== 'published' || $guide->getAttribute('published_at') === null) {
            throw ValidationException::withMessages(['guide' => 'This guide is not currently published.']);
        }
    }

    private function authorizeBusiness(Business $business, User $user): void
    {
        abort_unless($business->members()->whereKey($user->id)->exists(), 403);
    }
}
