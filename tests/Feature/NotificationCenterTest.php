<?php

declare(strict_types=1);

use App\Models\Business;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows an entrepreneur to access the notification center', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $user->businesses()->attach($business->id, ['role' => 'owner', 'joined_at' => now()]);
    UserNotification::create([
        'user_id' => $user->id,
        'business_id' => $business->id,
        'type' => 'test',
        'title' => 'Test',
        'body' => 'Test notification.',
    ]);

    $this->actingAs($user)->get(route('business.notifications'))->assertOk();
});

it('cannot mark another users notification as read', function (): void {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $notification = UserNotification::create([
        'user_id' => $other->id,
        'type' => 'test',
        'title' => 'Private',
        'body' => 'Private notification.',
    ]);

    app(NotificationService::class)->markRead($user, $notification->id);

    expect($notification->fresh()->read_at)->toBeNull();
});
