<?php

namespace App\Filament\Resources\MasterTokos\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MasterTokoForm
{
    public static function getFormFields(): array
    {
        return [
            TextInput::make('nama_toko')
                ->label('Nama Toko')
                ->required(),
            Textarea::make('alamat')
                ->label('Alamat'),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components(self::getFormFields());
    }
}
