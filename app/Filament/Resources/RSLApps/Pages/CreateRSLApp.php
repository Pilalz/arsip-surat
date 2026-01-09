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

use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;

class CreateRSLApp extends CreateRecord
{
    protected static string $resource = RSLAppResource::class;

    protected static ?string $title = 'Input';

    protected array $tempStatuses = [];    

    public function mount(): void
    {
        parent::mount();

        // Register Hook KHUSUS untuk halaman ini (ListCategories::class)
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => '<style>
                .fi-main {
                    margin: 1px 1px !important;
                }
                .fi-page-header-main-ctn {
                    row-gap: 10px !important;
                }
                .fi-header-heading {
                    font-size: 20px !important;
                }
                .fi-sc-form {
                    gap: 1px !important;
                }
                .fi-sc {
                    margin: 0px !important;
                    gap: 15px !important;
                }
            </style>',
            scopes: [static::class]
        );
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $statuses = $data['mailStatuses'] ?? [];

        foreach ($statuses as $key => $statusItem) {
            $finalFiles = [];

            if (!empty($statusItem['temp_photo_upload'])) {
                $uploaded = $statusItem['temp_photo_upload'];
                if (is_array($uploaded)) {
                    $finalFiles = array_merge($finalFiles, $uploaded);
                } else {
                    $finalFiles[] = $uploaded;
                }
            }

            if (!empty($statusItem['temp_photo_camera'])) {
                $photoData = $statusItem['temp_photo_camera'];
                if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $type)) {
                    $base64Data = substr($photoData, strpos($photoData, ',') + 1);
                    $extension = strtolower($type[1]);
                    $fileData = base64_decode($base64Data);

                    if ($fileData !== false) {
                        $fileName = 'status-attachments/' . Str::random(40) . '.' . $extension;
                        Storage::disk('local')->put($fileName, $fileData);
                        $finalFiles[] = $fileName;
                    }
                }
            }

            $statuses[$key]['attachments'] = $finalFiles;          

            // Hapus field sampah agar bersih
            unset($statuses[$key]['temp_photo_camera']);
            unset($statuses[$key]['temp_photo_upload']);
            unset($statuses[$key]['upload_method']);
            unset($statuses[$key]['photo']);
        }

        // Simpan ke Property Class untuk dipakai di afterCreate
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

    protected function afterCreate(): void
    {
        $record = $this->getRecord();

        if (!empty($this->tempStatuses)) {
            foreach ($this->tempStatuses as $status) {
                // Pastikan Model MailStatus punya protected $guarded = [];
                $record->mailStatuses()->create([
                    'status' => $status['status'],
                    'date'   => $status['date'],
                    'time'   => $status['time'],
                    // Jika kosong, simpan sebagai array kosong []
                    'attachments'  => $status['attachments'] ?? [], 
                    'recipient' => $status['recipient'] ?? null,
                ]);
            }
        }

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
