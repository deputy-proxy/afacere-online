<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProductPlanResource\Pages;

use App\Filament\Resources\ProductPlanResource;
use Filament\Resources\Pages\ListRecords;

class ListProductPlans extends ListRecords
{
    protected static string $resource = ProductPlanResource::class;
}
