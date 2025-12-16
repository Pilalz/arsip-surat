<?php

namespace App\Filament\Resources\RSLApps\Pages;

use App\Filament\Resources\RSLApps\RSLAppResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Contact;

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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ambil data record lama
        $record = $this->getRecord();

        // Cek jika ada foto baru yg diupload (Base64)
        if (isset($data['photo']) && Str::startsWith($data['photo'], 'data:image')) {
            
            if (preg_match('/^data:image\/(\w+);base64,/', $data['photo'], $type)) {
                $base64Data = substr($data['photo'], strpos($data['photo'], ',') + 1);
                $extension = strtolower($type[1]);
                $fileData = base64_decode($base64Data);

                if ($fileData !== false) {
                    $fileName = 'surat-photos/' . Str::random(40) . '.' . $extension;
                    Storage::disk('local')->put($fileName, $fileData);
                    
                    // Update data jadi path file
                    $data['photo'] = $fileName;

                    // HAPUS FILE LAMA (Biar gak nyampah)
                    if ($record->photo) {
                        Storage::disk('local')->delete($record->photo);
                    }
                }
            }
        }

        if (!empty($data['sender_id'])) {
            $contact = Contact::find($data['sender_id']);
            if ($contact) {
                $data['sender'] = $contact->name; // Simpan nama ke kolom sender
            }
        }

        // Jika Recipient ID diisi, ambil namanya
        if (!empty($data['recipient_id'])) {
            $contact = Contact::find($data['recipient_id']);
            if ($contact) {
                $data['recipient'] = $contact->name; // Simpan nama ke kolom recipient
            }
        }

        return $data;
    }
}
