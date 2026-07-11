<?php

namespace App\Filament\Resources\MasterMobils\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MasterMobilForm
{
    public static function getFormFields(): array
    {
        return [
            TextInput::make('nama_mobil')
                ->label('Nama Mobil')
                ->required(),
            TextInput::make('no_plat_mobil')
                ->label('No Plat Mobil')
                ->required(),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components(self::getFormFields());
    }
}
