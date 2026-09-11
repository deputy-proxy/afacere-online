<?php

declare(strict_types=1);
namespace App\Filament\Resources;
use App\Filament\Resources\ActionPlanResource\Pages;
use App\Models\ActionPlan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;
class ActionPlanResource extends Resource
{
    protected static ?string $model = ActionPlan::class;
    protected static string|UnitEnum|null $navigationGroup = 'Execution';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';
    public static function canAccess(): bool { return auth()->user()?->isAdmin() ?? false; }
    public static function form(Schema $schema): Schema { return $schema; }
    public static function table(Table $table): Table { return $table->columns([TextColumn::make('business.name')->label('Business'), TextColumn::make('version')->sortable(), TextColumn::make('status')->badge(), TextColumn::make('activated_at')->dateTime(), TextColumn::make('completed_at')->dateTime()]); }
    public static function getPages(): array { return ['index' => Pages\ListActionPlans::route('/')]; }
}
