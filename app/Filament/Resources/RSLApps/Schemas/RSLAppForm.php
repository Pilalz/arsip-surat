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
                        ->options([
                            'incoming' => 'Incoming',
                            'outgoing' => 'Outgoing',
                        ])
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
                        ->required(),
                    Select::make('subject1')
                        ->options([
                            'purchasing' => 'Purchasing',
                            'non purchasing' => 'Non-Purchasing',
                        ])
                        ->required()
                        ->live()
                        ->native(true)
                        ->afterStateUpdated(function ($state, $set) {
                            if ($state === 'purchasing') {
                                $set('subject2', null);
                            }
                        }),
                    TextInput::make('subject2')
                        ->label('Subject 2')
                        ->visible(fn ($get) => $get('subject1') === 'non purchasing')
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
                    Select::make('recipient_id')
                        ->label('Recipient')
                        ->relationship('recipientContact', 'name')
                        ->searchable()
                        ->visible(fn ($get) => $get('mail_type') === 'incoming')
                        ->required(fn ($get) => $get('mail_type') === 'incoming')
                        ->preload(),
                    TextInput::make('recipient')
                        ->visible(fn ($get) => $get('mail_type') === 'outgoing')
                        ->required(fn ($get) => $get('mail_type') === 'outgoing'),
                    DatePicker::make('sender_date')
                        ->label('Mail Date'),
                ]),
                // Opsional: Hilangkan shadow bawaan section kalau mau flat
                // ->compact() 

                Section::make('Status & Attachment') 
                ->schema([
                    // CameraField::make('photo')
                    //         ->label('Ambil Foto')
                    //         ->required()

                    Repeater::make('mailStatuses')
                        ->schema([
                            
                            // --- PILIH STATUS DARI TABLE MAILMASTER ---
                            Select::make('status')
                                ->label('Mail Status')
                                ->searchable()
                                ->required()
                                ->columnSpanFull()
                                ->options(fn () => MailMaster::where('owner', 'mailStatus.status')->orderBy('seq')
                                    ->pluck('item_name', 'item_name')
                                ),

                            DatePicker::make('date')
                                ->default(now())
                                ->required(),

                            TimePicker::make('time')
                                ->default(now())
                                ->required(),

                            Hidden::make('photo'),

                            Radio::make('upload_method')
                                ->label('Photo Source')
                                ->options([
                                    'camera' => 'Camera',
                                    'upload' => 'Upload File',
                                ])
                                ->default('camera')
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
                                ->label('Pilih File')
                                ->columnSpanFull()
                                ->directory('status-photos') // Simpan file upload di sini
                                ->visible(fn ($get) => $get('upload_method') === 'upload'),
                        ])
                        ->addActionLabel('Add Status')
                        ->defaultItems(1)
                        ->columns(2)
                            ]),

                ViewField::make('camera_script')
                    ->view('filament.components.camera-script')
                    ->hiddenLabel()
                    ->dehydrated(false),
        ]);
    }
}
