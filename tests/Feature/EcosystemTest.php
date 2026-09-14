<?php

declare(strict_types=1);

use App\Livewire\Ecosystem;
use App\Models\Business;
use App\Models\CommunityPost;
use App\Models\Expert;
use App\Models\ExpertAvailability;
use App\Models\MarketplaceProvider;
use App\Models\MarketplaceService;
use App\Models\PlatformEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

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
        ->assertSee('Marketplace')
        ->assertSee('Community &amp; peer review', false)
        ->assertSee('Events');
});

it('shows only verified experts and published marketplace providers', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $business = Business::factory()->create();
    $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => now()]);

    $verifiedUser = User::factory()->create();
    Expert::query()->create(['user_id' => $verifiedUser->id, 'title' => 'Growth Mentor', 'verification_status' => 'verified']);
    Expert::query()->create(['user_id' => User::factory()->create()->id, 'title' => 'Pending Mentor', 'verification_status' => 'pending']);

    $provider = MarketplaceProvider::query()->create(['name' => 'Verified Provider', 'verification_status' => 'verified']);
    MarketplaceService::query()->create(['provider_id' => $provider->id, 'name' => 'SEO', 'is_published' => true]);
    $pending = MarketplaceProvider::query()->create(['name' => 'Pending Provider', 'verification_status' => 'pending']);
    MarketplaceService::query()->create(['provider_id' => $pending->id, 'name' => 'Ads', 'is_published' => true]);

    Livewire::actingAs($user)
        ->test(Ecosystem::class)
        ->assertSee('Growth Mentor')
        ->assertDontSee('Pending Mentor')
        ->assertSee('Verified Provider')
        ->assertDontSee('Pending Provider');
});

it('can request an expert consultation through the ecosystem', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $business = Business::factory()->create();
    $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => now()]);
    $expertUser = User::factory()->create();
    $expert = Expert::query()->create(['user_id' => $expertUser->id, 'title' => 'Finance Mentor', 'verification_status' => 'verified']);
    $availability = ExpertAvailability::query()->create([
        'expert_id' => $expert->id,
        'starts_at' => now()->addDay(),
        'ends_at' => now()->addDay()->addHour(),
        'is_bookable' => true,
    ]);

    Livewire::actingAs($user)
        ->test(Ecosystem::class)
        ->set('selectedExpertId', $expert->id)
        ->set('selectedAvailabilityId', $availability->id)
        ->set('consultationNote', 'Need help with pricing.')
        ->call('requestConsultation');

    expect(DB::table('consultations')->where('business_id', $business->id)->where('expert_id', $expert->id)->exists())->toBeTrue();
});

it('can publish a community post and request an event registration', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $business = Business::factory()->create();
    $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => now()]);
    $event = PlatformEvent::query()->create([
        'title' => 'Founder Workshop',
        'description' => 'A practical workshop.',
        'type' => 'workshop',
        'status' => 'published',
        'starts_at' => now()->addDays(2),
        'capacity' => 20,
        'price' => 0,
        'is_online' => true,
    ]);

    Livewire::actingAs($user)
        ->test(Ecosystem::class)
        ->set('postTitle', 'Pricing question')
        ->set('postBody', 'How do you validate pricing?')
        ->call('createPost')
        ->call('registerForEvent', $event->id);

    expect(CommunityPost::query()->where('user_id', $user->id)->where('title', 'Pricing question')->exists())->toBeTrue();
    expect(DB::table('event_registrations')->where('event_id', $event->id)->where('user_id', $user->id)->count())->toBe(1);
});
