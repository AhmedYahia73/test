<?php

namespace App\Filament\Admin\Resources\LiveSessions\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Resources\LiveSessions\LiveSessionResource;

class CreateLiveSession extends CreateRecord
{
    protected static string $resource = LiveSessionResource::class;
    
    protected function afterCreate(): void{
        $this->record->refresh();
        $item = $this->record->generateActualDates();
    }
}
