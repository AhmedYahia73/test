<?php

namespace App\Filament\Admin\Resources\SessionTimes\Schemas;

use Carbon\Carbon;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;

class SessionTimesForm
{
    public static function configure(Schema $schema): Schema
    { 
        return $schema
            ->components([
                Section::make('Session Details')
                ->schema([
                    TextInput::make('link') 
                    ->label('Link'),
                    
                    Select::make('teacher_id')
                    ->relationship(
                        name: 'teacher', 
                        titleAttribute: 'name',
                    ) 
                    ->required()
                    ->searchable()
                    ->preload() 
                    ->label('Assign Teacher'),
                ]),
            ]);
    }
}
