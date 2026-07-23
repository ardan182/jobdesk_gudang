<?php

namespace App\Filament\Resources\TaskReturCabangs\Schemas;

use App\Models\MasterSopir;
use App\Models\TaskKirimanMobil;
use App\Models\WarehouseEmployee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaskReturCabangForm
{
    public static function getFormFields(): array
    {
        return [
            Section::make('Informasi Task')
                ->columns(2)
                ->schema([
                    Select::make('kiriman_mobil_id')
                        ->label('Kiriman Mobil')
                        ->prefixIcon('heroicon-m-truck')
                        ->searchable()
                        ->required()
                        ->live()
                        ->options(function () {
                            return TaskKirimanMobil::where('status', 'selesai')
                                ->whereIn('retur_option', ['ada_rb', 'rb_dan_rj'])
                                ->get()
                                ->mapWithKeys(fn ($k) => [
                                    $k->id => "{$k->cabang} - {$k->no_plat_mobil} - " . ($k->jam_tiba?->format('H:i') ?? '-'),
                                ]);
                        })
                        ->afterStateUpdated(function ($state, $set) {
                            $kirim = TaskKirimanMobil::find($state);
                            if ($kirim) {
                                $set('cabang', $kirim->cabang);
                                $set('no_plat_mobil', $kirim->no_plat_mobil);
                                $set('jam_tiba', $kirim->jam_tiba?->format('H:i'));
                            } else {
                                $set('cabang', null);
                                $set('no_plat_mobil', null);
                                $set('jam_tiba', null);
                            }
                        }),

                    Select::make('cabang')
                        ->label('Cabang')
                        ->prefixIcon('heroicon-m-building-storefront')
                        ->disabled()
                        ->dehydrated()
                        ->options(fn () => TaskKirimanMobil::whereIn('retur_option', ['ada_rb', 'rb_dan_rj'])
                            ->whereNotNull('cabang')
                            ->pluck('cabang', 'cabang')->unique()),

                    Select::make('no_plat_mobil')
                        ->label('No Plat Mobil')
                        ->prefixIcon('heroicon-m-truck')
                        ->disabled()
                        ->dehydrated()
                        ->options(fn () => TaskKirimanMobil::whereIn('retur_option', ['ada_rb', 'rb_dan_rj'])
                            ->whereNotNull('no_plat_mobil')
                            ->pluck('no_plat_mobil', 'no_plat_mobil')->unique()),

                    TimePicker::make('jam_tiba')
                        ->label('Jam Tiba')
                        ->prefixIcon('heroicon-m-clock')
                        ->disabled()
                        ->dehydrated()
                        ->seconds(false)
                        ->step(60),

                    Select::make('jenis_retur')
                        ->label('Jenis Retur')
                        ->prefixIcon('heroicon-m-arrows-right-left')
                        ->options([
                            'retur_bagus' => 'Retur Bagus',
                            'retur_jelek' => 'Retur Jelek',
                        ])
                        ->required(),

                    DatePicker::make('tanggal_bongkar')
                        ->label('Tanggal Bongkar')
                        ->prefixIcon('heroicon-m-calendar')
                        ->native(false)
                        ->required(),

                    TimePicker::make('jam_bongkar')
                        ->label('Jam Bongkar')
                        ->prefixIcon('heroicon-m-clock')
                        ->seconds(false)
                        ->step(60)
                        ->required(),

                    TextInput::make('no_sj_retur')
                        ->label('No SJ Retur')
                        ->prefixIcon('heroicon-m-document-text')
                        ->required(),

                    TextInput::make('total_qty')
                        ->label('Total Qty')
                        ->prefixIcon('heroicon-m-cube')
                        ->numeric()
                        ->required(),

                    Select::make('helpers')
                        ->label('Helper')
                        ->prefixIcon('heroicon-m-users')
                        ->multiple()
                        ->options(WarehouseEmployee::pluck('nama_karyawan', 'id'))
                        ->searchable(),

                    Select::make('status')
                        ->label('Status')
                        ->prefixIcon('heroicon-m-check-badge')
                        ->options([
                            'draft' => 'Draft',
                            'selesai' => 'Selesai',
                        ])
                        ->default('draft')
                        ->required(),

                    Select::make('nama_sopir')
                        ->label('Nama Sopir')
                        ->prefixIcon('heroicon-m-user')
                        ->options(MasterSopir::pluck('nama_sopir', 'nama_sopir'))
                        ->searchable()
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
