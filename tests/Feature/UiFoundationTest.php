<?php

declare(strict_types=1);

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the authenticated dashboard through the shared ui foundation', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create(['name' => 'Foundation Business']);
    $user->businesses()->attach($business->id, ['role' => 'owner', 'joined_at' => now()]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertSee('Foundation Business')
        ->assertSee('What should I do next?')
        ->assertSee('Recommendations')
        ->assertSee('Primary navigation')
        ->assertDontSee('https://github.com/laravel/livewire-starter-kit')
        ->assertDontSee('https://laravel.com/docs/starter-kits#livewire');
});

it('renders the dashboard empty state when no business is available', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('No business selected')
        ->assertSee('Create or select a business to continue.');
});
