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
                TextColumn::make('teacher')
                ->label('Teacher')
                ->getStateUsing(function ($record) use ($today, $now) {
                    return $record->actualSessions()
                    ->whereDate('date', now()->toDateString())
                    ->where('from', '<=', now()->addMinutes(120)->toDateTimeString())
                    ->where('to', '>', now()->toDateTimeString())
                    ->first()?->teacher?->name ?? $record?->teacher?->name;
                }),
                TextColumn::make('students_count')
                ->label('Enrolled Students')
                ->counts('students')
                ->badge()  
                ->sortable(),
                TextColumn::make('student_entered')
                ->label('Students Entered')
                ->badge()  
                ->getStateUsing(function ($record) use ($today, $now) {
                    return $record->actualSessions()
                    ->whereDate('date', now()->toDateString())
                    ->where('from', '<=', now()->addMinutes(120)->toDateTimeString())
                    ->where('to', '>', now()->toDateTimeString())
                    ->first()?->students_attendance->count() ?? 0;
                }),
                TextColumn::make('teacher_entered')
                ->label('Teacher Entered')
                ->badge()  
                ->getStateUsing(function ($record) use ($today, $now) {
                    return $record->actualSessions()
                    ->whereDate('date', now()->toDateString())
                    ->where('from', '<=', now()->addMinutes(120)->toDateTimeString())
                    ->where('to', '>', now()->toDateTimeString())
                    ->first()?->teacher_id ? "Yes" : "No";
                })
            ])
            ->filters([
                Filter::make('upcoming_sessions')
                ->label('Today\'s Upcoming Sessions')
                ->default()
                ->query(function (Builder $query) use ($today) {
                    return $query->whereHas('actualSessions', function (Builder $subQuery) {
                        $subQuery->whereDate('date', now())
                        ->where('from', '<=', now()->addMinutes(120))
                        ->where('to', '>', now());
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
