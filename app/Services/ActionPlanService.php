<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ActionPlan;
use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class ActionPlanService
{
    public function createFromPriorities(Business $business, User $user): ActionPlan
    {
        abort_unless($business->members()->whereKey($user->id)->exists(), 403);

        $priorities = $business->priorities()->where('status', 'active')->orderBy('position')->get();
        if ($priorities->isEmpty()) {
            throw ValidationException::withMessages(['priorities' => 'Choose at least one active priority before creating an Action Plan.']);
        }

        return DB::transaction(function () use ($business, $priorities): ActionPlan {
            $version = ((int) $business->actionPlans()->max('version')) + 1;
            $plan = $business->actionPlans()->create([
                'version' => $version,
                'status' => 'active',
                'activated_at' => now(),
            ]);

            foreach ($priorities as $index => $priority) {
                $recommendation = $priority->recommendation;
                $plan->actions()->create([
                    'priority_id' => $priority->id,
                    'recommendation_id' => $priority->recommendation_id,
                    'position' => $index + 1,
                    'title' => $priority->title,
                    'description' => $recommendation?->recommended_action ?? $priority->reason,
                    'status' => 'recommended',
                ]);
            }

            return $plan->load('actions', 'actions.priority', 'actions.recommendation');
        });
    }
}
