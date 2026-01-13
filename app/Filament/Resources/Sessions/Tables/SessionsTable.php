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
                            $todayDate = now()->toDateString();

                            // التأكد من أن الرابط صالح لليوم وموجود
                            if ($record->date_link === $todayDate && !empty($record->link)) {
                                
                                // تسجيل الحضور في جدول attendance_user
                                // نستخدم syncWithoutDetaching عشان لو ضغط أكتر من مرة ميكررش السجل
                                $record->students_attendance()->syncWithoutDetaching([
                                    auth()->id() => [
                                        'date' => $todayDate,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]
                                ]);

                                // توجيه المستخدم للرابط في صفحة جديدة
                                return redirect()->away($record->link);
                            }
                        })
                )
                // نجعل شكل النص يوحي بأنه قابل للضغط (اختياري)
                ->extraAttributes([
                    'style' => 'cursor: pointer; text-decoration: underline;'
                ])
                    ->openUrlInNewTab(),

                TextColumn::make('today_time')
                    ->label('Starting At Today')
                    ->dateTime('H:i A')
                    ->getStateUsing(function ($record) use ($today) {
                        // هنا نستخدم where العادية لأننا نتعامل مع Collection
                        // ونقارن الوقت كنص
                        return $record->sessionTimes
                            ->where('day', $today)
                            ->where('to', '>', now()->format("H:i:s"))
                            ->first()?->from;
                    }),
            ])
            ->filters([
                Filter::make('upcoming_sessions')
                    ->label('Today\'s Upcoming Sessions')
                    ->default() 
                    ->query(function (Builder $query) use ($today) {
                        $currentTime = now()->format('H:i:s');
                        $startTimeLimit = now()->addMinutes(30)->format('H:i:s');

                        return $query->whereHas('sessionTimes', function (Builder $subQuery) use ($today, $currentTime, $startTimeLimit) {
                            $subQuery->where('day', $today)
                                ->whereTime('from', '<=', $startTimeLimit)
                                ->whereTime('to', '>', $currentTime); 
                        })
                        ->where("start_date", "<=", date("Y-m-d"))
                        ->where("end_date", ">=", date("Y-m-d"))
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