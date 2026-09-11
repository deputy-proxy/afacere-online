<?php

declare(strict_types=1);
namespace App\Filament\Resources\BusinessGoalResource\Pages;
use App\Filament\Resources\BusinessGoalResource;
use Filament\Resources\Pages\ListRecords;
class ListBusinessGoals extends ListRecords { protected static string $resource = BusinessGoalResource::class; }
