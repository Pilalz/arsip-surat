<?php

namespace App\Filament\Resources\RSLApps\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

// Hapus use Section karena tidak dipakai di Table
// use Filament\Schemas\Components\Section; 

class RSLAppsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('mail_number')
                    ->searchable(),
                
                TextColumn::make('mail_type')
                    ->badge() // Kasih badge biar bagus
                    ->colors([
                        'success' => 'incoming',
                        'warning' => 'outgoing',
                    ])
                    ->formatStateUsing(fn ($state) => ucwords(strtolower($state))),
                
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                
                TextColumn::make('subject1')
                    ->label('Subject')
                    ->limit(30)
                    ->formatStateUsing(fn ($state) => ucwords(strtolower($state))),
                
                TextColumn::make('subject2')
                    ->label('Description')
                    ->limit(30)
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('sender_info')
                ->label('Sender')
                ->searchable(query: function (Builder $query, string $search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('sender', 'ilike', "%{$search}%")
                            ->orWhereHas('senderContact', function ($relQuery) use ($search) {
                                $relQuery->where('name', 'ilike', "%{$search}%");
                            });
                    });
                })
                ->state(function ($record) {
                    return $record->senderContact->name ?? $record->sender;
                }),

                TextColumn::make('kurir')
                    ->label('Kurir (Courier)')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('recipient_info')
                    ->label('Recipient')
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->where(function ($subQuery) use ($search) {
                            $subQuery->where('recipient', 'ilike', "%{$search}%")
                                ->orWhereHas('recipientContact', function ($relQuery) use ($search) {
                                    $relQuery->where('name', 'ilike', "%{$search}%");
                                });
                        });
                    })
                    ->state(function ($record) {
                        return $record->recipientContact->name ?? $record->recipient;
                    }),

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
                    ->preload(),

                SelectFilter::make('subject1')
                    ->label('Subject')
                    ->options([
                        'purchasing' => 'Purchasing',
                        'non purchasing' => 'Non Purchasing',
                    ])
                    ->preload(),

                Filter::make('date')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('created_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date) => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date) => $query->whereDate('date', '<=', $date),
                            );
                    })
                    // Opsional: Tampilkan indikator kalau filter aktif
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Dari: ' . \Carbon\Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Sampai: ' . \Carbon\Carbon::parse($data['created_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
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