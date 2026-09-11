<?php

declare(strict_types=1);

use App\Models\MarketplaceProvider;
use App\Models\User;
use App\Services\TrustGovernanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

it('restricts trust decisions to administrators and records reasons', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    $provider = MarketplaceProvider::query()->create(['name' => 'Provider', 'verification_status' => 'pending']);
    app(TrustGovernanceService::class)->verifyProvider($provider, $admin, 'Documents checked.');
    expect($provider->fresh()->verification_status)->toBe('verified');
    expect(DB::table('trust_actions')->where('target_id', $provider->id)->value('reason'))->toBe('Documents checked.');
});

it('rejects trust decisions by non administrators', function (): void {
    $user = User::factory()->create(['is_admin' => false]);
    $provider = MarketplaceProvider::query()->create(['name' => 'Provider', 'verification_status' => 'pending']);
    expect(fn (): mixed => app(TrustGovernanceService::class)->verifyProvider($provider, $user, 'Nope'))->toThrow(HttpException::class);
});
