<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('email')
                        ->label('Email address'),
                    TextEntry::make('email_verified_at')
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('roles.name')
                        ->badge()
                        ->placeholder('-'),
                    TextEntry::make('created_at')
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('updated_at')
                        ->dateTime()
                        ->placeholder('-'),
                ]),
            ]);
    }
}
