<?php

namespace App\Filament\Resources\RSLApps;

use App\Filament\Resources\RSLAppResource\Pages;
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
use Filament\Infolists\Infolist;
use Filament\Tables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

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

    protected static function processImageUpload($imageInput)
    {
        // Cek 1: Apakah datanya kosong?
        if (!$imageInput) {
            return null;
        }

        // Cek 2: Apakah ini format Base64? (Ciri: diawali "data:image/...")
        // Kalau bukan Base64 (misal cuma nama file lama), kembalikan apa adanya.
        if (!preg_match('/^data:image\/(\w+);base64,/', $imageInput, $type)) {
            return $imageInput;
        }

        // --- PROSES SIMPAN ---
        
        // 1. Ambil data murni (buang header "data:image/jpeg;base64,")
        $base64Data = substr($imageInput, strpos($imageInput, ',') + 1);
        
        // 2. Ambil ekstensi file (jpg/png) dari regex $type
        $extension = strtolower($type[1]); 

        // 3. Decode teks menjadi binary file
        $fileData = base64_decode($base64Data);

        if ($fileData === false) {
            // Jika gagal decode, return null atau throw error
            return null;
        }

        // 4. Buat Nama File Unik (Random String + Ekstensi)
        // Contoh hasil: "surat-photos/a8s7d87as6d876asd.jpg"
        $fileName = 'surat-photos/' . Str::random(40) . '.' . $extension;

        // 5. Simpan ke Storage LOCAL (Private)
        // File akan ada di: storage/app/surat-photos/...
        Storage::disk('local')->put($fileName, $fileData);

        // 6. Kembalikan PATH-nya saja untuk disimpan di Database
        return $fileName;
    }

    public static function afterDelete(Model $record): void
    {
        // Cek apakah data ini punya foto
        if ($record->photo) {
            // Hapus file fisik dari storage local
            Storage::disk('local')->delete($record->photo);
        }
    }
}
