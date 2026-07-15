<?php

namespace App\Filament\Resources\TaskDatangMobilSuppliers\Schemas;

use App\Models\Expedition;
use App\Models\Supplier;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class TaskDatangMobilSupplierForm
{
    public static function getFormFields(): array
    {
        return [
            Select::make('supplier_id')
                ->label('Supplier')
                ->options(Supplier::pluck('nama_supplier', 'id'))
                ->searchable()
                ->required(),
            Select::make('expedition_id')
                ->label('Ekspedisi')
                ->options(Expedition::pluck('nama_ekspedisi', 'id'))
                ->searchable()
                ->nullable(),
            TextInput::make('nama_sopir')
                ->label('Nama Sopir')
                ->required(),
            TextInput::make('no_plat_mobil')
                ->label('No Plat Mobil')
                ->required(),
            DatePicker::make('tanggal_datang')
                ->label('Tanggal Datang')
                ->default(now()->format('Y-m-d'))
                ->required(),
            TimePicker::make('jam_datang')
                ->label('Jam Datang')
                ->seconds(false)
                ->default(now()->format('H:i'))
                ->required(),
            TimePicker::make('jam_selesai')
                ->label('Jam Selesai')
                ->seconds(false)
                ->nullable(),
            Textarea::make('keterangan')
                ->label('Keterangan')
                ->columnSpanFull(),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components(self::getFormFields());
    }
}
