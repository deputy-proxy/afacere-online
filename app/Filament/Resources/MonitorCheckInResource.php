<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\MonitorCheckInResource\Pages;
use App\Models\MonitorCheckIn;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class MonitorCheckInResource extends Resource
{
    protected static ?string $model = MonitorCheckIn::class;

    protected static string|UnitEnum|null $navigationGroup = 'Monitor';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';

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
        return $table->columns([TextColumn::make('business.name')->label('Business')->searchable(), TextColumn::make('user.name')->label('User'), TextColumn::make('recorded_at')->dateTime()->sortable()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListMonitorCheckIns::route('/')];
    }
}
