<?php

namespace App\Enums;

enum ActionStatus: string
{
    case Recommended = 'recommended';
    case Accepted = 'accepted';
    case Active = 'active';
    case Completed = 'completed';
    case Skipped = 'skipped';
    case Blocked = 'blocked';

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, match ($this) {
            self::Recommended => [self::Accepted, self::Skipped],
            self::Accepted => [self::Active, self::Skipped, self::Blocked],
            self::Active => [self::Completed, self::Skipped, self::Blocked],
            self::Completed, self::Skipped, self::Blocked => [],
        }, true);
    }
}
