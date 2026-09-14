<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public pages expose a main landmark and skip link', function (): void {
    foreach (['/', '/how-it-works', '/pricing', '/about', '/faq', '/contact', '/legal'] as $path) {
        $this->get($path)
            ->assertOk()
            ->assertSee('href="#main-content"', false)
            ->assertSee('id="main-content"', false)
            ->assertSee('<main', false);
    }
});

test('authenticated application shell exposes a main landmark and skip link', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('business.onboarding'))
        ->assertOk()
        ->assertSee('href="#app-main-content"', false)
        ->assertSee('id="app-main-content"', false);
});

test('two-factor recovery toggle and input remain keyboard accessible', function (): void {
    $view = file_get_contents(resource_path('views/livewire/auth/two-factor-challenge.blade.php'));

    expect($view)
        ->toContain('<button type="button"')
        ->toContain(":label=\"__('Recovery code')\"")
        ->not->toContain('@click="toggleInput()"');
});

test('peer review response fields have unique programmatic labels', function (): void {
    $view = file_get_contents(resource_path('views/livewire/ecosystem.blade.php'));

    expect($view)
        ->toContain('for="review-body-{{ $review->id }}"')
        ->toContain('id="review-body-{{ $review->id }}"');
});

test('responsive shell avoids clipping long page titles', function (): void {
    expect(file_get_contents(resource_path('views/components/ui/page-header.blade.php')))
        ->toContain('break-words')
        ->not->toContain('class="truncate"');

    expect(file_get_contents(resource_path('views/layouts/auth/split.blade.php')))
        ->toContain('min-h-svh')
        ->not->toContain('h-dvh');
});

test('ecosystem section controls expose toggle state', function (): void {
    expect(file_get_contents(resource_path('views/livewire/ecosystem.blade.php')))
        ->toContain('aria-pressed="{{ $section === $key ? \'true\' : \'false\' }}"');
});

test('authentication shell exposes a main landmark and skip link', function (): void {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('href="#auth-main-content"', false)
        ->assertSee('id="auth-main-content"', false)
        ->assertSee('<main', false);
});
