<?php

use App\Models\Business;
use App\Models\Recommendation;
use App\Models\User;
use App\Services\RecommendationService;
use Illuminate\Validation\ValidationException;

it('keeps recommendation acceptance separate from generation', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    $recommendation = Recommendation::create([
        'business_id' => $business->id,
        'source_type' => 'system',
        'status' => 'suggested',
        'priority' => 1,
        'title' => 'Improve conversion',
        'reason' => 'Conversion is below target.',
        'confidence' => 0.8,
        'context' => ['source' => 'evaluation'],
    ]);

    $accepted = app(RecommendationService::class)->accept($recommendation, $user);

    expect($accepted->getAttribute('status'))->toBe('accepted')
        ->and($accepted->accepted_at)->not->toBeNull()
        ->and($accepted->rejected_at)->toBeNull();
});

it('rejects already resolved recommendations and records the reason', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    $recommendation = Recommendation::create([
        'business_id' => $business->id,
        'source_type' => 'ai',
        'status' => 'accepted',
        'priority' => 1,
        'title' => 'Hire sales support',
        'context' => [],
    ]);

    expect(fn () => app(RecommendationService::class)->reject($recommendation, $user, 'Not affordable now.'))
        ->toThrow(ValidationException::class);
});

it('creates a business priority from a recommendation', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    $recommendation = Recommendation::create([
        'business_id' => $business->id,
        'source_type' => 'system',
        'status' => 'suggested',
        'priority' => 0,
        'title' => 'Validate pricing',
        'reason' => 'Pricing is unvalidated.',
    ]);

    $priority = app(RecommendationService::class)->prioritize($recommendation, $user, 1);

    expect($priority->business_id)->toBe($business->id)
        ->and($priority->recommendation_id)->toBe($recommendation->id)
        ->and($priority->position)->toBe(1);
});
