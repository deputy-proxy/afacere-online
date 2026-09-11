<?php

declare(strict_types=1);

namespace App\Enums;

enum BusinessMetricAggregation: string
{
    case Latest = 'latest';
    case Sum = 'sum';
    case Average = 'average';
    case Minimum = 'minimum';
    case Maximum = 'maximum';
}
