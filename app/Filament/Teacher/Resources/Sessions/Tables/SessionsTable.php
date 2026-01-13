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
                    $today = now()->toDateString();
                    
                    if ($record->date_link === $today && !empty($record->link)) {
                        return $record->link;
                    }
                    
                    return null;
                })
                ->openUrlInNewTab(),
                TextColumn::make('student_count')
                ->label('Enrolled Students')
                ->badge()  
                ->sortable(),
                TextColumn::make('students_count')
                ->label('Students Entered')
                ->badge() 
                ->counts('students') 
                ->sortable(),
                TextColumn::make('today_time')
                ->label('Starting At Today')
                ->dateTime('H:i A')
                ->getStateUsing(function ($record) use ($today, $now) {
                    return $record->sessionTimes
                        ->where('day', $today)
                        ->where('from', '>', $now)
                        ->first()?->from;
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
                        ->whereTime('from', '<=', $currentTime->copy()->addHours(2)->format('H:i:s'))
                        ->whereTime('to', '>=', $currentTime->format('H:i:s'));
                    });
                })
            ])
            ->recordActions([
                EditAction::make()
                ->label('put session link') // المسمى الجديد اللي إنت عاوزه هنا
                ->icon('heroicon-m-pencil-square') // لو حابب تغير الأيقونة كمان
                ->color('primary'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
