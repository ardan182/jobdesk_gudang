<?php

namespace App\Filament\Resources\WarehouseEmployees\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WarehouseEmployeeForm
{
    public static function getFormFields(): array
    {
        return [
            TextInput::make('nama_karyawan')
                ->label('Nama Karyawan')
                ->required(),
            TextInput::make('no_whatsapp')
                ->label('No WhatsApp')
                ->tel()
                ->placeholder('Contoh: 08123456789'),
            Select::make('division_id')
                ->label('Divisi')
                ->relationship('division', 'nama_divisi')
                ->searchable()
                ->preload()
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
