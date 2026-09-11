<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Business;
use App\Models\DomainEvent;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class NotificationService
{
    /** @param array<string, mixed> $data */
    public function notify(User $user, string $type, string $title, string $body, ?Business $business = null, array $data = []): UserNotification
    {
        if ($business !== null) {
            abort_unless($business->members()->whereKey($user->id)->exists(), 403);
        }

        $eventKey = $data['event_key'] ?? null;
        if (is_string($eventKey) && $eventKey !== '') {
            $existing = UserNotification::query()->where('user_id', $user->id)->where('type', $type)->where('data->event_key', $eventKey)->first();
            if ($existing !== null) {
                return $existing;
            }
        }

        return UserNotification::create([
            'user_id' => $user->id,
            'business_id' => $business?->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);
    }

    public function markRead(User $user, int $notificationId): void
    {
        UserNotification::query()->whereKey($notificationId)->where('user_id', $user->id)->update(['read_at' => now()]);
    }

    public function markUnread(User $user, int $notificationId): void
    {
        UserNotification::query()->whereKey($notificationId)->where('user_id', $user->id)->update(['read_at' => null]);
    }

    /** @return array<int, UserNotification> */
    public function recent(User $user, ?Business $business = null, int $limit = 20): array
    {
        $query = UserNotification::query()->where('user_id', $user->id);
        if ($business !== null) {
            abort_unless($business->members()->whereKey($user->id)->exists(), 403);
            $query->where(function ($query) use ($business): void {
                $query->where('business_id', $business->id)->orWhereNull('business_id');
            });
        }

        return $query->latest()->limit($limit)->get()->all();
    }

    public function unreadCount(User $user, ?Business $business = null): int
    {
        $query = UserNotification::query()->where('user_id', $user->id)->whereNull('read_at');
        if ($business !== null) {
            abort_unless($business->members()->whereKey($user->id)->exists(), 403);
            $query->where(function ($query) use ($business): void {
                $query->where('business_id', $business->id)->orWhereNull('business_id');
            });
        }

        return $query->count();
    }

    /** @return Collection<int, DomainEvent> */
    public function activity(User $user, ?Business $business = null, int $limit = 20): Collection
    {
        $query = DomainEvent::query()->where(function ($query) use ($user): void {
            $query->where('actor_id', $user->id)->orWhereHas('business.members', fn ($members) => $members->whereKey($user->id));
        });
        if ($business !== null) {
            abort_unless($business->members()->whereKey($user->id)->exists(), 403);
            $query->where('business_id', $business->id);
        }

        return $query->latest('occurred_at')->limit($limit)->get();
    }

    /** @param array<string, mixed> $payload */
    public function recordEvent(string $type, ?User $actor = null, ?Business $business = null, ?Model $subject = null, array $payload = []): DomainEvent
    {
        return DB::transaction(fn (): DomainEvent => DomainEvent::create([
            'actor_id' => $actor?->id,
            'business_id' => $business?->id,
            'type' => $type,
            'subject_type' => $subject === null ? null : $subject::class,
            'subject_id' => $subject?->getKey(),
            'payload' => $payload,
            'occurred_at' => now(),
        ]));
    }
}
