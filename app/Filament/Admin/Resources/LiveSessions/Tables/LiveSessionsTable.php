<?php

namespace App\Filament\Admin\Resources\LiveSessions\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\Filter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class LiveSessionsTable
{
    public static function configure(Table $table): Table
    {
        $today = strtoupper(now()->format('l'));
        $now = now()->format('H:i:s');

        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('teacher.name')->label('Teacher'),
                TextColumn::make('sessionTimes_count')
                ->counts('sessionTimes')
                ->label('Slots Count'),
                TextColumn::make('student_count')
                ->label('Enrolled Students')
                ->badge()  
                ->counts('students') 
                ->sortable(), 
                TextColumn::make('attendance_today_count')
                ->label('Attendance (Today)')
                ->badge()
                ->color('success')
                ->getStateUsing(function ($record) {
                    // عدّ السجلات في جدول الحضور التي تطابق اليوم لهذه الحصة
                    return $record->students_attendance()
                        ->wherePivot('date', now()->toDateString())
                        ->count();
                }),
            ])
            ->filters([
                Filter::make('upcoming_sessions')
                ->label('Today\'s Upcoming Sessions')
                ->default() 
                ->query(function (Builder $query) {
                    $today = strtoupper(now()->format('l'));
                    $currentTime = now();

                    return $query->whereHas('sessionTimes', function (Builder $subQuery) use ($today, $currentTime) {
                        $subQuery->where('day', $today)
                        ->whereTime('from', '<=', $currentTime->copy()->addMinutes(30)->format('H:i:s'))
                        ->whereTime('to', '>=', $currentTime->format('H:i:s'));
                    })
                    ->whereDate("date_link", $currentTime)
                    ->whereHas("students", function($query){
                        $query->where("users.id", auth()->user()->id);
                    });
                })
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
