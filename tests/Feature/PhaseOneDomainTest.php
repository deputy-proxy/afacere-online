<?php

use App\Actions\TransitionAction;
use App\Actions\TransitionBusinessStage;
use App\Enums\ActionStatus;
use App\Enums\BusinessStage;
use App\Models\Action;
use App\Models\ActionPlan;
use App\Models\Business;
use App\Models\BusinessStageHistory;
use App\Models\Evaluation;
use App\Models\EvaluationVersion;
use App\Models\User;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('a business can be created with a valid lifecycle stage and membership', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create(['stage' => BusinessStage::Idea]);

    $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => Carbon::now()]);

    expect($business->fresh()->stage)->toBe(BusinessStage::Idea)
        ->and($business->fresh()->members()->whereKey($user->id)->exists())->toBeTrue();
});

test('business stage transitions are controlled and historical', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create(['stage' => BusinessStage::Idea]);
    $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => Carbon::now()]);

    app(TransitionBusinessStage::class)->execute($business, BusinessStage::Validation, $user, 'Validated customer problem');

    expect($business->fresh()->stage)->toBe(BusinessStage::Validation)
        ->and(BusinessStageHistory::query()->where('business_id', $business->id)->count())->toBe(1);
});

test('invalid business stage transitions are rejected', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create(['stage' => BusinessStage::Idea]);
    $business->members()->attach($user->id, ['role' => 'owner']);

    expect(fn () => app(TransitionBusinessStage::class)->execute($business, BusinessStage::Growth, $user))
        ->toThrow(DomainException::class);
});

test('evaluation remains pinned to its version', function (): void {
    $business = Business::factory()->create();
    $versionOne = EvaluationVersion::query()->create([
        'key' => 'core', 'name' => 'Core Evaluation', 'version' => '1.0', 'is_active' => true, 'definition' => ['questions' => []],
    ]);
    $versionTwo = EvaluationVersion::query()->create([
        'key' => 'core-v2', 'name' => 'Core Evaluation', 'version' => '2.0', 'is_active' => false, 'definition' => ['questions' => ['new']],
    ]);

    $evaluation = Evaluation::query()->create(['business_id' => $business->id, 'evaluation_version_id' => $versionOne->id]);
    $versionTwo->update(['is_active' => true]);

    expect($evaluation->fresh()->evaluation_version_id)->toBe($versionOne->id)
        ->and($evaluation->fresh()->version->version)->toBe('1.0');
});

test('action lifecycle rejects invalid transitions', function (): void {
    expect(ActionStatus::Completed->canTransitionTo(ActionStatus::Active))->toBeFalse();
});

test('action lifecycle transition is audited', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user->id, ['role' => 'owner']);
    $plan = ActionPlan::query()->create(['business_id' => $business->id, 'version' => 1, 'status' => 'active']);
    $action = Action::query()->create(['action_plan_id' => $plan->id, 'position' => 1, 'title' => 'Test action', 'status' => ActionStatus::Recommended]);

    $updated = app(TransitionAction::class)->execute($action, ActionStatus::Accepted, $user);

    expect($updated->status)->toBe(ActionStatus::Accepted)
        ->and(DB::table('audit_logs')->where('subject_id', $action->id)->exists())->toBeTrue();
});
