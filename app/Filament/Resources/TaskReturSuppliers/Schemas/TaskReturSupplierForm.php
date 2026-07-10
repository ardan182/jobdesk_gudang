<?php

namespace App\Filament\Resources\TaskReturSuppliers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class TaskReturSupplierForm
{
    public static function getFormFields(): array
    {
        return [
            TextInput::make('nama_supplier_ekspedisi')
                ->label('Nama Supplier / Ekspedisi')
                ->required(),
            TextInput::make('no_plat_mobil')
                ->label('No Plat Mobil')
                ->required(),
            TextInput::make('nama_sopir')
                ->label('Nama Sopir')
                ->required(),
            TimePicker::make('jam_muat')
                ->label('Jam Muat')
                ->required(),
            TextInput::make('jumlah_kolian')
                ->label('Jumlah Kolian')
                ->required()
                ->numeric(),
            TextInput::make('admin_sj_retur')
                ->label('Admin SJ Retur')
                ->required(),
            Select::make('status')
                ->label('Status')
                ->options([
                    'servis' => 'Servis',
                    'tukar' => 'Tukar',
                    'pot_nota' => 'Pot Nota',
                ])
                ->required(),
            Textarea::make('keterangan')
                ->label('Keterangan')
                ->columnSpanFull(),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components(self::getFormFields());
    }
}
