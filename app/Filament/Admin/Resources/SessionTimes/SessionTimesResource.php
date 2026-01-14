<?php

namespace App\Filament\Admin\Resources\SessionTimes;

use App\Filament\Admin\Resources\SessionTimes\Pages\CreateSessionTimes;
use App\Filament\Admin\Resources\SessionTimes\Pages\EditSessionTimes;
use App\Filament\Admin\Resources\SessionTimes\Pages\ListSessionTimes;
use App\Filament\Admin\Resources\SessionTimes\Schemas\SessionTimesForm;
use App\Filament\Admin\Resources\SessionTimes\Tables\SessionTimesTable;
use App\Models\SessionTimes;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SessionTimesResource extends Resource
{
    protected static ?string $model = SessionTimes::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'SessionTimes';

    public static function form(Schema $schema): Schema
    {
        return SessionTimesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SessionTimesTable::configure($table);
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
            'index' => ListSessionTimes::route('/'),
            'create' => CreateSessionTimes::route('/create'),
            'edit' => EditSessionTimes::route('/{record}/edit'),
        ];
    }
}
