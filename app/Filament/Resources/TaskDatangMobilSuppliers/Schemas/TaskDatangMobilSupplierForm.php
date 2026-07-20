<?php

namespace App\Filament\Resources\TaskDatangMobilSuppliers\Schemas;

use App\Models\Expedition;
use App\Models\Supplier;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaskDatangMobilSupplierForm
{
    public static function getFormFields(): array
    {
        return [
            Section::make('Informasi Kedatangan Mobil Supplier')
                ->description('Masukkan data logistik armada supplier dengan teliti.')
                ->icon('heroicon-o-truck')
                ->columns(2)
                ->schema([
                    TextInput::make('no_plat_mobil')
                        ->label('No Plat Mobil')
                        ->prefixIcon('heroicon-m-identification')
                        ->placeholder('Contoh: B 1234 CD')
                        ->required(),
                    Select::make('supplier_id')
                        ->label('Supplier')
                        ->options(Supplier::pluck('nama_supplier', 'id'))
                        ->searchable()
                        ->preload()
                        ->placeholder('Pilih supplier...')
                        ->required(),
                    Select::make('expedition_id')
                        ->label('Ekspedisi')
                        ->options(Expedition::pluck('nama_ekspedisi', 'id'))
                        ->searchable()
                        ->preload()
                        ->placeholder('Pilih ekspedisi (opsional)...')
                        ->nullable(),
                    TimePicker::make('jam_datang')
                        ->label('Jam Datang')
                        ->seconds(false)
                        ->default(now()->format('H:i'))
                        ->required(),
                    TextInput::make('nama_sopir')
                        ->label('Nama Sopir')
                        ->prefixIcon('heroicon-m-user')
                        ->placeholder('Nama sopir')
                        ->required(),
                    TimePicker::make('jam_selesai')
                        ->label('Jam Selesai')
                        ->seconds(false)
                        ->disabled()
                        ->nullable(),
                    DatePicker::make('tanggal_datang')
                        ->label('Tanggal Datang')
                        ->default(now()->format('Y-m-d'))
                        ->required(),
                    TextInput::make('status')
                        ->label('Status')
                        ->default('PROSES')
                        ->disabled(),
                    Select::make('jenis_kiriman')
                        ->label('Jenis Kiriman')
                        ->options([
                            'DATANG' => 'DATANG',
                            'RETUR' => 'RETUR',
                            'DATANG & RETUR' => 'DATANG & RETUR',
                        ])
                        ->default('DATANG')
                        ->required(),
                    Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->placeholder('Catatan tambahan...'),
                ]),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::getFormFields());
    }
}
