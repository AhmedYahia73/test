<?php

namespace App\Filament\Admin\Resources\LiveSessions\Pages;

use App\Filament\Admin\Resources\LiveSessions\LiveSessionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLiveSessions extends ListRecords
{
    protected static string $resource = LiveSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
