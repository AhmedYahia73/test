<?php

namespace App\Filament\Admin\Resources\SessionTimes\Pages;

use App\Filament\Admin\Resources\SessionTimes\SessionTimesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSessionTimes extends ListRecords
{
    protected static string $resource = SessionTimesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
