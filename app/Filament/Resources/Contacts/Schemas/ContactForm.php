<?php

namespace App\Filament\Resources\Contacts\Schemas;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Utama')
                    ->description('Identitas utama kontak.')
                    ->icon('heroicon-m-user') // Icon Header
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            // ->placeholder('Contoh: PT. Sumber Makmur')
                            ->prefixIcon('heroicon-m-user-circle') // Icon Input
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->prefixIcon('heroicon-m-envelope')
                            ->placeholder('email@domain.com'),

                        TextInput::make('phone')
                            ->label('No. Telepon / WA')
                            ->tel()
                            ->prefixIcon('heroicon-m-phone')
                            ->placeholder('0812...'),

                        Select::make('upper_contact_id')
                            ->label('Atasan')
                            ->relationship(
                                name: 'upperContact',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query, ?Contact $record) {
                                    if ($record) {
                                        $query->where('contact_id', '!=', $record->contact_id);
                                    }
                                    return $query;
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih jika ada...')
                            ->getOptionLabelFromRecordUsing(fn (?Contact $record) => "{$record->name} - {$record->type}")
                            ->options(function (?Contact $record) {
                                return Contact::query()
                                    ->when($record, fn ($query) => $query->where('contact_id', '!=', $record->contact_id))
                                    ->pluck('name', 'contact_id');
                            })
                            ->prefixIcon('heroicon-m-arrow-up-circle'),
                    ]),

                    Section::make('Detail Kontak')
                        ->icon('heroicon-m-identification')
                        ->schema([
                            
                            // Ganti Select biasa jadi Toggle (Lebih keren buat opsi dikit)
                            ToggleButtons::make('type')
                                ->label('Tipe Kontak')
                                ->options([
                                    'internal' => 'Internal',
                                    'external' => 'External',
                                ])
                                ->colors([
                                    'internal' => 'info',
                                    'external' => 'warning',
                                ])
                                ->icons([
                                    'internal' => 'heroicon-m-building-office',
                                    'external' => 'heroicon-m-globe-alt',
                                ])
                                ->default('external')
                                ->inline(),

                            TextInput::make('id_number')
                                ->label('ID Number'),

                            TextInput::make('description'),

                            Textarea::make('address')
                                ->label('Alamat Domisili')
                                ->placeholder('Masukkan alamat lengkap...')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
            ]);
    }
}