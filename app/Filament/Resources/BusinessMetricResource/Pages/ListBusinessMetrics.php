<?php

declare(strict_types=1);

namespace App\Filament\Resources\BusinessMetricResource\Pages;

use App\Filament\Resources\BusinessMetricResource;
use Filament\Resources\Pages\ListRecords;

class ListBusinessMetrics extends ListRecords
{
    protected static string $resource = BusinessMetricResource::class;
}
