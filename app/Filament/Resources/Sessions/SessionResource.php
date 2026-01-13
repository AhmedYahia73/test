<?php

namespace App\Filament\Resources\Sessions;

use BackedEnum; 
use Filament\Tables\Table;
use App\Models\LiveSession;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\Sessions\Pages\EditSession;
use App\Filament\Resources\Sessions\Pages\ListSessions;
use App\Filament\Resources\Sessions\Pages\CreateSession;
use App\Filament\Resources\Sessions\Schemas\SessionForm;
use App\Filament\Resources\Sessions\Tables\SessionsTable;

class SessionResource extends Resource
{
    protected static ?string $model = LiveSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'LiveSession';

    public static function form(Schema $schema): Schema
    {
        return SessionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SessionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSessions::route('/'),
            'create' => CreateSession::route('/create'),
            'edit' => EditSession::route('/{record}/edit'),
        ];
    }
}
