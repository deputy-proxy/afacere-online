<?php

declare(strict_types=1);

namespace App\Enums;

enum BusinessGoalStatus: string
{
    case Active = 'active';
    case Paused = 'paused';
    case Achieved = 'achieved';
    case Abandoned = 'abandoned';
}
