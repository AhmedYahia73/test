<?php

namespace App\Filament\Admin\Resources\LiveSessions;

use App\Filament\Admin\Resources\LiveSessions\Pages\CreateLiveSession;
use App\Filament\Admin\Resources\LiveSessions\Pages\EditLiveSession;
use App\Filament\Admin\Resources\LiveSessions\Pages\ListLiveSessions;
use App\Filament\Admin\Resources\LiveSessions\Schemas\LiveSessionForm;
use App\Filament\Admin\Resources\LiveSessions\Tables\LiveSessionsTable;
use App\Models\LiveSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LiveSessionResource extends Resource
{
    protected static ?string $model = LiveSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'LiveSession';

    public static function form(Schema $schema): Schema
    {
        return LiveSessionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LiveSessionsTable::configure($table);
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
            'index' => ListLiveSessions::route('/'),
            'create' => CreateLiveSession::route('/create'),
            'edit' => EditLiveSession::route('/{record}/edit'),
        ];
    }
}
