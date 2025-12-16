<?php

namespace App\Filament\Resources\RSLApps\Pages;

use App\Filament\Resources\RSLApps\RSLAppResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Contact;
use App\Mail\IncomingMailNotification;
use Illuminate\Support\Facades\Mail;

class CreateRSLApp extends CreateRecord
{
    protected static string $resource = RSLAppResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Cek foto
        if (isset($data['photo']) && $data['photo']) {
            
            // Cek Base64
            if (preg_match('/^data:image\/(\w+);base64,/', $data['photo'], $type)) {
                $base64Data = substr($data['photo'], strpos($data['photo'], ',') + 1);
                $extension = strtolower($type[1]);
                $fileData = base64_decode($base64Data);

                if ($fileData !== false) {
                    $fileName = 'surat-photos/' . Str::random(40) . '.' . $extension;
                    Storage::disk('local')->put($fileName, $fileData);
                    
                    // Ganti data dengan nama file
                    $data['photo'] = $fileName;
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

    protected function afterCreate(): void
    {
        $record = $this->getRecord();

        if ($record->mail_type === 'incoming') {
            
            $recipientEmail = $record->recipientContact?->email;
            $recipientDirectEmail = $record->recipientContact?->upperContact?->email;

            if ($recipientEmail) {
                try {
                    $mail = Mail::to($recipientEmail);

                    if ($recipientDirectEmail) {
                        $mail->cc($recipientDirectEmail);
                    }

                    $mail->send(new IncomingMailNotification($record));
                    
                    // (Opsional) Beri notifikasi sukses ke Admin di layar
                    \Filament\Notifications\Notification::make()
                        ->title('Email terkirim ke ' . $recipientEmail)
                        ->body($recipientDirectEmail ? "(CC ke atasan: $recipientDirectEmail)" : null)
                        ->success()
                        ->send();
                        
                } catch (\Exception $e) {
                    // Jika gagal kirim (misal internet mati), jangan bikin error aplikasi
                    // Cukup log error-nya atau beri notifikasi warning
                    \Filament\Notifications\Notification::make()
                        ->title('Gagal mengirim email')
                        ->body($e->getMessage())
                        ->warning()
                        ->send();
                }
            }
        }
    }
}
