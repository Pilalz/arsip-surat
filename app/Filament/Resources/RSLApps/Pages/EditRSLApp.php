<?php

namespace App\Filament\Resources\RSLApps\Pages;

use App\Filament\Resources\RSLApps\RSLAppResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRSLApp extends EditRecord
{
    protected static string $resource = RSLAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
