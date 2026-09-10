<?php

namespace App\Services;

use App\Models\ActionPlan;
use App\Models\ActionPlanRevision;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

final class ActionPlanRevisionService
{
    public function snapshot(ActionPlan $plan, User $actor): ActionPlanRevision
    {
        Gate::forUser($actor)->authorize('view', $plan->business);

        $version = ((int) $plan->revisions()->max('version')) + 1;
        $snapshot = [
            'plan' => $plan->only(['id', 'business_id', 'name', 'status']),
            'actions' => $plan->actions()->get()->map(fn ($action): array => $action->only([
                'id', 'priority_id', 'recommendation_id', 'title', 'status', 'resolution_reason',
            ]))->values()->all(),
        ];

        return $plan->revisions()->create([
            'version' => $version,
            'created_by' => $actor->id,
            'snapshot' => $snapshot,
        ]);
    }
}
