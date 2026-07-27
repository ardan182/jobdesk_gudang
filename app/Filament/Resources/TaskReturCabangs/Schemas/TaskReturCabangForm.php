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
            Section::make('Informasi Kiriman')
                ->description('Data kiriman mobil yang sudah selesai dengan retur')
                ->columns(3)
                ->schema([
                    Select::make('kiriman_mobil_id')
                        ->label('Kiriman Mobil')
                        ->prefixIcon('heroicon-m-truck')
                        ->searchable()
                        ->disabled(fn ($component) => $component->getRecord() !== null)
                        ->required()
                        ->live()
                        ->columnSpanFull()
                        ->options(function () {
                            return TaskKirimanMobil::where('status', 'selesai')
                                ->whereIn('retur_option', ['ada_retur'])
                                ->get()
                                ->mapWithKeys(fn ($k) => [
                                    $k->id => "{$k->cabang} - {$k->no_plat_mobil} - " . ($k->jam_tiba?->format('H:i') ?? '-'),
                                ]);
                        })
                        ->afterStateHydrated(function ($component, $state, $set) {
                            $record = $component->getRecord();
                            if ($record && $record->cabang && $record->no_plat_mobil) {
                                $kirim = TaskKirimanMobil::where('cabang', $record->cabang)
                                    ->where('no_plat_mobil', $record->no_plat_mobil)
                                    ->first();
                                if ($kirim) {
                                    $component->state($kirim->id);
                                    $set('cabang', $kirim->cabang);
                                    $set('no_plat_mobil', $kirim->no_plat_mobil);
                                    $set('jam_tiba', $kirim->jam_tiba?->format('H:i'));
                                }
                            }
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
                    TextInput::make('cabang')
                        ->label('Toko')
                        ->prefixIcon('heroicon-m-building-storefront')
                        ->disabled()
                        ->dehydrated(),
                    TextInput::make('no_plat_mobil')
                        ->label('No Plat Mobil')
                        ->prefixIcon('heroicon-m-truck')
                        ->disabled()
                        ->dehydrated(),
                    TimePicker::make('jam_tiba')
                        ->label('Jam Tiba')
                        ->prefixIcon('heroicon-m-clock')
                        ->disabled()
                        ->dehydrated()
                        ->seconds(false)
                        ->step(60),
                ]),
            Section::make('Data Retur')
                ->description('Jenis retur, jumlah SJ, dan catatan per jenis')
                ->columns(3)
                ->schema([
                    Select::make('jenis_retur')
                        ->label('Jenis Retur')
                        ->prefixIcon('heroicon-m-arrows-right-left')
                        ->options([
                            'retur_bagus' => 'Retur Bagus',
                            'retur_jelek' => 'Retur Jelek',
                            'rb_dan_rj' => 'RB dan RJ',
                        ])
                        ->live()
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
                    Select::make('nama_sopir')
                        ->label('Nama Sopir')
                        ->prefixIcon('heroicon-m-user')
                        ->options(MasterSopir::pluck('nama_sopir', 'nama_sopir'))
                        ->searchable()
                        ->required(),
                    Select::make('helpers')
                        ->label('Helper')
                        ->prefixIcon('heroicon-m-users')
                        ->multiple()
                        ->columnSpan(2)
                        ->options(WarehouseEmployee::pluck('nama_karyawan', 'id'))
                        ->searchable()
                        ->preload()
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record && $record->helpers->count() > 0) {
                                $component->state($record->helpers->pluck('id')->toArray());
                            }
                        }),
                    TextInput::make('jumlah_sj_bagus')
                        ->label('Jumlah SJ Retur Bagus')
                        ->prefixIcon('heroicon-m-document-text')
                        ->numeric()
                        ->required()
                        ->visible(fn ($get) => in_array($get('jenis_retur'), ['retur_bagus', 'rb_dan_rj'])),
                    Textarea::make('catatan_bagus')
                        ->label('Catatan Retur Bagus')
                        ->required()
                        ->rows(2)
                        ->columnSpan(2)
                        ->visible(fn ($get) => in_array($get('jenis_retur'), ['retur_bagus', 'rb_dan_rj'])),
                    TextInput::make('jumlah_sj_jelek')
                        ->label('Jumlah SJ Retur Jelek')
                        ->prefixIcon('heroicon-m-document-text')
                        ->numeric()
                        ->required()
                        ->visible(fn ($get) => in_array($get('jenis_retur'), ['retur_jelek', 'rb_dan_rj'])),
                    Textarea::make('catatan_jelek')
                        ->label('Catatan Retur Jelek')
                        ->required()
                        ->rows(2)
                        ->columnSpan(2)
                        ->visible(fn ($get) => in_array($get('jenis_retur'), ['retur_jelek', 'rb_dan_rj'])),
                ]),
            Section::make('Status')
                ->description('Status proses dan catatan global')
                ->columns(3)
                ->schema([
                    Select::make('status')
                        ->label('Status')
                        ->prefixIcon('heroicon-m-check-badge')
                        ->options([
                            'draft' => 'Draft',
                            'selesai' => 'Selesai',
                        ])
                        ->default('draft')
                        ->required(),
                    Textarea::make('keterangan')
                        ->label('Keterangan Global')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull()
                        ->placeholder('Catatan tambahan untuk semua jenis retur...'),
                ]),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::getFormFields());
    }
}
