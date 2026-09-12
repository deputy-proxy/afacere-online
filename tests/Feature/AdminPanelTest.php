<?php

use App\Models\User;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('only administrator users can access the admin panel', function (): void {
    $panel = app(Panel::class);
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    expect($admin->canAccessPanel($panel))->toBeTrue()
        ->and($user->canAccessPanel($panel))->toBeFalse();
});
