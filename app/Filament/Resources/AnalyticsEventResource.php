<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\AnalyticsEventResource\Pages;
use App\Models\AnalyticsEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AnalyticsEventResource extends Resource
{
    protected static ?string $model = AnalyticsEvent::class;

    protected static string|UnitEnum|null $navigationGroup = 'Analytics';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('user.name')->label('User'), TextColumn::make('business.name')->label('Business'), TextColumn::make('occurred_at')->dateTime()->sortable()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListAnalyticsEvents::route('/')];
    }
}
