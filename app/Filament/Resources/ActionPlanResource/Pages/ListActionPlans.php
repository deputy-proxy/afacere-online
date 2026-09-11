<?php

declare(strict_types=1);

namespace App\Filament\Resources\ActionPlanResource\Pages;

use App\Filament\Resources\ActionPlanResource;
use Filament\Resources\Pages\ListRecords;

class ListActionPlans extends ListRecords
{
    protected static string $resource = ActionPlanResource::class;
}
