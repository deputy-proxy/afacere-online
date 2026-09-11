<?php

declare(strict_types=1);

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('passes the public and readiness smoke journeys', function (): void {
    $this->get(route('home'))->assertOk();
    $this->get(route('public.how-it-works'))->assertOk();
    $this->get(route('public.pricing'))->assertOk();
    $this->get(route('health.ready'))->assertOk();
});

it('passes the authenticated core journey with a business context', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $business = Business::factory()->create();
    $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => now()]);

    $this->actingAs($user)->get(route('dashboard'))->assertOk();
    $this->actingAs($user)->get(route('business.guides'))->assertOk();
    $this->actingAs($user)->get(route('business.opportunities'))->assertOk();
    $this->actingAs($user)->get(route('business.monitor'))->assertOk();
    $this->actingAs($user)->get(route('business.ecosystem'))->assertOk();
    $this->actingAs($user)->get(route('business.notifications'))->assertOk();
    $this->actingAs($user)->get(route('account.subscription'))->assertOk();
});

it('enforces authentication and verification boundaries', function (): void {
    $this->get(route('dashboard'))->assertRedirect();

    $unverified = User::factory()->create(['email_verified_at' => null]);
    $this->actingAs($unverified)->get(route('dashboard'))->assertRedirect();
});

it('keeps support access restricted to administrators', function (): void {
    $admin = User::factory()->create(['is_admin' => true, 'email_verified_at' => now()]);
    $target = User::factory()->create(['email_verified_at' => now()]);
    $regular = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($admin)
        ->get(route('internal.support.users.summary', $target))
        ->assertOk();

    $this->actingAs($regular)
        ->get(route('internal.support.users.summary', $target))
        ->assertForbidden();
});
