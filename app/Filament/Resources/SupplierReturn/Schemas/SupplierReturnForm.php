<?php

namespace App\Filament\Resources\SupplierReturn\Schemas;

use App\Models\ArrivalSupplierTruck;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SupplierReturnForm
{
    public static function getFormFields(): array
    {
        return [
            Section::make('Data Supplier')
                ->description('Pilih truk datang')
                ->schema([
                            Select::make('arrival_supplier_truck_id')
                        ->label('Pilih Mobil Datang Supplier')
                        ->prefixIcon('heroicon-m-truck')
                        ->searchable()
                        ->preload()
                        ->placeholder('Pilih mobil datang...')
                        ->allowHtml()
                        ->disabled(fn ($component) => $component->getRecord() !== null)
                        ->columnSpanFull()
                        ->live()
                        ->options(function ($component) {
                            $record = $component->getRecord();
                            $takenIds = self::getTakenTruckIds($record);
                            $query = ArrivalSupplierTruck::whereIn('status', ['MENGANTRI', 'PROSES'])
                                ->whereIn('jenis_kiriman', ['RETUR', 'DATANG & RETUR'])
                                ->whereNotIn('id', $takenIds);
                            if ($record && $record->arrival_supplier_truck_id) {
                                $query->orWhere('id', $record->arrival_supplier_truck_id);
                            }
                            return $query->get()->mapWithKeys(fn ($truck) => [
                                $truck->id =>
                                    "<span style='background:#22c55e;color:#fff;padding:2px 6px;border-radius:4px;font-size:11px'>"
                                    . "{$truck->no_plat_mobil}"
                                    . '</span>'
                                    . " - {$truck->supplier?->nama_supplier} - "
                                    . "{$truck->jenis_kiriman} - "
                                    . 'TGL DATANG '
                                    . "<span style='background:#ef4444;color:#fff;padding:2px 6px;border-radius:4px;font-size:11px'>"
                                    . ($truck->tanggal_datang?->format('d/m/Y') ?? '-')
                                    . '</span>',
                            ]);
                        })
                        ->afterStateUpdated(function ($state, $set) {
                            if (!$state) return;
                            $truck = ArrivalSupplierTruck::with('supplier', 'expedition')->find($state);
                            if (!$truck) return;
                            $set('nama_supplier', $truck->supplier?->nama_supplier ?? '');
                            $set('nama_ekspedisi', $truck->expedition?->nama_ekspedisi ?? '');
                            $set('nama_supir', $truck->nama_sopir ?? '');
                            $set('no_plat_mobil', $truck->no_plat_mobil ?? '');
                            $set('tanggal_datang', $truck->tanggal_datang?->format('Y-m-d'));
                            $set('jam_kedatangan', $truck->jam_datang ? Carbon::parse($truck->jam_datang)->format('H:i') : '');
                        }),
                    Grid::make(3)
                        ->schema([
                    TextInput::make('nama_supplier')
                        ->label('Nama Supplier')
                        ->prefixIcon('heroicon-m-building-office')
                        ->disabled()
                        ->dehydrated(),
                    TextInput::make('nama_ekspedisi')
                        ->label('Nama Ekspedisi')
                        ->prefixIcon('heroicon-m-truck')
                        ->disabled()
                        ->dehydrated(),
                    TextInput::make('nama_supir')
                        ->label('Nama Supir')
                        ->prefixIcon('heroicon-m-user')
                        ->disabled()
                        ->dehydrated(),
                    TextInput::make('no_plat_mobil')
                        ->label('No Plat Mobil')
                        ->prefixIcon('heroicon-m-identification')
                        ->disabled()
                        ->dehydrated(),
                    DatePicker::make('tanggal_datang')
                        ->label('Tanggal Datang')
                        ->disabled()
                        ->dehydrated()
                        ->displayFormat('d/m/Y'),
                    TimePicker::make('jam_kedatangan')
                        ->label('Jam Kedatangan')
                        ->disabled()
                        ->dehydrated()
                        ->seconds(false),
                        ]),
                ]),
            Section::make('Detail Retur')
                ->description('Pilih jenis pengiriman untuk menampilkan blok retur yang sesuai')
                ->columns(2)
                ->schema([
                    Select::make('jenis_pengiriman')
                        ->label('Jenis Pengiriman')
                        ->prefixIcon('heroicon-m-arrows-right-left')
                        ->helperText('Pilih tipe pengiriman untuk menampilkan field retur yang sesuai.')
                        ->options([
                            'retur_masuk' => 'Retur Masuk',
                            'retur_keluar' => 'Retur Keluar',
                            'datang_dan_keluar' => 'Datang & Keluar',
                        ])
                        ->disabled(fn ($state, $component) => $component?->getRecord() !== null)
                        ->required()
                        ->live()
                        ->columnSpanFull(),
                    TextInput::make('no_nota_retur')
                        ->label('No Nota Retur')
                        ->prefixIcon('heroicon-m-document-text')
                        ->required(),
                    Select::make('status')
                        ->label('Status')
                        ->prefixIcon('heroicon-m-check-badge')
                        ->options([
                            'draft' => 'Draft',
                            'selesai' => 'Selesai',
                        ])
                        ->default('draft')
                        ->required(),
                    Grid::make(2)
                        ->columnSpanFull()
                        ->schema([
                            Fieldset::make('Retur Keluar')
                                ->columns(2)
                                ->columnSpan(fn ($get) => $get('jenis_pengiriman') === 'datang_dan_keluar' ? 1 : 2)
                                ->visible(fn ($get) => in_array($get('jenis_pengiriman'), ['retur_keluar', 'datang_dan_keluar']))
                                ->schema([
                                    Select::make('jenis_retur_keluar')
                                        ->label('Jenis Retur Keluar')
                                        ->prefixIcon('heroicon-m-tag')
                                        ->options([
                                            'potong_nota' => 'Potong Nota',
                                            'servis' => 'Servis',
                                            'ganti_barang' => 'Ganti Barang',
                                        ])
                                        ->required(),
                                    TextInput::make('total_koli_keluar')
                                        ->label('Total Koli Keluar')
                                        ->prefixIcon('heroicon-m-cube')
                                        ->numeric()
                                        ->minValue(1)
                                        ->required(),
                                ]),
                            Fieldset::make('Retur Masuk')
                                ->columns(2)
                                ->columnSpan(fn ($get) => $get('jenis_pengiriman') === 'datang_dan_keluar' ? 1 : 2)
                                ->visible(fn ($get) => in_array($get('jenis_pengiriman'), ['retur_masuk', 'datang_dan_keluar']))
                                ->schema([
                                    Select::make('jenis_retur_masuk')
                                        ->label('Jenis Retur Masuk')
                                        ->prefixIcon('heroicon-m-tag')
                                        ->options([
                                            'servis' => 'Servis',
                                            'ganti_barang' => 'Ganti Barang',
                                        ])
                                        ->required(),
                                    TextInput::make('total_kolian_masuk')
                                        ->label('Total Kolian Masuk')
                                        ->prefixIcon('heroicon-m-cube')
                                        ->numeric()
                                        ->minValue(1)
                                        ->required(),
                                ]),
                        ]),
                    Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->columnSpanFull()
                        ->rows(3),
                ]),
        ];
    }

    private static function getTakenTruckIds($record): array
    {
        $takenIds = \App\Models\SupplierReturn::whereNotNull('arrival_supplier_truck_id')
            ->pluck('arrival_supplier_truck_id')
            ->toArray();
        if ($record && $record->arrival_supplier_truck_id) {
            $takenIds = array_diff($takenIds, [$record->arrival_supplier_truck_id]);
        }
        return $takenIds;
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::getFormFields());
    }
}
