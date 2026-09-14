<?php

declare(strict_types=1);

use App\Enums\ActionStatus;
use App\Livewire\Business\ActionPlan;
use App\Models\Action;
use App\Models\ActionEvidence;
use App\Models\ActionPlan as ActionPlanModel;
use App\Models\AuditLog;
use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

function actionPlanFor(User $user, ActionStatus $status = ActionStatus::Active): array
{
    $business = Business::factory()->create();
    $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => now()]);
    $plan = ActionPlanModel::query()->create([
        'business_id' => $business->id,
        'version' => 1,
        'status' => 'active',
    ]);
    $action = $plan->actions()->create([
        'position' => 1,
        'title' => 'Complete the next step',
        'status' => $status,
    ]);

    return [$business, $plan, $action];
}

test('action plan renders supported execution controls and history', function (): void {
    $user = User::factory()->create();
    [$business, $plan, $action] = actionPlanFor($user, ActionStatus::Accepted);
    $action->update(['accepted_at' => now()]);
    $action->auditLogs()->create([
        'actor_id' => $user->id,
        'action' => 'status_changed',
        'context' => ['from' => ActionStatus::Recommended->value, 'to' => ActionStatus::Accepted->value],
        'occurred_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(ActionPlan::class)
        ->assertSee('Start')
        ->assertSee('Skip')
        ->assertSee('Block')
        ->assertSee('Progress history')
        ->assertSee('recommended')
        ->assertSee('accepted');
});

test('active action requires an outcome before completion', function (): void {
    $user = User::factory()->create();
    [$business, $plan, $action] = actionPlanFor($user);

    Livewire::actingAs($user)
        ->test(ActionPlan::class)
        ->call('openCompletionForm', $action->id)
        ->call('complete')
        ->assertHasErrors(['outcome' => 'required']);

    expect($action->fresh()->status)->toBe(ActionStatus::Active);
});

test('completing an active action records outcome and evidence', function (): void {
    $user = User::factory()->create();
    [$business, $plan, $action] = actionPlanFor($user);

    Livewire::actingAs($user)
        ->test(ActionPlan::class)
        ->call('openCompletionForm', $action->id)
        ->set('outcome', 'Completed the customer interviews.')
        ->set('evidence', 'Interview notes: research/notes-01')
        ->call('complete');

    expect($action->fresh()->status)->toBe(ActionStatus::Completed)
        ->and($action->fresh()->outcome?->summary)->toBe('Completed the customer interviews.')
        ->and(ActionEvidence::query()->where('action_id', $action->id)->value('description'))->toBe('Interview notes: research/notes-01')
        ->and(AuditLog::query()->where('subject_id', $action->id)->where('action', 'status_changed')->exists())->toBeTrue();
});

test('action plan rejects an invalid transition through the livewire action', function (): void {
    $user = User::factory()->create();
    [$business, $plan, $action] = actionPlanFor($user, ActionStatus::Completed);

    expect(fn () => Livewire::actingAs($user)
        ->test(ActionPlan::class)
        ->call('updateStatus', $action->id, ActionStatus::Active->value))
        ->toThrow(DomainException::class);
});

test('action plan denies access to actions belonging to another business', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    [$business, $plan, $action] = actionPlanFor($otherUser);

    expect(fn () => Livewire::actingAs($user)
        ->test(ActionPlan::class)
        ->call('updateStatus', $action->id, ActionStatus::Completed->value))
        ->toThrow(HttpException::class);
});
