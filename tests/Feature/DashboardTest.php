<?php

declare(strict_types=1);

use App\Models\Business;
use App\Models\BusinessGoal;
use App\Models\BusinessMetric;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected to the login page', function (): void {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

test('authenticated users without a business are redirected to onboarding', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertRedirect(route('business.onboarding'));
});

test('authenticated users with a business can visit the entrepreneur dashboard', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee($business->name);
    $response->assertSee('Your business workspace and next steps');
});

test('authenticated navigation exposes supported product and account destinations', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();

    foreach ([
        'business.evaluation',
        'business.evaluation.diagnosis',
        'business.action-plan',
        'business.guides',
        'business.opportunities',
        'business.monitor',
        'business.ecosystem',
        'business.notifications',
        'account.subscription',
        'profile.edit',
        'appearance.edit',
        'security.edit',
        'account.data.export',
        'account.data.deletion.status',
    ] as $routeName) {
        $response->assertSee(route($routeName), false);
    }

    $response->assertDontSee('laravel.com/docs');
    $response->assertDontSee('github.com/laravel');
    $response->assertSee($business->name);
});

test('dashboard reflects the current business profile goals and metrics', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create([
        'name' => 'Acme Studio',
        'description' => 'A measurable business description.',
    ]);
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    BusinessGoal::query()->create([
        'business_id' => $business->id,
        'type' => 'revenue',
        'title' => 'Reach monthly revenue target',
        'description' => null,
        'target' => 10000,
        'unit' => 'EUR',
        'deadline' => now()->addMonth(),
        'status' => 'active',
        'stage' => $business->stage,
    ]);
    BusinessMetric::query()->create([
        'business_id' => $business->id,
        'key' => 'monthly_revenue',
        'name' => 'Monthly revenue',
        'unit' => 'EUR',
        'aggregation' => 'latest',
        'stage' => $business->stage,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('A measurable business description.');
    $response->assertSee('Reach monthly revenue target');
    $response->assertSee('Monthly revenue');
    $response->assertSee('Start your evaluation');
    $response->assertSee('1');
});

test('dashboard next action changes from evaluation to diagnosis when priorities exist', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    $evaluationVersion = \App\Models\EvaluationVersion::query()->create([
        'name' => 'Default',
        'version' => '1.0',
        'is_active' => true,
        'definition' => [],
    ]);
    $business->evaluations()->create([
        'evaluation_version_id' => $evaluationVersion->id,
        'status' => 'completed',
        'started_at' => now()->subDay(),
        'completed_at' => now(),
    ]);
    \App\Models\Priority::query()->create([
        'business_id' => $business->id,
        'position' => 1,
        'title' => 'Improve customer acquisition',
        'reason' => 'The evaluation identified acquisition as the highest priority.',
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Review your diagnosis');
    $response->assertSee('Improve customer acquisition');
});

test('authenticated dashboard remains isolated to businesses the user can access', function (): void {
    $user = User::factory()->create();
    $ownedBusiness = Business::factory()->create(['name' => 'Owned Business']);
    $otherBusiness = Business::factory()->create(['name' => 'Other Business']);
    $ownedBusiness->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Owned Business');
    $response->assertDontSee('Other Business');
});
