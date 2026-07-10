<?php

namespace App\Filament\Resources\TaskTerimaSuppliers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class TaskTerimaSupplierForm
{
    public static function getFormFields(): array
    {
        return [
            TextInput::make('nama_supplier_ekspedisi')
                ->label('Nama Supplier / Ekspedisi')
                ->required(),
            TextInput::make('no_po_referensi')
                ->label('No PO Referensi')
                ->required(),
            TextInput::make('jumlah_kolian')
                ->label('Jumlah Kolian')
                ->required()
                ->numeric(),
            TimePicker::make('jam_bongkar')
                ->label('Jam Bongkar')
                ->seconds(false)
                ->step(60)
                ->extraAttributes(['lang' => 'id-ID'])
                ->required(),
            TextInput::make('nama_sopir')
                ->label('Nama Sopir')
                ->required(),
            Select::make('status')
                ->label('Status')
                ->options([
                    'komplit' => 'Komplit',
                    'kurang' => 'Kurang',
                    'lebih' => 'Lebih',
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
