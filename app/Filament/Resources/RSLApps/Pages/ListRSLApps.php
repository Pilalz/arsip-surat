<?php

namespace App\Filament\Resources\RSLApps\Pages;

use App\Filament\Resources\RSLApps\RSLAppResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRSLApps extends ListRecords
{
    protected static string $resource = RSLAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
