<?php

declare(strict_types=1);

use App\Actions\TransitionAction;
use App\Enums\ActionStatus;
use App\Livewire\Business\ActionPlan as ActionPlanComponent;
use App\Models\Action;
use App\Models\ActionEvidence;
use App\Models\ActionOutcome;
use App\Models\ActionPlan;
use App\Models\AuditLog;
use App\Models\Business;
use App\Models\Priority;
use App\Models\Recommendation;
use App\Models\User;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function makeActionPlanWorkflowFixture(): array
{
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    $recommendation = Recommendation::factory()->create(['business_id' => $business->id]);
    Priority::query()->create([
        'business_id' => $business->id,
        'recommendation_id' => $recommendation->id,
        'position' => 1,
        'title' => 'Improve customer discovery',
        'reason' => 'Customer discovery is a current priority.',
        'status' => 'active',
    ]);

    return [$user, $business];
}

it('creates a plan, exposes valid transitions, records completion data, and preserves history', function (): void {
    [$user, $business] = makeActionPlanWorkflowFixture();
    $this->actingAs($user);

    $component = Livewire::test(ActionPlanComponent::class)
        ->call('createPlan')
        ->assertSee('Version 1')
        ->assertSee('Recommended');

    $plan = $business->actionPlans()->firstOrFail();
    $action = $plan->actions()->firstOrFail();

    $component->call('updateStatus', $action->id, 'accepted')
        ->assertSee('Accepted')
        ->call('updateStatus', $action->id, 'active')
        ->assertSee('Active')
        ->call('beginCompletion', $action->id)
        ->set('outcome', 'Spoke with five customers and documented the findings.')
        ->set('evidence', 'customer-interviews.md')
        ->call('complete', $action->id)
        ->assertSee('Completed')
        ->assertSee('Spoke with five customers');

    $completedAction = Action::query()->findOrFail($action->id);

    expect($completedAction->status)->toBe(ActionStatus::Completed)
        ->and(ActionOutcome::query()->where('action_id', $action->id)->value('summary'))->toBe('Spoke with five customers and documented the findings.')
        ->and(ActionEvidence::query()->where('action_id', $action->id)->count())->toBe(1)
        ->and(AuditLog::query()->where('subject_type', Action::class)->where('subject_id', $action->id)->count())->toBe(3)
        ->and($plan->revisions()->count())->toBe(4);
});

it('requires an outcome before completing an active action', function (): void {
    [$user, $business] = makeActionPlanWorkflowFixture();
    $this->actingAs($user);
    $plan = ActionPlan::query()->create(['business_id' => $business->id, 'version' => 1, 'status' => 'active']);
    $action = $plan->actions()->create(['position' => 1, 'title' => 'Complete me', 'status' => ActionStatus::Active]);

    Livewire::test(ActionPlanComponent::class)
        ->set('planId', $plan->id)
        ->call('complete', $action->id)
        ->assertHasErrors(['outcome']);

    expect(Action::query()->findOrFail($action->id)->status)->toBe(ActionStatus::Active)
        ->and(ActionOutcome::query()->where('action_id', $action->id)->exists())->toBeFalse();
});

it('requires a reason before skipping or blocking an action', function (): void {
    [$user, $business] = makeActionPlanWorkflowFixture();
    $this->actingAs($user);
    $plan = ActionPlan::query()->create(['business_id' => $business->id, 'version' => 1, 'status' => 'active']);
    $action = $plan->actions()->create(['position' => 1, 'title' => 'Needs a reason', 'status' => ActionStatus::Recommended]);

    Livewire::test(ActionPlanComponent::class)
        ->set('planId', $plan->id)
        ->call('beginResolution', $action->id, 'skipped')
        ->call('resolveAction')
        ->assertHasErrors(['resolutionReason']);

    expect(Action::query()->findOrFail($action->id)->status)->toBe(ActionStatus::Recommended);
});

it('keeps invalid and unauthorized transitions rejected by the domain action', function (): void {
    [$user, $business] = makeActionPlanWorkflowFixture();
    $plan = ActionPlan::query()->create(['business_id' => $business->id, 'version' => 1, 'status' => 'active']);
    $action = $plan->actions()->create(['position' => 1, 'title' => 'Protected action', 'status' => ActionStatus::Completed]);

    expect(fn () => app(TransitionAction::class)->execute($action, ActionStatus::Active, $user))
        ->toThrow(DomainException::class);

    $outsider = User::factory()->create();
    expect(fn () => app(TransitionAction::class)->execute($action, ActionStatus::Completed, $outsider))
        ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});
