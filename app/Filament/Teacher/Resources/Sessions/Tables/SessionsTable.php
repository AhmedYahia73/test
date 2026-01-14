<?php

namespace App\Filament\Teacher\Resources\Sessions\Tables;

use Carbon\Carbon;
use Filament\Tables\Table; 
use Filament\Actions\EditAction;
use Filament\Tables\Filters\Filter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class SessionsTable
{
    public static function configure(Table $table): Table
    {
        $today = strtoupper(now()->format('l'));
        $now = now()->format('H:i:s');

        return $table 
           ->columns([
                TextColumn::make('name')
                ->color('primary')
                ->weight('bold')
                ->url(function ($record) {
                    $session = $record->actualSessions()
                    ->whereDate('date', now()->toDateString())
                    ->where('from', '<=', now()->addMinutes(120)->toDateTimeString())
                    ->where('to', '>', now()->toDateTimeString())
                    ->first();
                 
                    if ($session && !empty($session->link)) {
                        $session->students_attendance()->syncWithoutDetaching(auth()->id());

                        return redirect()->away($session->link);
                    
                    }
                    
                    return null;
                })
                ->openUrlInNewTab(),
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
                })
                ->sortable(),
                TextColumn::make('today_time')
                ->label('Starting At Today')
                ->dateTime('H:i A')
                ->getStateUsing(function ($record) use ($today, $now) {
                    return $record->actualSessions()
                    ->whereDate('date', now()->toDateString())
                    ->where('from', '<=', now()->addMinutes(120)->toDateTimeString())
                    ->where('to', '>', now()->toDateTimeString())
                    ->first()?->from;
                }),
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

                    })
                    ->where("teacher_id", auth()->user()->id);
                })
            ])
            ->recordActions([
                EditAction::make()
                ->label('put session link')
                ->icon('heroicon-m-pencil-square')
                ->color('primary'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
