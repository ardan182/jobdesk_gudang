<?php

namespace App\Filament\Resources\MasterSopirs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MasterSopirForm
{
    public static function getFormFields(): array
    {
        return [
            TextInput::make('nama_sopir')
                ->label('Nama Sopir')
                ->required(),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components(self::getFormFields());
    }
}
