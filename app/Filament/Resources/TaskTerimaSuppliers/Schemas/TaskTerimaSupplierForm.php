<?php

namespace App\Filament\Resources\TaskTerimaSuppliers\Schemas;

use App\Models\ArrivalSupplierTruck;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaskTerimaSupplierForm
{
    public static function getFormFields(): array
    {
        return [
            Section::make('Informasi Penerimaan Barang Supplier')
                ->description('Pilih mobil datang lalu isi data bongkar barang.')
                ->icon('heroicon-o-truck')
                ->columns(3)
                ->schema([
                    Select::make('arrival_supplier_truck_id')
                        ->label('Pilih Mobil Datang Supplier')
                        ->required()
                        ->options(function ($component) {
                            $record = $component->getRecord();

                            $takenIds = \App\Models\TaskTerimaSupplier::whereNotNull('arrival_supplier_truck_id')
                                ->pluck('arrival_supplier_truck_id')
                                ->toArray();

                            $query = ArrivalSupplierTruck::whereIn('status', ['MENGANTRI', 'PROSES'])
                                ->whereIn('jenis_kiriman', ['DATANG', 'DATANG & RETUR'])
                                ->whereNotIn('id', $takenIds);

                            if ($record && $record->arrival_supplier_truck_id) {
                                $query->orWhere('id', $record->arrival_supplier_truck_id);
                            }

                            return $query->get()->mapWithKeys(fn ($truck) => [
                                $truck->id => "{$truck->no_plat_mobil} - {$truck->supplier?->nama_supplier} - {$truck->jenis_kiriman} - (" . ($truck->tanggal_datang?->format('d/m/Y') ?? '-') . ')',
                            ]);
                        })
                        ->searchable()
                        ->preload()
                        ->placeholder('Pilih mobil datang...')
                        ->disabled(fn ($component) => $component->getRecord() !== null)
                        ->columnSpanFull()
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set, $get) {
                            if (!$state) return;
                            $truck = ArrivalSupplierTruck::with('supplier', 'expedition')->find($state);
                            if (!$truck) return;

                            $set('nama_supplier_ekspedisi', $truck->supplier?->nama_supplier
                                . ($truck->expedition ? ' / ' . $truck->expedition->nama_ekspedisi : ''));
                            $set('jenis_kiriman_tampil', $truck->jenis_kiriman ?? '-');
                            $set('nama_sopir', $truck->nama_sopir ?? '');
                            $set('jam_datang', $truck->jam_datang ? Carbon::parse($truck->jam_datang)->format('H:i') : '');
                        }),
                    TextInput::make('nama_supplier_ekspedisi')
                        ->label('Supplier / Ekspedisi')
                        ->prefixIcon('heroicon-m-building-office')
                        ->placeholder('Terisi otomatis')
                        ->disabled()
                        ->dehydrated(true),
                    TextInput::make('jenis_kiriman_tampil')
                        ->label('Jenis Kiriman')
                        ->prefixIcon('heroicon-m-truck')
                        ->placeholder('Terisi otomatis')
                        ->disabled()
                        ->dehydrated(false),
                    TimePicker::make('jam_datang')
                        ->label('Jam Datang')
                        ->prefixIcon('heroicon-m-clock')
                        ->placeholder('Terisi otomatis')
                        ->seconds(false)
                        ->disabled()
                        ->dehydrated(true),
                    TextInput::make('nama_sopir')
                        ->label('Nama Sopir')
                        ->prefixIcon('heroicon-m-user')
                        ->placeholder('Terisi otomatis')
                        ->disabled()
                        ->dehydrated(true),
                    TextInput::make('no_po_referensi')
                        ->label('No PO Referensi')
                        ->prefixIcon('heroicon-m-document-text')
                        ->placeholder('Kosongkan jika PO belum terbit')
                        ->helperText('Nomor PO pembelian (opsional)')
                        ->nullable(),
                    TextInput::make('jumlah_kolian')
                        ->label('Jumlah Kolian')
                        ->prefixIcon('heroicon-m-cube')
                        ->placeholder('0')
                        ->helperText('Total barang/koli diterima')
                        ->numeric(),
                    TimePicker::make('jam_bongkar')
                        ->label('Jam Bongkar')
                        ->prefixIcon('heroicon-m-clock')
                        ->helperText('Jam mulai bongkar')
                        ->seconds(false)
                        ->step(60)
                        ->extraAttributes(['lang' => 'id-ID'])
                        ->required(),
                    TimePicker::make('selesai_bongkar')
                        ->label('Selesai Bongkar')
                        ->prefixIcon('heroicon-m-clock')
                        ->helperText('Wajib diisi jika status Selesai')
                        ->seconds(false)
                        ->step(60)
                        ->required(fn ($get) => $get('status') === 'SELESAI')
                        ->extraAttributes(['lang' => 'id-ID']),
                    TextInput::make('lama_bongkar_tampil')
                        ->label('Lama Bongkar')
                        ->prefixIcon('heroicon-m-clock')
                        ->disabled()
                        ->dehydrated(false)
                        ->afterStateHydrated(function ($component, $state) {
                            $record = $component->getRecord();
                            if ($record && $record->jam_bongkar && $record->selesai_bongkar) {
                                $minutes = \Carbon\Carbon::parse($record->jam_bongkar)->diffInMinutes(\Carbon\Carbon::parse($record->selesai_bongkar));
                                $h = intdiv($minutes, 60);
                                $m = $minutes % 60;
                                $component->state($h > 0 ? "{$h}j {$m}m" : "{$m}m");
                            }
                        }),
                    TextInput::make('lembar_sj')
                        ->label('Lembar SJ')
                        ->prefixIcon('heroicon-m-document-duplicate')
                        ->placeholder('0')
                        ->helperText('Jumlah lembar surat jalan')
                        ->numeric(),
                    Select::make('status')
                        ->label('Status Bongkar')
                        ->prefixIcon('heroicon-m-check-badge')
                        ->options([
                            'DRAFT' => 'Draft',
                            'SELESAI' => 'Selesai',
                        ])
                        ->default('DRAFT')
                        ->helperText('Pilih status hasil bongkar')
                        ->nullable()
                        ->placeholder('Pilih status...'),
                    Select::make('helpers')
                        ->label('Helpers')
                        ->options(\App\Models\WarehouseEmployee::pluck('nama_karyawan', 'id'))
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->placeholder('Pilih helpers...')
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record && $record->helpers->count() > 0) {
                                $component->state($record->helpers->pluck('id')->toArray());
                            }
                        }),
                    Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->placeholder('Catatan tambahan...')
                        ->columnSpanFull(),
                ]),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::getFormFields());
    }
}
