<?php

declare(strict_types=1);

use App\Livewire\Business\Onboarding;
use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guests cannot access business onboarding', function (): void {
    $response = $this->get(route('business.onboarding'));

    $response->assertRedirect(route('login'));
});

test('an authenticated user can create their first business', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Onboarding::class)
        ->set('name', 'Acme SRL')
        ->set('description', 'A practical business.')
        ->call('createBusiness')
        ->assertRedirect(route('dashboard'));

    $business = Business::query()->where('name', 'Acme SRL')->first();

    expect($business)->not->toBeNull()
        ->and($business?->members()->whereKey($user->id)->exists())->toBeTrue()
        ->and($business?->members()->whereKey($user->id)->first()?->pivot->role)->toBe('owner');
});

test('business name is required during onboarding', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Onboarding::class)
        ->set('name', '')
        ->call('createBusiness')
        ->assertHasErrors(['name' => 'required']);

    expect(Business::query()->count())->toBe(0);
});
