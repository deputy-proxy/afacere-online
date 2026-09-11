<?php

declare(strict_types=1);
namespace App\Filament\Resources;
use App\Filament\Resources\ProductPlanResource\Pages;
use App\Models\ProductPlan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;
class ProductPlanResource extends Resource
{
    protected static ?string $model = ProductPlan::class;
    protected static string|UnitEnum|null $navigationGroup = 'Commerce';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';
    public static function canAccess(): bool { return auth()->user()?->isAdmin() ?? false; }
    public static function form(Schema $schema): Schema { return $schema; }
    public static function table(Table $table): Table { return $table->columns([TextColumn::make('product.name')->label('Product'), TextColumn::make('key'), TextColumn::make('name'), TextColumn::make('price_minor')->label('Price'), TextColumn::make('active')->badge()]); }
    public static function getPages(): array { return ['index' => Pages\ListProductPlans::route('/')]; }
}
