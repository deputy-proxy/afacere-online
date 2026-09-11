<?php

declare(strict_types=1);

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('serves the critical public discovery pages', function (string $path): void {
    $this->get($path)->assertSuccessful();
})->with([
    '/',
    '/how-it-works',
    '/pricing',
    '/about',
    '/faq',
    '/contact',
    '/legal',
]);

it('serves the authenticated business dashboard for a verified member', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertSuccessful();
});
