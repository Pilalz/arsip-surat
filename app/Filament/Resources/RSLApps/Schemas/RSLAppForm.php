<?php

namespace App\Filament\Resources\RSLApps\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Radio;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\Contact;
use App\Models\MailMaster;

use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

use App\Forms\Components\CameraField;
use Filament\Forms\Components\ViewField;

class RSLAppForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Mail Detail') 
                ->schema([
                    TextInput::make('mail_number')                
                    ->required(),
                    Select::make('mail_type')
                        ->label('Mail Category')
                        ->options([
                            'incoming' => 'Incoming',
                            'outgoing' => 'Outgoing',
                        ])
                        ->default('incoming')
                        ->required()
                        ->live()
                        ->native(true)
                        ->afterStateUpdated(function ($state, $set) {
                            if ($state === 'incoming') $set('sender_id', null);
                            if ($state === 'outgoing') $set('sender', null);


                            if ($state === 'outgoing') $set('recipient_id', null);
                            if ($state === 'incoming') $set('recipient', null);
                        }),
                    DatePicker::make('date')
                        ->label(fn ($get) => match ($get('mail_type')) {
                            'incoming' => 'Receiving Date',
                            'outgoing' => 'Sending Date',
                            default => 'Receiving Date',
                        })
                        ->default(today())
                        ->required(),
                    DatePicker::make('sender_date')
                        ->label(fn ($get) => match ($get('mail_type')) {
                            'incoming' => 'Sending Date',
                            'outgoing' => 'Receiving Date',
                            default => 'Sending Date',
                        })
                        ->required(fn ($get) => $get('mail_type') === 'incoming'),
                    Select::make('subject1')
                        ->label('Subject 1')
                        ->options([
                            'purchasing' => 'Purchasing',
                            'non purchasing' => 'Non-Purchasing',
                        ])
                        ->required()
                        ->live()
                        ->native(true),
                    TextInput::make('subject2')
                        ->label('Subject 2')
                        ->visible()
                        ->required(fn ($get) => $get('subject1') === 'non purchasing'),
                    Select::make('sender_id')
                        ->label('Sender')
                        ->relationship('senderContact', 'name')
                        ->searchable()
                        ->preload() // biar cepat
                        ->visible(fn ($get) => $get('mail_type') === 'outgoing')
                        ->required(fn ($get) => $get('mail_type') === 'outgoing'),
                    TextInput::make('sender')
                        ->visible(fn ($get) => $get('mail_type') === 'incoming')
                        ->required(fn ($get) => $get('mail_type') === 'incoming'),
                    TextInput::make('kurir')
                        ->label('Kurir (Courier)'),
                    Select::make('recipient_id')
                        ->label('Addressee')
                        ->relationship('recipientContact', 'name')
                        ->searchable()
                        ->visible(fn ($get) => $get('mail_type') === 'incoming')
                        ->required(fn ($get) => $get('mail_type') === 'incoming')
                        ->preload(),
                    TextInput::make('recipient')
                        ->label('Addressee')
                        ->visible(fn ($get) => $get('mail_type') === 'outgoing')
                        ->required(fn ($get) => $get('mail_type') === 'outgoing'),
                ])
                ->columns(2)
                ->compact(),

                Section::make('Status & Attachment') 
                ->schema([
                    Repeater::make('mailStatuses')
                        ->hiddenLabel()
                        ->schema([                        
                            // --- PILIH STATUS DARI TABLE MAILMASTER ---
                            Select::make('status')
                                ->searchable()
                                ->required()
                                ->columnSpanFull()
                                ->live()
                                ->afterStateUpdated(function ($state, $set) {
                                    if ($state === 'Received by user') {
                                        $set('recipient', null);
                                    }
                                })
                                ->options(fn () => MailMaster::where('owner', 'mailStatus.status')->orderBy('seq')
                                    ->pluck('item_name', 'item_name')
                                ),

                            Select::make('recipient')
                                ->options([
                                    'Mila' => 'Mila',
                                    'Dayat' => 'Dayat',
                                    'Hendri' => 'Hendri',
                                    'Manto' => 'Manto',
                                    'Zaenal' => 'Zaenal',
                                    'Lilik' => 'Lilik',
                                    'Ayu' => 'Ayu',
                                    'Ida' => 'Ida',
                                ])
                                ->columnSpanFull()
                                ->visible(fn ($get) => in_array($get('status'), [
                                    'Received by receptionist lantai 29',
                                    'Received by receptionist'
                                ]))
                                ->required(fn ($get) => $get('status') === 'Received by receptionist lantai 29'),

                            DatePicker::make('date')
                                ->default(now())
                                ->required(),

                            TimePicker::make('time')
                                ->seconds(false)
                                ->default(now('Asia/Jakarta')->format('H:i'))
                                ->required(),

                            Hidden::make('attachments'),

                            Radio::make('upload_method')
                                ->label('File Source')
                                ->options([
                                    'upload' => 'Upload File',
                                    'camera' => 'Camera',
                                ])
                                ->default('upload')
                                ->inline()
                                ->live()
                                ->columnSpanFull()
                                ->dehydrated(false)
                                ->afterStateUpdated(function ($state, $set) {
                                    if ($state === 'camera') $set('temp_photo_upload', null);
                                    if ($state === 'upload') $set('temp_photo_camera', null);
                                }),

                            // 2. Input Kamera
                            CameraField::make('temp_photo_camera')
                                ->label('Ambil Foto')
                                ->columnSpanFull()
                                ->visible(fn ($get) => $get('upload_method') === 'camera'),

                            // 3. Input Upload File
                            FileUpload::make('temp_photo_upload')
                                ->hiddenLabel()
                                ->columnSpanFull()
                                ->directory('status-photos')
                                ->multiple()
                                ->reorderable()
                                ->appendFiles()
                                ->maxFiles(5)
                                ->maxSize(2048)
                                ->panelLayout('grid')
                                ->visible(fn ($get) => $get('upload_method') === 'upload'),
                        ])
                        ->addActionLabel('Add Status')
                        ->defaultItems(1)
                        ->columns(2)
                        ->compact()
                            ]),

                    ViewField::make('camera_script')
                        ->view('filament.components.camera-script')
                        ->hiddenLabel()
                        ->dehydrated(false),
                ]);
    }
}
