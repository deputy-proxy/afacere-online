<?php

declare(strict_types=1);
namespace App\Filament\Resources;
use App\Filament\Resources\EvaluationResource\Pages;
use App\Models\Evaluation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;
class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;
    protected static string|UnitEnum|null $navigationGroup = 'Evaluation';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';
    public static function canAccess(): bool { return auth()->user()?->isAdmin() ?? false; }
    public static function form(Schema $schema): Schema { return $schema; }
    public static function table(Table $table): Table { return $table->columns([TextColumn::make('business.name')->label('Business'), TextColumn::make('status')->badge(), TextColumn::make('evaluationVersion.name')->label('Version'), TextColumn::make('completed_at')->dateTime()]); }
    public static function getPages(): array { return ['index' => Pages\ListEvaluations::route('/')]; }
}
