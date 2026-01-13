<?php

namespace App\Filament\Admin\Resources\LiveSessions\Pages;

use App\Filament\Admin\Resources\LiveSessions\LiveSessionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLiveSession extends CreateRecord
{
    protected static string $resource = LiveSessionResource::class;
}
