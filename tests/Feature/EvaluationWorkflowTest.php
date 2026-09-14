<?php

declare(strict_types=1);

use App\Enums\EvaluationStatus;
use App\Livewire\Business\EvaluationDiagnosis;
use App\Livewire\Business\EvaluationWizard;
use App\Models\Business;
use App\Models\EvaluationVersion;
use App\Models\Recommendation;
use App\Models\User;
use Livewire\Livewire;

function evaluationFixture(): array
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

    $second = $version->sections()->create(['position' => 2, 'key' => 'second', 'title' => 'Second']);
    $second->questions()->create([
        'position' => 1,
        'key' => 'channels',
        'type' => 'checkbox',
        'prompt' => 'Channels used?',
        'required' => true,
        'options' => ['email', 'social'],
        'validation_rules' => ['array', 'min:1'],
    ]);

    return [$user, $business, $version];
}

it('walks through every question, preserves answers, and resumes at the first unanswered question', function (): void {
    [$user, $business, $version] = evaluationFixture();
    $this->actingAs($user);

    $component = Livewire::test(EvaluationWizard::class)
        ->assertSet('sectionIndex', 0)
        ->assertSet('questionIndex', 0)
        ->set('answer', 'Example business')
        ->call('saveAndNext')
        ->assertSet('questionIndex', 1)
        ->set('answer', 'web')
        ->call('saveAndNext')
        ->assertSet('sectionIndex', 1)
        ->assertSet('questionIndex', 0)
        ->set('answer', ['email'])
        ->call('previous')
        ->assertSet('sectionIndex', 0)
        ->assertSet('questionIndex', 1)
        ->assertSet('answer', 'web');

    expect($business->evaluations()->first()->answers()->pluck('question_key')->all())
        ->toBe(['name', 'channel']);

    $component->set('answer', 'web')->call('saveAndNext')->set('answer', ['social'])->call('saveAndNext');

    expect($business->evaluations()->first()->fresh()->status)->toBe(EvaluationStatus::Completed);

    Livewire::test(EvaluationWizard::class)
        ->assertSet('sectionIndex', 1)
        ->assertSet('questionIndex', 0)
        ->assertSet('answer', ['social']);
});

it('renders diagnosis findings and exposes linked recommendations', function (): void {
    [$user, $business, $version] = evaluationFixture();
    $this->actingAs($user);

    $evaluation = $business->evaluations()->create([
        'evaluation_version_id' => $version->id,
        'status' => EvaluationStatus::Completed,
        'started_at' => now(),
        'completed_at' => now(),
    ]);
    $finding = $evaluation->findings()->create([
        'dimension' => 'first',
        'severity' => 'high',
        'title' => 'Channel coverage',
        'description' => 'Review channel coverage.',
        'context' => ['answered' => 1, 'total' => 2, 'version' => '1.0'],
    ]);
    Recommendation::query()->create([
        'business_id' => $business->id,
        'evaluation_id' => $evaluation->id,
        'evaluation_finding_id' => $finding->id,
        'status' => 'suggested',
        'title' => 'Improve channel coverage',
        'reason' => 'The finding indicates an opportunity.',
        'expected_outcome' => 'More complete coverage.',
        'context' => ['expected_outcome' => 'More complete coverage.'],
    ]);

    Livewire::test(EvaluationDiagnosis::class)
        ->assertSee('Channel coverage')
        ->assertSee('Improve channel coverage')
        ->assertSee('Make a priority');
});
