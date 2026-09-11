<?php

declare(strict_types=1);

use App\Models\Business;
use App\Models\CommunityPost;
use App\Models\Expert;
use App\Models\MarketplaceProvider;
use App\Models\PlatformEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('exposes the phase three ecosystem through an authenticated business route', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $business = Business::factory()->create();
    $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => now()]);

    Expert::query()->create([
        'user_id' => $user->id,
        'title' => 'Verified Expert',
        'verification_status' => 'verified',
    ]);
    MarketplaceProvider::query()->create([
        'name' => 'Verified Provider',
        'verification_status' => 'verified',
    ]);
    CommunityPost::query()->create([
        'user_id' => $user->id,
        'title' => 'Community question',
        'body' => 'A useful question.',
        'visibility' => 'community',
        'status' => 'published',
    ]);
    PlatformEvent::query()->create([
        'title' => 'Business workshop',
        'description' => 'Upcoming workshop.',
        'status' => 'published',
        'starts_at' => now()->addDay(),
        'price' => 0,
        'is_online' => true,
    ]);

    $this->actingAs($user)
        ->get(route('business.ecosystem'))
        ->assertOk()
        ->assertSee('Ecosystem')
        ->assertSee('Verified Expert')
        ->assertSee('Verified Provider')
        ->assertSee('Community question')
        ->assertSee('Business workshop');
});
