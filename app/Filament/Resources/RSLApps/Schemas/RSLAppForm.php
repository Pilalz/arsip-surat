<?php

namespace App\Filament\Resources\RSLApps\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\View;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

use App\Forms\Components\CameraField;

class RSLAppForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make() 
                ->schema([
                    TextInput::make('mail_number')
                    ->required(),
                Select::make('mail_type')
                    ->options([
                        'incoming' => 'Incoming',
                        'outgoing' => 'Outgoing',
                    ])
                    ->required(),
                DatePicker::make('date')
                    ->required(),
                Select::make('subject1')
                    ->options([
                        'purchasing' => 'Purchasing',
                        'non' => 'Non-Purchasing',
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state === 'purchasing') {
                            $set('subject2', null);
                        }
                    }),
                TextInput::make('subject2')
                    ->label('Subject 2')
                    ->visible(fn ($get) => $get('subject1') === 'non')
                    ->required(fn ($get) => $get('subject1') === 'non')
                    ->reactive(),
                Select::make('sender_id')
                    ->label('Sender')
                    ->relationship('senderContact', 'name')
                    ->searchable()
                    ->required()
                    ->preload() // biar cepat
                    ->createOptionForm([
                        TextInput::make('name')->required(),
                        TextInput::make('address'),
                        TextInput::make('phone'),
                        TextInput::make('email'),
                        Select::make('type')
                            ->options([
                                'internal' => 'Internal',
                                'external' => 'External',
                            ]),
                    ]),
                // TextInput::make('sender'),
                Select::make('recipient_id')
                    ->label('Recipient')
                    ->relationship('recipientContact', 'name')
                    ->searchable()
                    ->required()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')->required(),
                        TextInput::make('address'),
                        TextInput::make('phone'),
                        TextInput::make('email'),
                        Select::make('type')
                            ->options([
                                'internal' => 'Internal',
                                'external' => 'External',
                            ]),
                    ]),
                // TextInput::make('recipient'),
                DatePicker::make('sender_date'),
                CameraField::make('photo')
                        ->label('Ambil Foto')
                        ->required()
                ])
                // Opsional: Hilangkan shadow bawaan section kalau mau flat
                // ->compact() 
        ]);
    }
}
