<?php

declare(strict_types=1);

use App\Contracts\RecommendationRanker;
use App\Models\Business;
use App\Models\Recommendation;
use App\Models\User;
use App\Services\RecommendationPresentationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('validates and presents recommendation provenance without requiring AI', function (): void {
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
        public function rank(Business $business, User $user, array $candidates): array
        {
            return array_reverse($candidates);
        }
    };

    app()->instance(RecommendationRanker::class, $ranker);

    $business = Business::factory()->create();
    $user = User::factory()->create();
    $user->businesses()->attach($business->id);

    $result = app(RecommendationPresentationService::class)->rank($business, $user, [
        ['id' => 1],
        ['id' => 2],
    ]);

    expect($result)->toBe([
        ['id' => 2],
        ['id' => 1],
    ]);
});
