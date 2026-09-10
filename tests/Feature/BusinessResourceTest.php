<?php

use App\Filament\Resources\BusinessResource;
use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('business resource is registered for the admin panel', function (): void {
    expect(BusinessResource::getModel())->toBe(Business::class)
        ->and(BusinessResource::getPages())->toHaveKeys(['index', 'create', 'edit']);
});

test('business resource is only accessible to administrators', function (): void {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)->get('/admin/businesses')->assertSuccessful();
    $this->actingAs($user)->get('/admin/businesses')->assertForbidden();
});
