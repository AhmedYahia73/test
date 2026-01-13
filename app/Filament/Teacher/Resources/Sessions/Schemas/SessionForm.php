<?php

namespace App\Filament\Teacher\Resources\Sessions\Schemas;

use Carbon\Carbon;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;

class SessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('link')
                ->required()
                ->label('Session Name')
                ->afterStateHydrated(function (TextInput $component, $state, $record) {
                    if ($record) {
                        $today = Carbon::today()->toDateString();
                        $savedDate = $record->date_link;

                        if ($savedDate !== $today) {
                            $component->state(null);
                        }
                    }
                }),
                Hidden::make('date_link')
                ->default(now()->toDateString()) // القيمة الافتراضية للجديد
                ->dehydrated(fn ($state) => true),
            ]);
    }
}
