<?php

namespace App\Filament\Resources\Sessions\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\Filter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder; // تأكد من الـ Import الصحيح

class SessionsTable
{
    public static function configure(Table $table): Table
    {
        $today = strtoupper(now()->format('l'));

        return $table
            ->columns([
                TextColumn::make('name')
                    ->color('primary')
                    ->weight('bold')
                ->weight('bold')
                ->action(
                    Action::make('joinAndTrackAttendance')
                    ->action(function ($record) {
                        $currentSession = $record->actualSessions()
                        ->whereDate('date', now()->toDateString())
                        ->where('from', '<=', now()->addMinutes(30)->toDateTimeString())
                        ->where('to', '>', now()->toDateTimeString())
                        ->first();

                        if ($currentSession && !empty($currentSession->link)) {
                            $currentSession->students_attendance()->syncWithoutDetaching(auth()->id());
                            return redirect()->away($currentSession->link);
                        }
                    })
                )
                ->extraAttributes([
                    'style' => 'cursor: pointer; text-decoration: underline;'
                ])
                ->openUrlInNewTab(),
                TextColumn::make('actualSessions')
                ->label('Starting At Today')
                ->dateTime('H:i A')
                ->getStateUsing(function ($record) {
                    return $record->actualSessions()
                        ->whereDate('date', now()->toDateString())
                        ->where('from', '<=', now()->addMinutes(30)->toDateTimeString())
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

                            ->where('from', '<=', now()->addMinutes(30))

                            ->where('to', '>', now());

                    })

                    ->whereHas("students", function(Builder $q) {

                        $q->where("users.id", auth()->id());

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