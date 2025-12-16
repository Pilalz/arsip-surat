<?php

namespace App\Filament\Resources\RSLApps\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Grouping\Group;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Schema;

class RSLAppInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([    
                        TextEntry::make('mail_number')
                            ->hiddenLabel() // Label disembunyikan biar fokus ke nomor
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large)
                            ->icon('heroicon-m-document-text')
                            ->copyable(), // Biar user bisa copy nomor surat
                        
                        TextEntry::make('mail_type')
                            ->hiddenLabel()
                            ->badge()
                            ->colors([
                                'success' => 'incoming',
                                'warning' => 'outgoing',
                            ])
                            ->icons([
                                'heroicon-m-arrow-down-tray' => 'incoming',
                                'heroicon-m-paper-airplane' => 'outgoing',
                            ])
                            ->alignRight(),

                        TextEntry::make('date')
                            ->hiddenLabel()
                            ->date('d F Y')
                            ->icon('heroicon-m-calendar')
                            ->alignRight()
                            ->color('gray'),
                    ]),

                // --- BAGIAN 2: DETAIL SURAT (2 Kolom) ---
                Section::make('Detail Isi Surat')
                    ->icon('heroicon-m-bars-3-bottom-left')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('subject1')
                            ->label('Kategori')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('subject2')
                            ->label('Keterangan Tambahan')
                            ->placeholder('-')
                            ->visible(fn ($record) => $record->subject2), 
                    ]),

                // --- BAGIAN 3: PELAKU SURAT (Grid 2 Kolom) ---
                Grid::make(2)
                    ->schema([
                        // KOTAK PENGIRIM
                        Section::make('Pengirim (Sender)')
                            ->icon('heroicon-m-user')
                            ->schema([
                                TextEntry::make('sender_info')
                                    ->label('Nama Pengirim')
                                    ->weight(FontWeight::SemiBold)
                                    ->state(function ($record) {
                                        return $record->senderContact->name ?? $record->sender;
                                    }),
                                
                                TextEntry::make('sender_date')
                                    ->label('Tanggal Kirim')
                                    ->date('d M Y')
                                    ->icon('heroicon-m-clock')
                                    ->placeholder('-'),
                            ])->columnSpan(1),

                        // KOTAK PENERIMA
                        Section::make('Penerima (Recipient)')
                            ->icon('heroicon-m-user-group')
                            ->schema([
                                TextEntry::make('recipientContact.name')
                                    ->weight(FontWeight::SemiBold)
                                    ->label('Nama'),
                            ])->columnSpan(1),
                    ]),

                // --- BAGIAN 4: BUKTI FOTO (Secure Image) ---
                Section::make('Lampiran Bukti')
                    ->icon('heroicon-m-photo')
                    ->collapsible()
                    ->schema([
                        ImageEntry::make('photo')
                            ->hiddenLabel()
                            ->state(fn ($record) => $record->photo ? route('view.private.image', ['filename' => basename($record->photo)]) : null)
                            ->extraImgAttributes([
                                'class' => 'rounded-lg shadow-md border border-gray-200 w-full h-full',
                                'style' => 'object-fit: contain;',
                            ])
                            ->checkFileExistence(false)
                            ->placeholder('Tidak ada lampiran foto.'),
                    ]),
            ]);
    }
}
