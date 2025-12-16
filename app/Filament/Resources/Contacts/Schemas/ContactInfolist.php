<?php

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([   
                        TextEntry::make('name'), 
                        TextEntry::make('id_number')
                            ->placeholder('-'),
                        TextEntry::make('phone')
                            ->placeholder('-'),
                        TextEntry::make('email')
                            ->label('Email address')
                            ->placeholder('-'),
                        TextEntry::make('upper_contact_id')
                            ->numeric()
                            ->placeholder('-'),
                    ]),

                // --- BAGIAN 2: DETAIL SURAT (2 Kolom) ---
                Section::make('Detail')
                    ->icon('heroicon-m-bars-3-bottom-left')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('type')
                            ->placeholder('-'),
                        TextEntry::make('description')
                            ->placeholder('-'),
                        TextEntry::make('address')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),    
            ]);
    }
}
