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
                    ->label('No. Surat')
                    ->weight('bold'),

                TextColumn::make('mail_type')
                    ->badge()
                    ->colors([
                        'success' => 'incoming',
                        'warning' => 'outgoing',
                    ]),

                TextColumn::make('date')
                    ->date('d M Y')
                    ->label('Tanggal'),

                TextColumn::make('subject1')
                    ->label('Subject'),

                TextColumn::make('subject2')
                    ->label('Keterangan')
                    ->placeholder('-'),

                TextColumn::make('senderContact.name')
                    ->label('Sender'),
                
                TextColumn::make('recipientContact.name')
                    ->label('Recipient'),
            ])
            ->paginated(false); // Matikan pagination karena cuma 5 data
    }
}