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
                TextColumn::make('teacher.name')
                ->label('Teacher'),
                TextColumn::make('start_date')
                ->label('Start Date')
                ->badge()  
                ->sortable(),
                TextColumn::make('end_date')
                ->label('End Date')
                ->badge(),
            ])
            ->filters([ 
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
