<?php

use App\Enums\EvaluationStatus;
use App\Models\Business;
use App\Models\EvaluationVersion;
use App\Models\User;
use App\Services\EvaluationService;
use Illuminate\Validation\ValidationException;

it('pins evaluations to a version and persists structured answers', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    $version = EvaluationVersion::create([
        'key' => 'business-health',
        'name' => 'Business Health',
        'version' => '1.0',
        'is_active' => true,
        'definition' => [],
    ]);
    $section = $version->sections()->create(['position' => 1, 'key' => 'basics', 'title' => 'Basics']);
    $section->questions()->create([
        'position' => 1,
        'key' => 'revenue',
        'type' => 'number',
        'prompt' => 'Monthly revenue?',
        'required' => true,
        'validation_rules' => ['numeric', 'min:0'],
    ]);

    $service = app(EvaluationService::class);
    $evaluation = $service->create($business, $version, $user);
    $service->saveAnswers($evaluation, $user, ['revenue' => 1200]);
    $completed = $service->complete($evaluation, $user);

    expect($completed->status)->toBe(EvaluationStatus::Completed)
        ->and($completed->evaluation_version_id)->toBe($version->id)
        ->and($completed->answers()->where('question_key', 'revenue')->value('value'))->toBe(1200);
});

it('rejects incomplete evaluations', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    $version = EvaluationVersion::create([
        'key' => 'required-test',
        'name' => 'Required Test',
        'version' => '1.0',
        'is_active' => true,
        'definition' => [],
    ]);
    $version->sections()->create(['position' => 1, 'key' => 'section', 'title' => 'Section'])
        ->questions()->create([
            'position' => 1,
            'key' => 'question',
            'type' => 'text',
            'prompt' => 'Required',
            'required' => true,
        ]);

    $evaluation = app(EvaluationService::class)->create($business, $version, $user);

    expect(fn () => app(EvaluationService::class)->complete($evaluation, $user))
        ->toThrow(ValidationException::class);
});
