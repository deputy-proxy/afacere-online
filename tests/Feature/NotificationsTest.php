<?php

declare(strict_types=1);

use App\Models\AiRun;
use App\Models\Business;
use App\Models\DomainEvent;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\FeedbackService;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

it('lists only authorized business notifications and persists read state', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $other = Business::factory()->create();
    $user->businesses()->attach($business->id, ['role' => 'owner', 'joined_at' => now()]);

    $service = app(NotificationService::class);
    $visible = $service->notify($user, 'action.status_changed', 'Action updated', 'Keep moving.', $business, ['event_key' => 'action:1:active']);
    UserNotification::create(['user_id' => $user->id, 'business_id' => $other->id, 'type' => 'private', 'title' => 'Hidden', 'body' => 'No access']);

    expect($service->recent($user, $business))->toHaveCount(1);
    $service->markRead($user, $visible->id);
    expect($service->unreadCount($user, $business))->toBe(0);
});

it('deduplicates notifications by event key', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $user->businesses()->attach($business->id, ['role' => 'owner', 'joined_at' => now()]);
    $service = app(NotificationService::class);

    $first = $service->notify($user, 'monitor.alert', 'Alert', 'Check this.', $business, ['event_key' => 'monitor:1']);
    $second = $service->notify($user, 'monitor.alert', 'Alert', 'Check this.', $business, ['event_key' => 'monitor:1']);

    expect($second->id)->toBe($first->id)->and(UserNotification::query()->count())->toBe(1);
});

it('records authorized activity with business context', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $user->businesses()->attach($business->id, ['role' => 'owner', 'joined_at' => now()]);
    $service = app(NotificationService::class);

    $service->recordEvent('evaluation.completed', $user, $business, null, ['version' => 1]);

    expect($service->activity($user, $business))->toHaveCount(1)
        ->and(DomainEvent::query()->first()->type)->toBe('evaluation.completed');
});

it('stores valid ai feedback and rejects feedback for another user', function (): void {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    $run = AiRun::create(['user_id' => $owner->id, 'provider' => 'test', 'model' => 'test', 'status' => 'completed']);

    expect(fn () => app(FeedbackService::class)->submit($user, $run, 5, 'Not mine.'))->toThrow(HttpException::class, 'Forbidden');

    $ownedRun = AiRun::create(['user_id' => $user->id, 'provider' => 'test', 'model' => 'test', 'status' => 'completed']);
    $feedback = app(FeedbackService::class)->submit($user, $ownedRun, 4, 'Useful.');

    expect($feedback->rating)->toBe(4)->and($feedback->feedback)->toBe('Useful.');
});
