<?php

namespace App\Filament\Resources\RSLApps\Pages;

use App\Filament\Resources\RSLApps\RSLAppResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRSLApp extends ViewRecord
{
    protected static string $resource = RSLAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
