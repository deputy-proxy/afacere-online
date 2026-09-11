<?php

declare(strict_types=1);

use App\Enums\BusinessGoalStatus;
use App\Enums\BusinessMetricAggregation;
use App\Enums\BusinessStage;
use App\Models\Business;
use App\Models\User;
use App\Services\BusinessGoalsMetricsService;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

function goalsMetricsActor(Business $business, string $role = 'owner'): User
{
    $user = User::factory()->create();
    $business->members()->attach($user, ['role' => $role, 'joined_at' => now()]);

    return $user;
}

it('creates a measurable goal scoped to a business and lifecycle stage', function (): void {
    $business = Business::factory()->atStage(BusinessStage::Validation)->create();
    $user = goalsMetricsActor($business);

    $goal = app(BusinessGoalsMetricsService::class)->createGoal($business, $user, [
        'type' => 'revenue',
        'title' => 'Reach monthly revenue target',
        'target' => 5000,
        'unit' => 'EUR',
        'deadline' => '2026-12-31',
        'stage' => BusinessStage::Validation,
    ]);

    expect($goal->business_id)->toBe($business->id)
        ->and($goal->status)->toBe(BusinessGoalStatus::Active)
        ->and($goal->stage)->toBe(BusinessStage::Validation)
        ->and($goal->target)->toBe('5000.0000');
});

it('rejects goals without a numeric target', function (): void {
    $business = Business::factory()->create();
    $user = goalsMetricsActor($business);

    expect(fn () => app(BusinessGoalsMetricsService::class)->createGoal($business, $user, [
        'type' => 'revenue',
        'title' => 'Invalid goal',
        'target' => 'not-a-number',
    ]))->toThrow(ValidationException::class);
});

it('records metric values as immutable historical records', function (): void {
    $business = Business::factory()->create();
    $user = goalsMetricsActor($business);
    $metric = app(BusinessGoalsMetricsService::class)->createMetric($business, $user, [
        'key' => 'monthly_revenue',
        'name' => 'Monthly revenue',
        'unit' => 'EUR',
        'aggregation' => BusinessMetricAggregation::Latest,
        'stage' => BusinessStage::EarlyOperations,
    ]);
    $service = app(BusinessGoalsMetricsService::class);
    $firstDate = CarbonImmutable::parse('2026-09-01 12:00:00');
    $secondDate = CarbonImmutable::parse('2026-09-08 12:00:00');

    $service->recordMetricValue($metric, $user, 1200, $firstDate);
    $service->recordMetricValue($metric, $user, 1800, $secondDate, ['source' => 'dashboard']);

    $history = $service->metricHistory($metric, $user);

    expect($metric->aggregation)->toBe(BusinessMetricAggregation::Latest)
        ->and($metric->stage)->toBe(BusinessStage::EarlyOperations)
        ->and($history)->toHaveCount(2)
        ->and($history[0]->value)->toBe('1200.000000')
        ->and($history[1]->value)->toBe('1800.000000')
        ->and($history[1]->context)->toBe(['source' => 'dashboard']);
});

it('authorizes goal and metric mutations and history reads by business membership', function (): void {
    $business = Business::factory()->create();
    $member = goalsMetricsActor($business, 'viewer');
    $outsider = User::factory()->create();
    $service = app(BusinessGoalsMetricsService::class);

    expect(fn () => $service->createGoal($business, $member, [
        'type' => 'revenue',
        'title' => 'Viewer goal',
        'target' => 100,
    ]))->toThrow(AuthorizationException::class);

    $metric = $service->createMetric($business, goalsMetricsActor($business), [
        'key' => 'customers',
        'name' => 'Customers',
    ]);

    expect(fn () => $service->recordMetricValue($metric, $outsider, 10, now()))
        ->toThrow(AuthorizationException::class)
        ->and(fn () => $service->metricHistory($metric, $outsider))
        ->toThrow(AuthorizationException::class);
});
