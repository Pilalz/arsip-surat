<?php

namespace App\Filament\Resources\RSLApps\Schemas;

use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class RSLAppForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->schema([
            // --- SECTION 1: INFORMASI UTAMA ---
            Section::make('Informasi Dasar Surat')
                ->description('Data administrasi penomoran dan tanggal.')
                ->columns(2) // Bagi jadi 2 kolom kiri-kanan
                ->schema([
                    
                    TextInput::make('mail_number')
                        ->label('Nomor Surat')
                        ->required()
                        ->placeholder('Contoh: 001/SRT/XII/2025')
                        ->maxLength(255),

                    Select::make('mail_type')
                        ->label('Jenis Surat')
                        ->options([
                            'incoming' => 'Surat Masuk (Incoming)',
                            'outgoing' => 'Surat Keluar (Outgoing)',
                        ])
                        ->native(false)
                        ->required(),

                    DatePicker::make('date')
                        ->label('Tanggal Dicatat')
                        ->default(now())
                        ->required(),

                    DatePicker::make('sender_date')
                        ->label('Tanggal Surat (Dari Pengirim)'),
                ]),

            // --- SECTION 2: PENGIRIM & PENERIMA ---
            Section::make('Identitas')
                ->description('Informasi pengirim dan penerima surat.')
                ->columns(2)
                ->schema([
                    // Group Pengirim
                    TextInput::make('sender')
                        ->label('Nama Pengirim')
                        ->prefixIcon('heroicon-m-user'),
                    
                    TextInput::make('sender_id')
                        ->label('ID Pengirim')
                        ->numeric()
                        ->placeholder('Opsional (Angka)'),

                    // Group Penerima
                    TextInput::make('recipient')
                        ->label('Nama Penerima')
                        ->prefixIcon('heroicon-m-paper-airplane'),

                    TextInput::make('recipient_id')
                        ->label('ID Penerima')
                        ->numeric()
                        ->placeholder('Opsional (Angka)'),
                ]),

            // --- SECTION 3: ISI SURAT ---
            Section::make('Isi & Keterangan')
                ->schema([
                    Textarea::make('subject1')
                        ->label('Perihal Utama')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(), // Lebar full

                    Textarea::make('subject2')
                        ->label('Keterangan Tambahan')
                        ->placeholder('Isi jika ada catatan khusus...')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
