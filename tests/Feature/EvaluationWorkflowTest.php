<?php

declare(strict_types=1);

use App\Enums\EvaluationStatus;
use App\Livewire\Business\EvaluationDiagnosis;
use App\Livewire\Business\EvaluationWizard;
use App\Models\Business;
use App\Models\EvaluationVersion;
use App\Models\User;
use Livewire\Livewire;

function makeEvaluationWorkflowFixture(): array
{
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);

    $version = EvaluationVersion::create([
        'key' => 'workflow-test',
        'name' => 'Workflow Test',
        'version' => '1.0',
        'is_active' => true,
        'definition' => [],
    ]);

    $first = $version->sections()->create(['position' => 1, 'key' => 'first', 'title' => 'First']);
    $first->questions()->create([
        'position' => 1,
        'key' => 'name',
        'type' => 'text',
        'prompt' => 'Business name?',
        'required' => true,
        'validation_rules' => ['string', 'max:255'],
    ]);
    $first->questions()->create([
        'position' => 2,
        'key' => 'channel',
        'type' => 'radio',
        'prompt' => 'Primary channel?',
        'required' => true,
        'options' => [['value' => 'web', 'label' => 'Website'], ['value' => 'shop', 'label' => 'Shop']],
        'validation_rules' => ['in:web,shop'],
    ]);

    return [$user, $business, $version];
}

it('walks through questions and preserves saved answers when navigating backwards', function (): void {
    [$user, $business] = makeEvaluationWorkflowFixture();
    $this->actingAs($user);

    Livewire::test(EvaluationWizard::class)
        ->assertSet('sectionIndex', 0)
        ->assertSet('questionIndex', 0)
        ->set('answer', 'Example business')
        ->call('saveAndNext')
        ->assertSet('questionIndex', 1)
        ->set('answer', 'web')
        ->call('previous')
        ->assertSet('questionIndex', 0)
        ->assertSet('answer', 'Example business');

    expect($business->evaluations()->first()->answers()->pluck('question_key')->all())->toBe(['name']);
});

it('renders the completed diagnosis findings for the latest evaluation', function (): void {
    [$user, $business, $version] = makeEvaluationWorkflowFixture();
    $this->actingAs($user);

    $evaluation = $business->evaluations()->create([
        'evaluation_version_id' => $version->id,
        'status' => EvaluationStatus::Completed,
        'started_at' => now(),
        'completed_at' => now(),
    ]);
    $evaluation->findings()->create([
        'dimension' => 'first',
        'severity' => 'high',
        'title' => 'Channel coverage',
        'description' => 'Review channel coverage.',
        'context' => ['answered' => 1, 'total' => 2, 'version' => '1.0'],
    ]);

    Livewire::test(EvaluationDiagnosis::class)
        ->assertSee('Channel coverage')
        ->assertSee('Review channel coverage.')
        ->assertSee('1 of 2 questions assessed');
});
