<?php

declare(strict_types=1);

namespace App\Filament\Resources\MonitorCheckInResource\Pages;

use App\Filament\Resources\MonitorCheckInResource;
use Filament\Resources\Pages\ListRecords;

class ListMonitorCheckIns extends ListRecords { protected static string $resource = MonitorCheckInResource::class; }
