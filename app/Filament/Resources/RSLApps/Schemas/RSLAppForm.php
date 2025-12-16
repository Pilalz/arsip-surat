<?php

namespace App\Filament\Resources\RSLApps\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\Contact;
use Filament\Forms\Get;

use App\Forms\Components\CameraField;

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
                    DatePicker::make('sender_date'),
                ]),
                // Opsional: Hilangkan shadow bawaan section kalau mau flat
                // ->compact() 
                Section::make('Mail Attachments') 
                ->schema([
                    CameraField::make('photo')
                            ->label('Ambil Foto')
                            ->required()
                ])
        ]);
    }
}
