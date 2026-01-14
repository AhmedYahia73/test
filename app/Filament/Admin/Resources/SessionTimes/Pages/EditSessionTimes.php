<?php

namespace App\Filament\Admin\Resources\SessionTimes\Pages;

use App\Filament\Admin\Resources\SessionTimes\SessionTimesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSessionTimes extends EditRecord
{
    protected static string $resource = SessionTimesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
