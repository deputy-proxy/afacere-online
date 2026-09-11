<?php

declare(strict_types=1);

use App\Models\Business;
use App\Models\MarketplaceProvider;
use App\Models\MarketplaceService;
use App\Models\User;
use App\Services\MarketplaceServiceLayer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('only discovers verified providers with published services', function (): void {
    $verified = MarketplaceProvider::query()->create(['name' => 'Verified', 'verification_status' => 'verified']);
    $pending = MarketplaceProvider::query()->create(['name' => 'Pending', 'verification_status' => 'pending']);
    MarketplaceService::query()->create(['provider_id' => $verified->id, 'name' => 'SEO', 'is_published' => true]);
    MarketplaceService::query()->create(['provider_id' => $pending->id, 'name' => 'Ads', 'is_published' => true]);

    $results = app(MarketplaceServiceLayer::class)->discover(Business::factory()->create());

    expect($results)->toHaveCount(1)->and($results->first()?->id)->toBe($verified->id);
});

it('creates a business-scoped lead only for a verified published service', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => now()]);
    $provider = MarketplaceProvider::query()->create(['name' => 'Provider', 'verification_status' => 'verified']);
    $service = MarketplaceService::query()->create(['provider_id' => $provider->id, 'name' => 'SEO', 'is_published' => true]);

    $leadId = app(MarketplaceServiceLayer::class)->createLead($business, $provider, $service, 'Please contact me.');

    expect(DB::table('marketplace_leads')->where('id', $leadId)->value('business_id'))->toBe($business->id);
});

it('rejects unpublished services', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => now()]);
    $provider = MarketplaceProvider::query()->create(['name' => 'Provider', 'verification_status' => 'verified']);
    $service = MarketplaceService::query()->create(['provider_id' => $provider->id, 'name' => 'SEO', 'is_published' => false]);

    expect(fn (): mixed => app(MarketplaceServiceLayer::class)->createLead($business, $provider, $service, 'Hello'))->toThrow(ValidationException::class);
});