<?php

namespace App\Filament\Resources\RSLApps\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

// Hapus use Section karena tidak dipakai di Table
// use Filament\Schemas\Components\Section; 

class RSLAppsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // --- LANGSUNG LIST KOLOMNYA DI SINI (JANGAN PAKE SECTION) ---
                
                TextColumn::make('mail_number')
                    ->searchable(),
                
                TextColumn::make('mail_type')
                    ->badge() // Kasih badge biar bagus
                    ->colors([
                        'success' => 'incoming',
                        'warning' => 'outgoing',
                    ]),
                
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                
                TextColumn::make('subject1')
                    ->label('Subject')
                    ->limit(30) // Batasi panjang teks biar rapi
                    ->searchable(),
                
                // TextColumn::make('subject2') ... (Opsional, kalau penuh mending hide)

                TextColumn::make('senderContact.name')
                    ->label('Sender')
                    ->searchable(),

                TextColumn::make('recipientContact.name')
                    ->label('Recipient')
                    ->searchable(),

                TextColumn::make('sender_date')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true), // Sembunyikan default biar ga penuh
            ])
            ->filters([
                SelectFilter::make('mail_type')
                    ->options([
                        'incoming' => 'Incoming',
                        'outgoing' => 'Outgoing',
                    ])
                    ->preload()
            ])
            ->actions([
                ViewAction::make()->iconButton(),   // Pakai iconButton biar rapi
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