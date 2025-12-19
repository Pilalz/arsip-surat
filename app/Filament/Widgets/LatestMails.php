<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\RSLApps\RSLAppResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\RSLApp;
use Filament\Tables\Columns\TextColumn;

class LatestMails extends BaseWidget
{
    // Atur urutan tampilan di dashboard (opsional)
    protected static ?int $sort = 3;

    // Lebar penuh
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Ambil dari Resource kita agar konsisten
                // Atau query manual: RSLApp::query()->latest()
                RSLApp::query()->latest('mail_id')->limit(5)
            )
            ->columns([
                TextColumn::make('mail_number')
                    ->label('Mail Number')
                    ->weight('bold'),

                TextColumn::make('mail_type')
                    ->badge()
                    ->colors([
                        'success' => 'incoming',
                        'warning' => 'outgoing',
                    ]),

                TextColumn::make('date')
                    ->date('d M Y'),

                TextColumn::make('subject1')
                    ->label('Subject'),

                TextColumn::make('subject2')
                    ->label('Description')
                    ->placeholder('-'),

                TextColumn::make('sender_info')
                    ->label('Sender')
                    ->state(function ($record) {
                        return $record->senderContact->name ?? $record->sender;
                    }),
                
                TextColumn::make('recipient_info')
                    ->label('Recipient')
                    ->state(function ($record) {
                        return $record->recipientContact->name ?? $record->recipient;
                    }),
            ])
            ->paginated(false); // Matikan pagination karena cuma 5 data
    }
}