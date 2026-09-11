<?php

declare(strict_types=1);

use App\Models\PlatformEvent;
use App\Models\User;
use App\Services\EventRegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('enforces event capacity and registration state server side', function (): void {
    $event = PlatformEvent::query()->create(['title' => 'Workshop', 'starts_at' => now()->addDay(), 'capacity' => 1, 'status' => 'published']);
    $first = User::factory()->create();
    $second = User::factory()->create();
    app(EventRegistrationService::class)->register($event, $first);

    expect(fn (): mixed => app(EventRegistrationService::class)->register($event, $second))->toThrow(ValidationException::class);
    expect(DB::table('event_registrations')->where('event_id', $event->id)->count())->toBe(1);
});

it('allows cancellation only to the registering user', function (): void {
    $event = PlatformEvent::query()->create(['title' => 'Workshop', 'starts_at' => now()->addDay(), 'status' => 'published']);
    $user = User::factory()->create();
    $other = User::factory()->create();
    $id = app(EventRegistrationService::class)->register($event, $user);

    expect(fn (): mixed => app(EventRegistrationService::class)->cancel($id, $other))->toThrow(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
    app(EventRegistrationService::class)->cancel($id, $user);
    expect(DB::table('event_registrations')->where('id', $id)->value('status'))->toBe('cancelled');
});