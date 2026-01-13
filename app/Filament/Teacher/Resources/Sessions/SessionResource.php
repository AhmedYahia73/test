<?php

namespace App\Filament\Teacher\Resources\Sessions;

use BackedEnum;
use Carbon\Carbon;
use App\Models\Session;
use Filament\Tables\Table;
use App\Models\LiveSession;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Teacher\Resources\Sessions\Pages\EditSession;
use App\Filament\Teacher\Resources\Sessions\Pages\ListSessions;
use App\Filament\Teacher\Resources\Sessions\Pages\CreateSession;
use App\Filament\Teacher\Resources\Sessions\Schemas\SessionForm;
use App\Filament\Teacher\Resources\Sessions\Tables\SessionsTable;

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
    
    public static function getEloquentQuery(): Builder
    { 
        return parent::getEloquentQuery()->where('teacher_id', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSessions::route('/'),
            // 'create' => CreateSession::route('/create'),
            'edit' => EditSession::route('/{record}/edit'),
        ];
    }
}
