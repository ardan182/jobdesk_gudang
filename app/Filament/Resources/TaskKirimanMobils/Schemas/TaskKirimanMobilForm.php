<?php

namespace App\Filament\Resources\TaskKirimanMobils\Schemas;

use App\Models\BranchShipment;
use App\Models\MasterKendaraan;
use App\Models\MasterSopir;
use App\Models\MasterToko;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaskKirimanMobilForm
{
    public static function getFormFields(): array
    {
        return [
            Section::make('Informasi Task')
                ->columns(2)
                ->schema([
                    Select::make('cabang')
                        ->label('Nama Cabang')
                        ->options(MasterToko::pluck('nama_toko', 'nama_toko'))
                        ->searchable()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $set) {
                            $set('branch_shipments', []);
                            $set('total_sj_tampil', 0);
                            $totalAll = BranchShipment::where('cabang', $state)
                                ->where('status', 'selesai')->count();
                            $set('sisa_sj_tampil', $totalAll);
                        }),
                    Select::make('branch_shipments')
                        ->label('Pilih SJ')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->live()
                        ->options(function ($get, $record) {
                            $cabang = $get('cabang');
                            if (!$cabang) return [];

                            $query = BranchShipment::where('status', 'selesai')
                                ->where('cabang', $cabang)
                                ->where(function ($q) use ($record) {
                                    $q->whereNotIn('id', function ($q2) {
                                        $q2->select('branch_shipment_id')
                                            ->from('branch_shipment_kiriman_mobil');
                                    });
                                    if ($record && $record->id) {
                                        $attached = $record->branchShipments()
                                            ->pluck('branch_shipments.id')->toArray();
                                        if (!empty($attached)) {
                                            $q->orWhereIn('id', $attached);
                                        }
                                    }
                                });

                            return $query->pluck('nomor_sj', 'id');
                        })
                        ->afterStateHydrated(function ($component, $state, $set, $get) {
                            $cabang = $get('cabang');
                            $selected = count($state ?? []);
                            $totalAll = $cabang
                                ? BranchShipment::where('cabang', $cabang)
                                    ->where('status', 'selesai')->count()
                                : 0;
                            $set('total_sj_tampil', $selected);
                            $set('sisa_sj_tampil', $totalAll - $selected);
                        })
                        ->afterStateUpdated(function ($state, $set, $get) {
                            $cabang = $get('cabang');
                            $selected = count($state ?? []);
                            $totalAll = $cabang
                                ? BranchShipment::where('cabang', $cabang)
                                    ->where('status', 'selesai')->count()
                                : 0;
                            $set('total_sj_tampil', $selected);
                            $set('sisa_sj_tampil', $totalAll - $selected);
                        }),
                    TextInput::make('total_sj_tampil')
                        ->label('Total SJ Dipilih')
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('sisa_sj_tampil')
                        ->label('Sisa SJ Kiriman')
                        ->disabled()
                        ->dehydrated(false),
                    TimePicker::make('jam_muat')
                        ->label('Jam Muat')
                        ->seconds(false)
                        ->step(60)
                        ->extraAttributes(['lang' => 'id-ID']),
                    TimePicker::make('jam_selesai_muat')
                        ->label('Jam Selesai Muat')
                        ->seconds(false)
                        ->step(60)
                        ->extraAttributes(['lang' => 'id-ID']),
                    Select::make('no_plat_mobil')
                        ->label('No Plat Mobil')
                        ->options(MasterKendaraan::pluck('nomor_polisi', 'nomor_polisi'))
                        ->searchable()
                        ->preload(),
                    Select::make('nama_supir')
                        ->label('Sopir')
                        ->options(MasterSopir::pluck('nama_sopir', 'nama_sopir'))
                        ->searchable()
                        ->preload(),
                    TimePicker::make('jam_berangkat')
                        ->label('Jam Berangkat')
                        ->seconds(false)
                        ->step(60)
                        ->extraAttributes(['lang' => 'id-ID'])
                        ->live()
                        ->afterStateUpdated(function ($state, $set, $get) {
                            $tiba = $get('jam_tiba');
                            if ($state && $tiba) {
                                $minutes = Carbon::parse($state)->diffInMinutes(Carbon::parse($tiba));
                                $h = intdiv($minutes, 60);
                                $m = $minutes % 60;
                                $set('durasi_tampil', $h > 0 ? "{$h}j {$m}m" : "{$m}m");
                            } else {
                                $set('durasi_tampil', '-');
                            }
                        }),
                    TimePicker::make('jam_tiba')
                        ->label('Jam Tiba')
                        ->seconds(false)
                        ->step(60)
                        ->extraAttributes(['lang' => 'id-ID'])
                        ->live()
                        ->afterStateUpdated(function ($state, $set, $get) {
                            $berangkat = $get('jam_berangkat');
                            if ($state && $berangkat) {
                                $minutes = Carbon::parse($berangkat)->diffInMinutes(Carbon::parse($state));
                                $h = intdiv($minutes, 60);
                                $m = $minutes % 60;
                                $set('durasi_tampil', $h > 0 ? "{$h}j {$m}m" : "{$m}m");
                            } else {
                                $set('durasi_tampil', '-');
                            }
                        }),
                    TextInput::make('durasi_tampil')
                        ->label('Durasi Kiriman')
                        ->disabled()
                        ->dehydrated(false),
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'dalam pengiriman' => 'Dalam Pengiriman',
                            'datang' => 'Datang',
                        ])
                        ->default('draft')
                        ->required(),
                    Textarea::make('keterangan')
                        ->label('Keterangan')
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
