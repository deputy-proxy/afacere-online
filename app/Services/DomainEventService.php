<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Business;
use App\Models\DomainEvent;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;

final class DomainEventService
{
    /** @param array<string, mixed> $payload */
    public function record(string $type, ?User $actor = null, ?Business $business = null, ?object $subject = null, array $payload = []): DomainEvent
    {
        return DomainEvent::create(['actor_id' => $actor?->id, 'business_id' => $business?->id, 'type' => $type, 'subject_type' => $subject !== null ? $subject::class : null, 'subject_id' => $subject?->getKey(), 'payload' => $payload, 'occurred_at' => now()]);
    }

    /** @param array<string, mixed> $data */
    public function notify(User $user, string $type, string $title, string $body, ?Business $business = null, array $data = []): UserNotification
    {
        return UserNotification::create(['user_id' => $user->id, 'business_id' => $business?->id, 'type' => $type, 'title' => $title, 'body' => $body, 'data' => $data]);
    }

    /** @param array<string, mixed> $payload */
    public function transaction(string $type, ?User $actor, ?Business $business, ?object $subject, array $payload = []): DomainEvent
    {
        return DB::transaction(fn (): DomainEvent => $this->record($type, $actor, $business, $subject, $payload));
    }
}
