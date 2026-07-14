<?php

namespace App\Filament\Resources\Expeditions\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExpeditionForm
{
    public static function getFormFields(): array
    {
        return [
            TextInput::make('nama_ekspedisi')
                ->label('Nama Ekspedisi')
                ->required(),
            TextInput::make('no_telepon')
                ->label('No Telepon')
                ->tel()
                ->placeholder('Contoh: 022-XXXXXX atau 0812XXXXXXXX'),
            Textarea::make('alamat')
                ->label('Alamat')
                ->rows(3)
                ->columnSpanFull(),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components(self::getFormFields());
    }
}
