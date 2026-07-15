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
            TimePicker::make('jam_datang')
                ->label('Jam Datang')
                ->seconds(false)
                ->step(60)
                ->extraAttributes(['lang' => 'id-ID']),
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
            TimePicker::make('selesai_bongkar')
                ->label('Selesai Bongkar')
                ->seconds(false)
                ->step(60)
                ->extraAttributes(['lang' => 'id-ID']),
            TextInput::make('lembar_sj')
                ->label('Lembar SJ')
                ->numeric()
                ->default(1),
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
