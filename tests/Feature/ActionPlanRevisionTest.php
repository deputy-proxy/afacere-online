<?php

declare(strict_types=1);

use App\Models\ActionPlan;
use App\Models\Business;
use App\Models\User;
use App\Services\ActionPlanRevisionService;

it('preserves action plan snapshots as immutable revisions', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    $plan = ActionPlan::create(['business_id' => $business->id, 'name' => 'Growth plan', 'status' => 'active']);
    $plan->actions()->create(['title' => 'Talk to customers', 'status' => 'recommended']);

    $revision = app(ActionPlanRevisionService::class)->snapshot($plan, $user);

    expect($revision->version)->toBe(1)
        ->and($revision->snapshot['actions'])->toHaveCount(1);
});
