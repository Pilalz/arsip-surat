<?php

namespace App\Filament\Resources\Contacts\Schemas;

use App\Models\Contact;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3) // Kita bagi layar jadi 3 kolom grid
                    ->schema([
                        
                        // --- KOLOM KIRI (LEBAR 2) ---
                        Group::make()
                            ->columnSpan(2)
                            ->schema([
                                Section::make('Informasi Utama')
                                    ->description('Identitas utama kontak.')
                                    ->icon('heroicon-m-user') // Icon Header
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nama Lengkap')
                                            ->required()
                                            ->placeholder('Contoh: PT. Sumber Makmur')
                                            ->prefixIcon('heroicon-m-user-circle') // Icon Input
                                            ->maxLength(255),

                                        Textarea::make('address')
                                            ->label('Alamat Domisili')
                                            ->placeholder('Masukkan alamat lengkap...')
                                            ->rows(5)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // --- KOLOM KANAN (LEBAR 1 - SIDEBAR) ---
                        Group::make()
                            ->columnSpan(1)
                            ->schema([
                                Section::make('Detail Kontak')
                                    ->icon('heroicon-m-identification')
                                    ->schema([
                                        
                                        // Ganti Select biasa jadi Toggle (Lebih keren buat opsi dikit)
                                        ToggleButtons::make('type')
                                            ->label('Tipe Kontak')
                                            ->options([
                                                'internal' => 'Internal',
                                                'external' => 'External',
                                            ])
                                            ->colors([
                                                'internal' => 'info',
                                                'external' => 'warning',
                                            ])
                                            ->icons([
                                                'internal' => 'heroicon-m-building-office',
                                                'external' => 'heroicon-m-globe-alt',
                                            ])
                                            ->default('external')
                                            ->inline(),

                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->prefixIcon('heroicon-m-envelope')
                                            ->placeholder('email@domain.com'),

                                        TextInput::make('phone')
                                            ->label('No. Telepon / WA')
                                            ->tel()
                                            ->prefixIcon('heroicon-m-phone')
                                            ->placeholder('0812...'),

                                        // UX IMPROVEMENT: Jangan suruh user hafal ID angka!
                                        // Gunakan Select searchable cari nama
                                        TextInput::make('upper_contact_id')
                                            ->label('Induk Perusahaan / Atasan'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}