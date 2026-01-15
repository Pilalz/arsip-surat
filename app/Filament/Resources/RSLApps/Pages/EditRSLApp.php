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
        $data['mailStatuses'] = $this->getRecord()->mailStatuses->toArray();

        // 2. PREVIEW FOTO LAMA
        if (isset($data['mailStatuses'])) {
            foreach ($data['mailStatuses'] as $key => $status) {
                // Mapping attachments database ke temp_photo_upload agar muncul di form
                if (isset($status['attachments']) && !empty($status['attachments'])) {
                    $photos = $status['attachments'];
                    
                    if (is_string($photos)) {
                        $photos = [$photos];
                    }

                    $data['mailStatuses'][$key]['temp_photo_upload'] = $photos;
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
            $finalPhotos = [];

            // A. Cek Upload (Ini menghandle file BARU dan file LAMA yg dibiarkan user)
            // Filament FileUpload otomatis mengirim path file lama jika tidak dihapus user
            if (!empty($statusItem['temp_photo_upload'])) {
                $uploaded = $statusItem['temp_photo_upload'];
                if (is_array($uploaded)) {
                    $finalPhotos = array_merge($finalPhotos, $uploaded);
                } else {
                    $finalPhotos[] = $uploaded;
                }
            }

            // B. Cek Kamera
            if (!empty($statusItem['temp_photo_camera'])) {
                $photoData = $statusItem['temp_photo_camera'];
                if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $type)) {
                    $base64Data = substr($photoData, strpos($photoData, ',') + 1);
                    $extension = strtolower($type[1]);
                    $fileData = base64_decode($base64Data);
                    if ($fileData !== false) {
                        $fileName = 'status-photos/' . Str::random(40) . '.' . $extension;
                        Storage::disk('local')->put($fileName, $fileData);
                        $finalPhotos[] = $fileName;
                    }
                }
            }

            // C. Final Assignment
            // Jika kosong, biarkan kosong (Optional)
            $statuses[$key]['attachments'] = $finalPhotos;

            // Bersihkan
            unset($statuses[$key]['temp_photo_camera']);
            unset($statuses[$key]['temp_photo_upload']);
            unset($statuses[$key]['upload_method']);
            unset($statuses[$key]['rowid']); 
            unset($statuses[$key]['photo']); // Bersihkan legacy key

            if ($isNewStatus) {
                $this->newStatusToEmail[] = $statuses[$key];
            }
        }

        $this->tempStatuses = $statuses;
        unset($data['mailStatuses']);

        // Handle Names
        if (!empty($data['sender_id'])) {
            $contact = Contact::find($data['sender_id']);
            if ($contact) $data['sender'] = $contact->name;
        }

        if (!empty($data['recipient_id'])) {
            $contact = Contact::find($data['recipient_id']);
            if ($contact) $data['recipient'] = $contact->name;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();

        // Hapus status lama dan buat ulang (Cara paling aman untuk repeater manual)
        $record->mailStatuses()->delete();

        if (!empty($this->tempStatuses)) {
            foreach ($this->tempStatuses as $status) {
                $record->mailStatuses()->create([
                    'status' => $status['status'],
                    'date'   => $status['date'],
                    'time'   => $status['time'],
                    // Gunakan null coalescing operator untuk handle null/array kosong
                    'attachments'  => $status['attachments'] ?? [], 
                    'recipient' => $status['recipient'] ?? null,
                ]);
            }
        }

        if (!empty($this->newStatusToEmail)) {

            // if ($record->mail_type === 'incoming') {
            //     $userEmail = $record->recipientContact?->email;
            //     $userCCEmail = $record->recipientContact?->upperContact?->email;
            
            // } elseif ($record->mail_type === 'outgoing') {
            //     $userEmail = $record->senderContact?->email;
            //     $userCCEmail = $record->senderContact?->upperContact?->email;
            // }

            $userEmail = "mail.managementbbp@gmail.com";
            $userCCEmail = null;

            // if ($record->subject1 === 'purchasing') {
            //     $userCCEmail = "dini.indriasari@borneo.co.id";
            
            // } elseif ($record->subject1 === 'non purchasing') {
            //     $userCCEmail = "erry.nurima@borneo.co.id"; 
            // }

            if ($record->subject1 === 'purchasing') {
                $userCCEmail = "mail.admin01@bagasbumipersada.co.id";
            
            } elseif ($record->subject1 === 'non purchasing') {
                $userCCEmail = "mail.admin02@bagasbumipersada.co.id";
            }
            
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
