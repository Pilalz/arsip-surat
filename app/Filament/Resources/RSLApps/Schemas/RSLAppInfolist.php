<?php

namespace App\Filament\Resources\RSLApps\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\Entry;
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
                        
                        Grid::make(2)
                            ->schema([
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
                                    ->formatStateUsing(fn ($state) => ucwords(strtolower($state))),

                                TextEntry::make('date')
                                    ->hiddenLabel()
                                    ->date('d F Y')
                                    ->icon('heroicon-m-calendar')
                                    ->color('gray'),

                                TextEntry::make('subject1')
                                    ->label('Kategori')
                                    ->badge()
                                    ->color('info')
                                    ->formatStateUsing(fn ($state) => ucwords(strtolower($state))),

                                TextEntry::make('subject2')
                                    ->label('Keterangan Tambahan')
                                    ->placeholder('-')
                                    ->visible(fn ($record) => $record->subject2), 
                            ]),
                        
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

                                        TextEntry::make('kurir')
                                            ->label('Nama Kurir')
                                            ->weight(FontWeight::SemiBold)
                                            ->placeholder('-'),                                        
                                        
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
                                        TextEntry::make('recipient_info')
                                            ->label('Nama')
                                            ->weight(FontWeight::SemiBold)
                                            ->state(function ($record) {
                                                return $record->recipientContact->name ?? $record->recipient;
                                            }),
                                    ])->columnSpan(1),
                            ]),
                    ]),

                Section::make('Status & Photo')
                    ->icon('heroicon-m-photo')
                    // ->collapsible()
                    ->schema([
                        RepeatableEntry::make('mailStatuses') // <--- WAJIB SAMA dengan nama fungsi relasi di Model RSLApp
                            ->schema([
                                // Kita bagi layout per item jadi 2 kolom: Kiri (Info), Kanan (Foto)
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('status')
                                            ->weight(\Filament\Support\Enums\FontWeight::Bold)
                                            ->badge()
                                            ->color('info')
                                            ->formatStateUsing(fn ($state) => ucwords(strtolower($state))),
                                        
                                        TextEntry::make('date')
                                            ->icon('heroicon-m-calendar')
                                            ->date('d F Y'),

                                        TextEntry::make('time')
                                            ->icon('heroicon-m-clock')
                                            ->time('H:i'),

                                        Entry::make('photo_lightbox') 
                                            ->label('Lampiran Bukti') // Label kolom
                                            ->hiddenLabel()
                                            ->view('infolists.components.lightbox-image'),

                                        TextEntry::make('recipient')
                                            ->icon('heroicon-m-user')
                                            ->visible(fn ($state) => filled($state)),
                                    ])
                                    ->columnSpanFull(),
                            ])
                    ]),

                    ViewEntry::make('global_lightbox')
                        ->view('filament.components.global-lightbox')
                        ->hiddenLabel(),
            ]);
    }
}
