<?php

namespace App\Filament\Resources\SupplierReturnInbound\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class SupplierReturnInboundForm
{
    public static function getFormFields(): array
    {
        return [
            TextInput::make('nama_supplier')
                ->label('Nama Supplier')
                ->nullable(),
            TextInput::make('no_nota_retur')
                ->label('No Nota Retur')
                ->nullable(),
            TextInput::make('nama_ekspedisi')
                ->label('Nama Ekspedisi')
                ->nullable(),
            TextInput::make('jumlah_kolian')
                ->label('Jumlah Kolian')
                ->numeric()
                ->nullable(),
            TextInput::make('nama_supir')
                ->label('Nama Supir')
                ->nullable(),
            TextInput::make('no_plat_mobil')
                ->label('No Plat Mobil')
                ->nullable(),
            DatePicker::make('tanggal_datang')
                ->label('Tanggal Datang')
                ->nullable()
                ->displayFormat('d/m/Y'),
            TimePicker::make('jam_kedatangan')
                ->label('Jam Kedatangan')
                ->seconds(false)
                ->nullable(),
            Textarea::make('keterangan')
                ->label('Keterangan')
                ->nullable()
                ->rows(3),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components(self::getFormFields());
    }
}
