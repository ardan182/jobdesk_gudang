<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function getFormFields(): array
    {
        return [
            TextInput::make('kode_supplier')
                ->label('Kode Supplier')
                ->required()
                ->unique(ignoreRecord: true)
                ->regex('/^[A-Z0-9-]+$/')
                ->extraAlpineAttributes(['x-on:input' => '$el.value = $el.value.toUpperCase()']),
            TextInput::make('nama_supplier')
                ->label('Nama Supplier')
                ->required(),
            TextInput::make('no_telepon')
                ->label('No Telepon')
                ->tel()
                ->placeholder('Contoh: 021-XXXXXX'),
            Textarea::make('alamat')
                ->label('Alamat')
                ->rows(3)
                ->columnSpanFull(),
            Textarea::make('keterangan')
                ->label('Keterangan')
                ->rows(2)
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
