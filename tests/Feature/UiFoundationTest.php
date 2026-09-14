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

test('shared resilience controls are present in the application shell', function (): void {
    expect(file_get_contents(resource_path('views/components/ui/livewire-feedback.blade.php')))
        ->toContain('wire:loading')
        ->toContain('livewire-request-error');

    expect(file_get_contents(resource_path('views/components/ui/livewire-resilience-script.blade.php')))
        ->toContain("Livewire.hook('request'")
        ->toContain('beforeunload')
        ->toContain('setControlsDisabled');

    expect(file_get_contents(resource_path('views/layouts/app.blade.php')))
        ->toContain('<x-ui.livewire-feedback />')
        ->toContain('<x-ui.livewire-resilience-script />');
});

test('standard not-found errors provide safe recovery paths', function (): void {
    $this->get('/this-route-does-not-exist')
        ->assertNotFound()
        ->assertSee('We could not find that page')
        ->assertSee('Go home')
        ->assertSee('Go back');
});
