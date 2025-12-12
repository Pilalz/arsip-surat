<?php

namespace App\Filament\Resources\RSLApps;

use App\Filament\Resources\RSLApps\Pages\CreateRSLApp;
use App\Filament\Resources\RSLApps\Pages\EditRSLApp;
use App\Filament\Resources\RSLApps\Pages\ListRSLApps;
use App\Filament\Resources\RSLApps\Pages\ViewRSLApp;
use App\Filament\Resources\RSLApps\Schemas\RSLAppForm;
use App\Filament\Resources\RSLApps\Schemas\RSLAppInfolist;
use App\Filament\Resources\RSLApps\Tables\RSLAppsTable;
use App\Models\RSLApp;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;

class RSLAppResource extends Resource
{
    protected static ?string $model = RSLApp::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    
    protected static ?string $modelLabel = 'Mail'; 
    protected static ?string $pluralModelLabel = 'Mail';
    protected static ?string $navigationLabel = 'Mail';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return RSLAppForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RSLAppInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RSLAppsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRSLApps::route('/'),
            'create' => CreateRSLApp::route('/create'),
            'view' => ViewRSLApp::route('/{record}'),
            'edit' => EditRSLApp::route('/{record}/edit'),
        ];
    }
}
