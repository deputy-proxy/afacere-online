<?php

use App\Models\User;
use App\Providers\Filament\AdminPanelProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('only administrator users can access the admin panel', function (): void {
    $provider = app(AdminPanelProvider::class);
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    expect($provider->canAccessPanel($admin))->toBeTrue()
        ->and($provider->canAccessPanel($user))->toBeFalse();
});
