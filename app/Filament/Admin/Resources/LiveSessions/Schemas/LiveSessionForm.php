<?php

namespace App\Filament\Admin\Resources\LiveSessions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;

class LiveSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Session Details')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->label('Session Name'),
                    
                    Select::make('teacher_id')
                    ->relationship(
                        name: 'teacher', 
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('role', 'teacher')
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Assign Teacher'),
                ]),

            Section::make('Schedule')
            ->schema([
                Repeater::make('sessionTimes') // اسم العلاقة في الموديل
                    ->relationship() 
                    ->schema([
                        Select::make('day')
                            ->options([
                                'SUNDAY' => 'Sunday',
                                'MONDAY' => 'Monday',
                                'TUESDAY' => 'Tuesday',
                                'WEDNESDAY' => 'Wednesday',
                                'THURSDAY' => 'Thursday',
                                'FRIDAY' => 'Friday',
                                'SATURDAY' => 'Saturday',
                            ])
                            ->required(),
                        
                        TimePicker::make('from')
                            ->label('Start Time')
                            ->required(),
                            
                        TimePicker::make('to')
                            ->label('End Time')
                            ->required(),
                    ])
                    ->columns(3) // عرض الحقول بجانب بعضها داخل الـ Repeater
                    ->addActionLabel('Add New Time Slot')
                    ->collapsible(), // إمكانية طي المواعيد لتنظيم الشكل
            ]),

            Section::make('Schedule')
            ->schema([ 
                DatePicker::make('start_date')
                    ->label('Start Date')
                    ->required(),
                
                DatePicker::make('end_date')
                    ->label('End Date')
                    ->required(),
            ]),
            Section::make('Students Enrollment')
            ->description('Assign students to this live session')
            ->schema([
                Select::make('students') // اسم العلاقة في الموديل
                    ->relationship(
                        name: 'students', 
                        titleAttribute: 'name',
                        // نفلتر النتائج عشان يظهر الطلاب فقط وليس المدرسين أو الأدمن
                        modifyQueryUsing: fn (Builder $query) => $query->where('role', 'student') 
                    )
                    ->multiple()
                    ->preload()
                    ->searchable() 
                    ->label('Select Students')
                    ->required(),
            ]),
        ]);
    }
}
