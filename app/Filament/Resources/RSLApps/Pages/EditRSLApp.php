<?php

namespace App\Filament\Resources\RSLApps\Pages;

use App\Filament\Resources\RSLApps\RSLAppResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Contact;
use App\Mail\IncomingMailNotification;
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification;

class EditRSLApp extends EditRecord
{
    protected static string $resource = RSLAppResource::class;

    protected array $tempStatuses = [];
    protected array $newStatusToEmail = [];

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // 1. LOAD DATA MANUAL
        // Karena repeater manual, kita harus ambil data anak dan masukkan ke array form
        $data['mailStatuses'] = $this->getRecord()->mailStatuses->toArray();

        // 2. PREVIEW FOTO LAMA
        if (isset($data['mailStatuses'])) {
            foreach ($data['mailStatuses'] as $key => $status) {
                if (isset($status['photo']) && $status['photo']) {
                    // Masukkan ke field upload agar muncul previewnya
                    $data['mailStatuses'][$key]['temp_photo_upload'] = $status['photo'];
                    // Set default tab ke upload
                    $data['mailStatuses'][$key]['upload_method'] = 'upload';
                }
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $statuses = $data['mailStatuses'] ?? [];

        $this->newStatusToEmail = [];

        foreach ($statuses as $key => $statusItem) {

            $isNewStatus = !isset($statusItem['rowid']);

            $photoData = $statusItem['temp_photo_camera'] ?? $statusItem['temp_photo_upload'] ?? null;

            $finalFileName = $statusItem['photo'] ?? null;

            if ($photoData) {
                if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $type)) {
                    $base64Data = substr($photoData, strpos($photoData, ',') + 1);
                    $extension = strtolower($type[1]);
                    $fileData = base64_decode($base64Data);
                    if ($fileData !== false) {
                        $fileName = 'status-photos/' . Str::random(40) . '.' . $extension;
                        Storage::disk('local')->put($fileName, $fileData);
                        $finalFileName = $fileName;
                    }
                } else {
                    // Ini path file (baru upload atau existing)
                    $finalFileName = $photoData;
                }
            }

            $statuses[$key]['photo'] = $finalFileName;

            unset($statuses[$key]['temp_photo_camera']);
            unset($statuses[$key]['temp_photo_upload']);
            unset($statuses[$key]['upload_method']);
            unset($statuses[$key]['rowid']); 

            if ($isNewStatus) {
                $this->newStatusToEmail[] = $statuses[$key];
            }
        }

        $this->tempStatuses = $statuses;
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

    protected function afterSave(): void
    {
        $record = $this->getRecord();

        $record->mailStatuses()->delete();

        if (!empty($this->tempStatuses)) {
            foreach ($this->tempStatuses as $status) {
                $record->mailStatuses()->create([
                    'status' => $status['status'],
                    'date'   => $status['date'],
                    'time'   => $status['time'],
                    'photo'  => $status['photo'] ?? null,
                ]);
            }
        }

        if (!empty($this->newStatusToEmail)) {

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
                foreach ($this->newStatusToEmail as $newStatus) {
                    try {

                        $mail = Mail::to($userEmail);

                        if ($userCCEmail) {
                            $mail->cc($userCCEmail);
                        }

                        $mail->send(new IncomingMailNotification($record, $newStatus));

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
    }
}
