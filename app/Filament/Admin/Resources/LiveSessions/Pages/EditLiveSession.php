<?php

namespace App\Filament\Admin\Resources\LiveSessions\Pages;

use App\Filament\Admin\Resources\LiveSessions\LiveSessionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLiveSession extends EditRecord
{
    protected static string $resource = LiveSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
