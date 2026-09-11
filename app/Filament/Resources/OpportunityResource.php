<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\OpportunityResource\Pages;
use App\Models\Opportunity;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class OpportunityResource extends Resource
{
    protected static ?string $model = Opportunity::class;
    protected static string|UnitEnum|null $navigationGroup = 'Opportunities';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';
    public static function canAccess(): bool { return auth()->user()?->isAdmin() ?? false; }
    public static function form(Schema $schema): Schema { return $schema->components([TextInput::make('title')->required(), TextInput::make('opportunity_type_id')->required(), Textarea::make('description')->columnSpanFull(), Toggle::make('is_published'), TextInput::make('valid_from'), TextInput::make('valid_until')]); }
    public static function table(Table $table): Table { return $table->columns([TextColumn::make('title')->searchable()->sortable(), TextColumn::make('type.name')->label('Type'), TextColumn::make('is_published')->badge(), TextColumn::make('valid_until')->dateTime()])->recordActions([EditAction::make()]); }
    public static function getPages(): array { return ['index' => Pages\ListOpportunities::route('/'), 'create' => Pages\CreateOpportunity::route('/create'), 'edit' => Pages\EditOpportunity::route('/{record}/edit')]; }
}
