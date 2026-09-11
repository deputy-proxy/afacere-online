<?php

declare(strict_types=1);

use App\Models\Business;
use App\Models\Recommendation;
use App\Models\User;
use App\Services\RecommendationPresentationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('presents recommendation provenance without requiring AI', function (): void {
    $recommendation = new Recommendation([
        'title' => 'Improve customer retention',
        'reason' => 'Retention is a current business priority.',
        'confidence' => 0.82,
        'context' => [
            'expected_outcome' => 'Increase repeat purchases.',
            'source' => 'deterministic',
        ],
    ]);

    $context = app(RecommendationPresentationService::class)->context($recommendation);

    expect($context)->toMatchArray([
        'reason' => 'Retention is a current business priority.',
        'expected_outcome' => 'Increase repeat purchases.',
        'confidence' => 0.82,
        'source' => 'deterministic',
    ])->and($context['source'])->toBe('deterministic');
});

it('keeps ranking behind the application-level provider boundary', function (): void {
    $business = Business::factory()->create();
    $user = User::factory()->create();
    $user->businesses()->attach($business->id, [
        'role' => 'owner',
        'joined_at' => now(),
    ]);

    $result = app(RecommendationPresentationService::class)->rank($business, $user, [
        ['id' => 1],
        ['id' => 2],
    ]);

    expect($result)->toBe([
        ['id' => 1],
        ['id' => 2],
    ]);
});

it('only returns suggested recommendations for an authorized business', function (): void {
    $business = Business::factory()->create();
    $user = User::factory()->create();
    $user->businesses()->attach($business->id, [
        'role' => 'owner',
        'joined_at' => now(),
    ]);

    Recommendation::factory()->create([
        'business_id' => $business->id,
        'status' => 'suggested',
    ]);
    Recommendation::factory()->create([
        'business_id' => $business->id,
        'status' => 'accepted',
    ]);

    $results = app(RecommendationPresentationService::class)->forBusiness($business, $user);

    expect($results)->toHaveCount(1)
        ->and($results->first()->status)->toBe('suggested');
});

it('denies access to recommendations for an unauthorized business', function (): void {
    $business = Business::factory()->create();
    $user = User::factory()->create();

    expect(fn (): mixed => app(RecommendationPresentationService::class)->forBusiness($business, $user))
        ->toThrow(AuthorizationException::class);
});
