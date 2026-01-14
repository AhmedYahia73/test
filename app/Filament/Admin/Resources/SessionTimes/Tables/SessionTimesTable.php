<?php

namespace App\Filament\Admin\Resources\SessionTimes\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\Filter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class SessionTimesTable
{
    public static function configure(Table $table): Table
    {
        return $table
             ->columns([
                TextColumn::make('session.name')->searchable(),
                TextColumn::make('teacher')
                ->label('Teacher')
                ->getStateUsing(function ($record) {
                    return $record
                    ->whereDate('date', now()->toDateString())
                    ->where('from', '<=', now()->addMinutes(120)->toDateTimeString())
                    ->where('to', '>', now()->toDateTimeString())
                    ->first()?->teacher?->name ?? $record?->teacher?->name;
                }),
                TextColumn::make('students_count')
                ->label('Enrolled Students')
                ->getStateUsing(function ($record) {
                    return $record->session?->students()->count() ?? 0;
                })
                ->badge()  
                ->sortable(),
                TextColumn::make('student_entered')
                ->label('Students Entered')
                ->badge()  
                ->getStateUsing(function ($record) {
                    return $record
                    ->whereDate('date', now()->toDateString()) 
                    ->where('to', '>', now()->toDateTimeString())
                    ->first()?->students_attendance->count() ?? 0;
                }),
                TextColumn::make('tacher_entered')
                ->label('Teacher Entered')
                ->badge()  
                ->getStateUsing(function ($record) {
                    return $record->tacher_entered ? "Yes" : "No";
                }),
                TextColumn::make('Warning')
                ->label('Warning')
                ->badge()  
                ->getStateUsing(function ($record) {
                    return empty($record->link) && $record
                    ->whereDate('date', now()->toDateString()) 
                    ->where('from', '<=', now()->addMinutes(30)->toDateTimeString())
                    ->where('to', '>', now()->toDateTimeString())
                    ->first() ? "Warning" : "Stable";
                })
                ->color(fn (string $state): string => match ($state) {
                    'Warning' => 'danger', 
                    'Stable' => 'success', 
                    default => 'gray',
                })
                ->sortable()
            ])
            ->filters([
                Filter::make('upcoming_sessions')
                ->label('Today\'s Upcoming Sessions')
                ->default()
                ->query(function (Builder $query) {
                    return $query->whereDate('date', now()) 
                    ->where('to', '>', now());
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
