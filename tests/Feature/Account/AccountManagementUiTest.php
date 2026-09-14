<?php

declare(strict_types=1);

use App\Livewire\Account\DataLifecycle;
use App\Livewire\Account\Subscription;
use App\Livewire\Business\Notifications;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('account data page exposes export and deletion controls', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('account.data'))
        ->assertOk()
        ->assertSee('Export your data')
        ->assertSee('Request deletion')
        ->assertSee(route('account.data.export'));
});

test('deletion requires explicit confirmation and creates a pending request', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(DataLifecycle::class)
        ->call('requestDeletion')
        ->assertStatus(422);

    Livewire::test(DataLifecycle::class)
        ->set('confirmDeletion', true)
        ->call('requestDeletion')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('data_requests', [
        'user_id' => $user->id,
        'type' => 'deletion',
        'status' => 'pending',
    ]);
});

test('notifications refresh unread state after marking a notification read', function (): void {
    $user = User::factory()->create();

    UserNotification::query()->create([
        'user_id' => $user->id,
        'business_id' => null,
        'type' => 'test',
        'title' => 'Test notification',
        'body' => 'A test notification.',
        'data' => [],
    ]);

    Livewire::actingAs($user)
        ->test(Notifications::class)
        ->assertSet('unread', 1)
        ->call('markRead', UserNotification::query()->firstOrFail()->id)
        ->assertSet('unread', 0)
        ->assertSee('Mark unread');
});

test('subscription page is available without exposing provider-specific billing state', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('account.subscription'))
        ->assertOk()
        ->assertSee('Current subscription')
        ->assertSee('Billing')
        ->assertSee('server-authoritative');

    Livewire::actingAs($user)
        ->test(Subscription::class)
        ->assertSee('Available plans');
});
