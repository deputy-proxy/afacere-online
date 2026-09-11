<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PlatformEvent;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class EventRegistrationService
{
    public function register(PlatformEvent $event, User $user): int
    {
        if ($event->status !== 'published' || CarbonImmutable::parse($event->starts_at)->isPast()) {
            throw ValidationException::withMessages(['event' => 'This event is not open for registration.']);
        }
        $query = DB::table('event_registrations')->where('event_id', $event->id)->where('status', 'registered');
        if ($event->capacity !== null && $query->count() >= $event->capacity) {
            throw ValidationException::withMessages(['event' => 'This event is full.']);
        }
        if (DB::table('event_registrations')->where('event_id', $event->id)->where('user_id', $user->id)->where('status', 'registered')->exists()) {
            throw ValidationException::withMessages(['event' => 'You are already registered.']);
        }

        return (int) DB::table('event_registrations')->insertGetId([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'registered',
            'registered_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function cancel(int $registrationId, User $user): void
    {
        $updated = DB::table('event_registrations')->where('id', $registrationId)->where('user_id', $user->id)->where('status', 'registered')->update(['status' => 'cancelled', 'cancelled_at' => now(), 'updated_at' => now()]);
        abort_unless($updated === 1, 404);
    }
}
