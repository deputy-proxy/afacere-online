<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static string|UnitEnum|null $navigationGroup = 'Security';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

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
        return $table->columns([TextColumn::make('action')->searchable(), TextColumn::make('actor.name')->label('Actor'), TextColumn::make('subject_type')->label('Subject'), TextColumn::make('occurred_at')->dateTime()->sortable()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListAuditLogs::route('/')];
    }
}
