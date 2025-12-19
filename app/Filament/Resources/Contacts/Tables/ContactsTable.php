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
                    ->weight('bold') // Tebalkan nama
                    ->icon('heroicon-m-user')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->badge()
                    ->colors([
                        'info' => 'internal',
                        'warning' => 'external',
                    ])
                    ->icons([
                        'heroicon-m-building-office' => 'internal',
                        'heroicon-m-globe-alt' => 'external',
                    ]),

                TextColumn::make('phone')
                    ->icon('heroicon-m-phone')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('upperContact.name') 
                    ->label('Supervisor')
                    ->placeholder('-'),

                TextColumn::make('address')
                    ->icon('heroicon-m-map-pin')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('id_number')
                    ->label('ID')
                    ->icon('heroicon-m-identification')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('description')
                    ->toggleable(isToggledHiddenByDefault: true),
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