<?php

namespace App\Filament\Admin\Resources\LiveSessions\Schemas;

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
                    ->afterStateHydrated(function (TextInput $component, $state, $record) {
                        if ($record) {
                            $today = Carbon::today()->toDateString();
                            $savedDate = $record->date_link;

                            if ($savedDate !== $today) {
                                $component->state(null);
                            }
                        }
                    })
                    ->label('Assign Teacher'),
                ]),

                Section::make('Schedule')
                ->schema([
                    Repeater::make('sessionWeeklySchedule')
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
                        ->disabled(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord)
                        ->collapsible(), // إمكانية طي المواعيد لتنظيم الشكل
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
                
                Section::make('General Schedule')
                ->description('Set the recurring schedule here')
                ->schema([
                    DatePicker::make('start_date')
                    ->label('Start Date')
                    ->required(),
                    DatePicker::make('end_date')
                    ->label('End Date')
                    ->required(),
                     
                ])
                ->disabled(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord)
                ->collapsible(),

                Section::make('Individual Sessions (Instances)')
                    ->description('Modify specific dates and times for each session')
                    ->schema([
                        Repeater::make('actualSessions') // علاقة HasMany لجدول الحصص المنفردة
                            ->relationship(
                                name: 'actualSessions', 
                                modifyQueryUsing: fn (Builder $query) => $query->orderBy('date')->orderBy('from')
                            )
                            ->schema([
                                Grid::make(3)
                                ->schema([
                                    DatePicker::make('date')
                                    ->label('Date')
                                    ->required()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $set('day', Carbon::parse($state)->format('l')); 
                                        }
                                    }),
                                    TimePicker::make('from')
                                    ->label('Start Time')
                                    ->required()
                                    ->dehydrateStateUsing(fn ($state, $get) => 
                                        $get('date') && $state 
                                            ? Carbon::parse($get('date'))->format('Y-m-d') . ' ' . $state 
                                            : $state
                                    ),
                                    TimePicker::make('to')
                                    ->label('End Time')
                                    ->readOnly()
                                    ->required()
                                    ->dehydrateStateUsing(fn ($state, $get) => 
                                        $get('date') && $state 
                                            ? Carbon::parse($get('date'))->format('Y-m-d') . ' ' . $state 
                                            : $state
                                    ),
                                    TextInput::make('day')
                                    ->readOnly()
                                    ->label('Day')
                                    ->live(),
                                ])
                            ])
                            ->addActionLabel('Add Extra Session') // للسماح بإضافة حصة استثنائية
                            ->itemLabel(fn (array $state): ?string => $state['session_date'] ?? null)
                            ->defaultItems(0)
                    ])
                    ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),
        ]);
    }
}
