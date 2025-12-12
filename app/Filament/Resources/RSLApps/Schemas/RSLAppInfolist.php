<?php

namespace App\Filament\Resources\RSLApps\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RSLAppInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('mail_number'),
                TextEntry::make('mail_type'),
                TextEntry::make('date')
                    ->date(),
                TextEntry::make('subject1')
                    ->placeholder('-'),
                TextEntry::make('subject2')
                    ->placeholder('-'),
                TextEntry::make('sender_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('sender')
                    ->placeholder('-'),
                TextEntry::make('recipient_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('recipient')
                    ->placeholder('-'),
                TextEntry::make('sender_date')
                    ->date()
                    ->placeholder('-'),
            ]);
    }
}
