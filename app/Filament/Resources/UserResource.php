<?php

declare(strict_types=1);
namespace App\Filament\Resources;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;
class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|UnitEnum|null $navigationGroup = 'Security';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';
    public static function canAccess(): bool { return auth()->user()?->isAdmin() ?? false; }
    public static function form(Schema $schema): Schema { return $schema; }
    public static function table(Table $table): Table { return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('email')->searchable(), TextColumn::make('is_admin')->badge(), TextColumn::make('created_at')->dateTime()]); }
    public static function getPages(): array { return ['index' => Pages\ListUsers::route('/')]; }
}
