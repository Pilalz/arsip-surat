<?php

namespace App\Filament\Resources\RSLApps\Pages;

use App\Filament\Resources\RSLApps\RSLAppResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Contact;
use App\Mail\IncomingMailNotification;
use Illuminate\Support\Facades\Mail;

class CreateRSLApp extends CreateRecord
{
    protected static string $resource = RSLAppResource::class;

    protected static ?string $title = 'Input';

    protected array $tempStatuses = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $statuses = $data['mailStatuses'] ?? [];

        // 3. Proses Foto (Logic Loop yang tadi)
        foreach ($statuses as $key => $statusItem) {
            // Ambil data foto dari salah satu sumber
            $photoData = $statusItem['temp_photo_camera'] ?? $statusItem['temp_photo_upload'] ?? null;
            $finalFileName = null;

            if ($photoData) {
                // Cek Base64 (Dari Kamera)
                if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $type)) {
                    $base64Data = substr($photoData, strpos($photoData, ',') + 1);
                    $extension = strtolower($type[1]);
                    $fileData = base64_decode($base64Data);

                    if ($fileData !== false) {
                        $fileName = 'status-photos/' . Str::random(40) . '.' . $extension;
                        Storage::disk('local')->put($fileName, $fileData);
                        $finalFileName = $fileName;
                    }
                } 
                // Cek jika Upload Biasa (sudah berupa path)
                else {
                    $finalFileName = $photoData;
                }
            }

            // Simpan nama file foto ke array lokal
            $statuses[$key]['photo'] = $finalFileName;            

            // Hapus field sampah agar bersih
            unset($statuses[$key]['temp_photo_camera']);
            unset($statuses[$key]['temp_photo_upload']);
            unset($statuses[$key]['upload_method']);
        }

        // Simpan ke Property Class untuk dipakai di afterCreate
        $this->tempStatuses = $statuses;

        // HAPUS dari $data utama agar tidak error SQL (karena kolom mailStatuses tidak ada di tabel RSLApp)
        unset($data['mailStatuses']);

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

        if (!empty($this->tempStatuses)) {
            foreach ($this->tempStatuses as $status) {
                // Buat baris baru di table anak
                $record->mailStatuses()->create([
                    'status' => $status['status'],
                    'date'   => $status['date'],
                    'time'   => $status['time'],
                    'photo'  => $status['photo'] ?? null,
                ]);
            }
        }

        if ($record->mail_type === 'incoming') {

            $userEmail = $record->recipientContact?->email;
            $userCCEmail = $record->recipientContact?->upperContact?->email;
        
        } elseif ($record->mail_type === 'outgoing') {
            $userEmail = $record->senderContact?->email;
            $userCCEmail = $record->senderContact?->upperContact?->email;
        }

        // if ($record->subject1 === 'purchasing') {
        //     $userCCEmail = "dini.indriasari@borneo.co.id";
        
        // } elseif ($record->subject1 === 'outgoing') {
        //     $userCCEmail = "erry.nurima@borneo.co.id";
            
        // }

        if ($userEmail) {
            try {
                $mail = Mail::to($userEmail);

                if ($userCCEmail) {
                    $mail->cc($userCCEmail);
                }

                $latestStatus = !empty($this->tempStatuses) ? end($this->tempStatuses) : [];

                $mail->send(new IncomingMailNotification($record, $latestStatus));
                
                Notification::make()
                    ->title('Email terkirim ke ' . $userEmail)
                    ->body($userCCEmail ? "(CC ke atasan: $userCCEmail)" : null)
                    ->success()
                    ->send();
                    
            } catch (\Exception $e) {
                Notification::make()
                    ->title('Gagal mengirim email')
                    ->body($e->getMessage())
                    ->warning()
                    ->send();
            }
        }
    }
}
