<?php

declare(strict_types=1);

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the ecosystem for an authenticated business member', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $business = Business::factory()->create();
    $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => now()]);

    $this->actingAs($user)
        ->get(route('business.ecosystem'))
        ->assertOk()
        ->assertSee('Ecosystem')
        ->assertSee('Experts')
        ->assertSee('Marketplace providers')
        ->assertSee('Community')
        ->assertSee('Events');
});
