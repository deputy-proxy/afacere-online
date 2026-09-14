<?php

declare(strict_types=1);

use App\Enums\EvaluationStatus;
use App\Livewire\Business\Dashboard;
use App\Models\Business;
use App\Models\BusinessGoal;
use App\Models\BusinessMetric;
use App\Models\EvaluationVersion;
use App\Models\Priority;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guests are redirected to the login page', function (): void {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('authenticated users without a business are redirected to onboarding', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('business.onboarding'));
});

test('authenticated users with a business can visit the entrepreneur dashboard', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee($business->name);
    $response->assertSee('Dashboard');
});

test('authenticated navigation exposes supported product and account destinations', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(route('dashboard'));
    $response->assertSee(route('business.opportunities'));
    $response->assertSee(route('profile.edit'));
});

test('dashboard reflects the current business profile goals and metrics', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create([
        'description' => 'A measurable business description.',
    ]);
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    BusinessGoal::query()->create([
        'business_id' => $business->id,
        'type' => 'revenue',
        'title' => 'Reach monthly revenue target',
        'target' => 10000,
        'unit' => 'EUR',
        'status' => 'active',
        'stage' => $business->stage,
    ]);
    BusinessMetric::query()->create([
        'business_id' => $business->id,
        'key' => 'monthly-revenue',
        'name' => 'Monthly revenue',
        'unit' => 'EUR',
        'aggregation' => 'latest',
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('A measurable business description.');
    $response->assertSee('Reach monthly revenue target');
    $response->assertSee('Monthly revenue');
    $response->assertSee('Start your evaluation');
    $response->assertSee('1');
});

test('dashboard next action changes from diagnosis to action plan when priorities exist', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    $evaluationVersion = EvaluationVersion::query()->create([
        'key' => 'core',
        'name' => 'Default',
        'version' => '1.0',
        'is_active' => true,
        'definition' => [],
    ]);
    $business->evaluations()->create([
        'evaluation_version_id' => $evaluationVersion->id,
        'status' => EvaluationStatus::Completed,
        'started_at' => now()->subDay(),
        'completed_at' => now(),
    ]);
    Priority::query()->create([
        'business_id' => $business->id,
        'position' => 1,
        'title' => 'Improve customer acquisition',
        'reason' => 'The evaluation identified acquisition as the highest priority.',
        'status' => 'active',
    ]);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Improve customer acquisition')
        ->assertSee('Build your Action Plan');
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

test('dashboard component exposes the current business and completed evaluation', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    $evaluationVersion = EvaluationVersion::query()->create([
        'key' => 'core',
        'name' => 'Default',
        'version' => '1.0',
        'is_active' => true,
        'definition' => [],
    ]);
    $business->evaluations()->create([
        'evaluation_version_id' => $evaluationVersion->id,
        'status' => EvaluationStatus::Completed,
        'started_at' => now()->subDay(),
        'completed_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSet('businessId', $business->id)
        ->assertSee($business->name)
        ->assertSee('Review your diagnosis');
});
