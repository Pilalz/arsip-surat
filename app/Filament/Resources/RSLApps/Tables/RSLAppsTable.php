<?php

namespace App\Filament\Resources\RSLApps\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RSLAppsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mail_number')
                    ->searchable(),
                TextColumn::make('mail_type'),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('subject1')
                    ->searchable(),
                TextColumn::make('subject2')
                    ->searchable(),
                // TextColumn::make('sender_id')
                //     ->numeric()
                //     ->sortable(),
                TextColumn::make('sender')
                    ->searchable(),
                // TextColumn::make('recipient_id')
                //     ->numeric()
                //     ->sortable(),
                TextColumn::make('recipient')
                    ->searchable(),
                TextColumn::make('sender_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
