<?php

declare(strict_types=1);

use App\Models\Business;
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
    $response->assertSee('What should I do next?');
});

test('dashboard does not expose a business the user is not a member of', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertRedirect(route('business.onboarding'));
    $response->assertDontSee($business->name);
});
