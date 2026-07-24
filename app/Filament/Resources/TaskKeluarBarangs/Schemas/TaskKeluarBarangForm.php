<?php

namespace App\Filament\Resources\TaskKeluarBarangs\Schemas;

use App\Models\BranchShipment;
use App\Models\MasterToko;
use App\Models\WarehouseEmployee;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaskKeluarBarangForm
{
    public static function getFormFields(): array
    {
        return [
            Section::make('Informasi Task')
                ->columns(2)
                ->schema([
                    Select::make('cabang')
                        ->label('Pilih Cabang')
                        ->prefixIcon('heroicon-m-building-storefront')
                        ->options(MasterToko::pluck('nama_toko', 'nama_toko'))
                        ->searchable()
                        ->required()
                        ->disabled(fn ($component) => $component->getRecord() !== null)
                        ->live()
                        ->afterStateUpdated(function ($state, $set, $get) {
                            $set('branch_shipment_id', null);
                            $set('nomor_sj', null);
                            $set('total_qty', null);
                            $set('no_po', null);
                        }),
                    Select::make('branch_shipment_id')
                        ->label('Ambil dari Kiriman Barang')
                        ->prefixIcon('heroicon-m-document-arrow-down')
                        ->disabled(fn ($record) => $record !== null)
                        ->options(function ($get, $record) {
                            $cabang = $get('cabang');
                            if (!$cabang) return [];
                            $query = BranchShipment::where('status', 'selesai')
                                ->where('cabang', $cabang)
                                ->whereNotIn('id', function ($q) {
                                    $q->select('branch_shipment_id')
                                        ->from('task_keluar_barangs')
                                        ->whereNotNull('branch_shipment_id');
                                });

                            if ($record && $record->branch_shipment_id) {
                                $query->orWhere('id', $record->branch_shipment_id);
                            }

                            return $query->get()
                                ->mapWithKeys(fn ($item) => [
                                    $item->id => $item->nomor_sj,
                                ]);
                        })
                        ->searchable()
                        ->preload()
                        ->placeholder('Pilih kiriman barang...')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $set) {
                            if (!$state) {
                                $set('nomor_sj', null);
                                $set('total_qty', null);
                                $set('no_po', null);
                                return;
                            }
                            $shipment = BranchShipment::find($state);
                            if ($shipment) {
                                $set('nomor_sj', $shipment->nomor_sj);
                                $set('total_qty', $shipment->total_qty);
                                $set('no_po', $shipment->no_po);
                            }
                        }),
                    TextInput::make('nomor_sj')
                        ->label('Nomor SJ')
                        ->prefixIcon('heroicon-m-document-text')
                        ->disabled()
                        ->dehydrated(true),
                    TextInput::make('total_qty')
                        ->label('Qty Input')
                        ->prefixIcon('heroicon-m-cube')
                        ->disabled()
                        ->dehydrated(true),
                    TextInput::make('qty_checker')
                        ->label('Qty Checker')
                        ->prefixIcon('heroicon-m-cube')
                        ->numeric()
                        ->nullable(),
                    TextInput::make('no_po')
                        ->label('No PO')
                        ->prefixIcon('heroicon-m-receipt-percent')
                        ->disabled()
                        ->dehydrated(true),
                    TimePicker::make('jam_disiapkan')
                        ->label('Jam Disiapkan')
                        ->prefixIcon('heroicon-m-clock')
                        ->seconds(false)
                        ->step(60)
                        ->extraAttributes(['lang' => 'id-ID'])
                        ->required(),
                    Select::make('status')
                        ->label('Status')
                        ->prefixIcon('heroicon-m-check-badge')
                        ->options([
                            'draft' => 'Draft',
                            'siap kirim' => 'Siap Kirim',
                            'selesai' => 'Selesai',
                        ])
                        ->default('draft')
                        ->required()
                        ->live(),
                    TextInput::make('diserahkan_kepada')
                        ->label('Diserahkan Kepada')
                        ->prefixIcon('heroicon-m-user')
                        ->placeholder('Nama koordinator...'),
                    Select::make('helper')
                        ->label('Helper')
                        ->prefixIcon('heroicon-m-user-group')
                        ->options(WarehouseEmployee::pluck('nama_karyawan', 'id'))
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->placeholder('Pilih helper...'),
                    Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->columnSpanFull()
                        ->placeholder('Kondisi proses...'),
                ]),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::getFormFields());
    }
}
