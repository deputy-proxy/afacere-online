<?php

declare(strict_types=1);

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('keeps every entrepreneur workflow behind authentication', function (): void {
    $routes = [
        'dashboard',
        'business.notifications',
        'account.subscription',
        'business.onboarding',
        'business.evaluation',
        'business.evaluation.diagnosis',
        'business.action-plan',
        'business.guides',
        'business.opportunities',
        'business.monitor',
    ];

    foreach ($routes as $route) {
        $this->get(route($route))->assertRedirect(route('login'));
    }
});

it('preserves business context across the entrepreneur entry points', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create(['name' => 'Integration Business']);
    $user->businesses()->attach($business->id, ['role' => 'owner', 'joined_at' => now()]);

    $this->actingAs($user)->get(route('dashboard'))->assertOk()->assertSee('Integration Business');
    $this->actingAs($user)->get(route('business.notifications'))->assertOk();
    $this->actingAs($user)->get(route('business.monitor'))->assertOk()->assertSee('Monitor');
    $this->actingAs($user)->get(route('account.subscription'))->assertOk();
});

it('does not expose another business through entrepreneur context', function (): void {
    $user = User::factory()->create();
    $owned = Business::factory()->create(['name' => 'Owned Business']);
    $other = Business::factory()->create(['name' => 'Private Business']);
    $user->businesses()->attach($owned->id, ['role' => 'owner', 'joined_at' => now()]);

    $this->actingAs($user)->get(route('dashboard'))->assertOk()->assertSee('Owned Business')->assertDontSee('Private Business');
    $this->actingAs($user)->get(route('business.notifications'))->assertOk()->assertDontSee('Private Business');
});
