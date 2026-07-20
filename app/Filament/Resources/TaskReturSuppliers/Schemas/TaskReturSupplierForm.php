<?php

namespace App\Filament\Resources\TaskReturSuppliers\Schemas;

use App\Models\ArrivalSupplierTruck;
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
            Select::make('arrival_supplier_truck_id')
                ->label('Pilih Mobil Datang')
                ->options(function () {
                    return ArrivalSupplierTruck::where(function ($query) {
                        $query->whereIn('status', ['MENGANTRI', 'PROSES'])
                            ->whereIn('jenis_kiriman', ['RETUR', 'DATANG & RETUR']);
                    })
                    ->orWhere(function ($query) {
                        $query->where('status', 'SELESAI')
                            ->where('jenis_kiriman', 'DATANG & RETUR');
                    })
                    ->get()
                    ->pluck('no_plat_mobil', 'id');
                })
                ->searchable()
                ->placeholder('Pilih mobil datang...')
                ->reactive()
                ->afterStateUpdated(function ($state, $set, $get) {
                    if (!$state) return;
                    $truck = ArrivalSupplierTruck::with('supplier')->find($state);
                    if (!$truck) return;

                    $set('nama_supplier_ekspedisi', $truck->supplier?->nama_supplier ?? '');
                    $set('no_plat_mobil', $truck->no_plat_mobil);
                    $set('nama_sopir', $truck->nama_sopir ?? '');
                }),
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
                ->seconds(false)
                ->step(60)
                ->extraAttributes(['lang' => 'id-ID'])
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
