<?php

declare(strict_types=1);

use App\Contracts\RecommendationRanker;
use App\Models\Business;
use App\Models\Recommendation;
use App\Models\User;
use App\Services\RecommendationPresentationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

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
    ]);
});

it('keeps ranking behind the application-level provider boundary', function (): void {
    $ranker = new class implements RecommendationRanker
    {
        /**
         * @param array<int, array<string, mixed>> $candidates
         *
         * @return array<int, array<string, mixed>>
         */
        public function rank(Business $business, User $user, array $candidates): array
        {
            return array_reverse($candidates);
        }
    };

    app()->instance(RecommendationRanker::class, $ranker);

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
        ['id' => 2],
        ['id' => 1],
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

    Gate::forUser($user)->shouldReceive('authorize');

    expect(fn (): mixed => app(RecommendationPresentationService::class)->forBusiness($business, $user))
        ->toThrow(Throwable::class);
});
