<?php

namespace App\Filament\Resources\Contacts\Tables;

use App\Models\Contact;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kontak')
                    ->weight('bold') // Tebalkan nama
                    ->icon('heroicon-m-user')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge() // Ubah jadi Badge warna-warni
                    ->colors([
                        'info' => 'internal',
                        'warning' => 'external',
                    ])
                    ->icons([
                        'heroicon-m-building-office' => 'internal',
                        'heroicon-m-globe-alt' => 'external',
                    ]),

                TextColumn::make('phone')
                    ->label('Telepon')
                    ->icon('heroicon-m-phone')
                    ->copyable() // Biar bisa dicopy user
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-m-envelope')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true), // Sembunyikan default biar ga penuh

                // MENAMPILKAN NAMA INDUK (BUKAN ANGKA ID)
                // Pastikan kamu punya relasi 'parent' di Model Contact ya
                TextColumn::make('parent.name') 
                    ->label('Induk/Atasan')
                    ->placeholder('-')
                    ->description(fn (Contact $record) => $record->upper_contact_id ? 'ID: ' . $record->upper_contact_id : null)
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Filter Tipe')
                    ->options([
                        'internal' => 'Internal Office',
                        'external' => 'External Client',
                    ]),
            ])
            ->actions([
                // Ganti jadi icon button biar tabel ga penuh
                ViewAction::make()->iconButton(),
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}