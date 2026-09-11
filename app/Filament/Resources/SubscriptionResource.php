<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;
    protected static string|UnitEnum|null $navigationGroup = 'Commerce';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';
    public static function canAccess(): bool { return auth()->user()?->isAdmin() ?? false; }
    public static function form(Schema $schema): Schema { return $schema; }
    public static function table(Table $table): Table { return $table->columns([TextColumn::make('user.name')->label('User')->searchable(), TextColumn::make('plan.name')->label('Plan'), TextColumn::make('status')->badge(), TextColumn::make('starts_at')->dateTime(), TextColumn::make('ends_at')->dateTime()]); }
    public static function getPages(): array { return ['index' => Pages\ListSubscriptions::route('/')]; }
}
