<?php

namespace App\Filament\Resources\KendaraanDokumen\Schemas;

use App\Models\MasterKendaraan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KendaraanDokumenForm
{
    public static function getFormFields(): array
    {
        return [
            Section::make('Informasi Dokumen')
                ->description('Data referensi dari sistem')
                ->columns(2)
                ->schema([
                    Select::make('master_kendaraan_id')
                        ->label('Kendaraan')
                        ->prefixIcon('heroicon-m-truck')
                        ->options(MasterKendaraan::all()->mapWithKeys(fn ($k) => [
                            $k->id => "{$k->nomor_polisi} — {$k->merek_dan_model}",
                        ]))
                        ->searchable()
                        ->disabled()
                        ->required(),
                    Select::make('jenis')
                        ->label('Jenis Dokumen')
                        ->prefixIcon('heroicon-m-document-text')
                        ->options([
                            'stnk' => 'STNK',
                            'kir' => 'KIR',
                        ])
                        ->disabled()
                        ->required(),
                    TextInput::make('periode_label')
                        ->label('Periode')
                        ->prefixIcon('heroicon-m-clock')
                        ->disabled()
                        ->dehydrated(false)
                        ->afterStateHydrated(function ($component, $state) {
                            $record = $component->getRecord();
                            $component->state($record?->periode_label ?? '-');
                        }),
                    DatePicker::make('masa_berlaku_lama')
                        ->label('Masa Berlaku Terakhir')
                        ->prefixIcon('heroicon-m-calendar-days')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->disabled()
                        ->dehydrated(false)
                        ->default(now()->format('Y-m-d'))
                        ->afterStateHydrated(function ($component, $state, $get) {
                            $record = $component->getRecord();
                            $component->state($record?->masa_berlaku?->format('Y-m-d') ?? now()->format('Y-m-d'));
                        }),
                ]),
            Section::make('Update Dokumen')
                ->description('Input data perpanjangan terbaru')
                ->columns(2)
                ->schema([
                    TextInput::make('nomor_dokumen')
                        ->label('No Dokumen')
                        ->prefixIcon('heroicon-m-document')
                        ->required(),
                    DatePicker::make('masa_berlaku')
                        ->label('Masa Berlaku Baru')
                        ->prefixIcon('heroicon-m-calendar-days')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->live(),
                    TextInput::make('user_perpanjang')
                        ->label('User Perpanjang')
                        ->prefixIcon('heroicon-m-user')
                        ->placeholder('Nama yang memperpanjang...')
                        ->required(),
                    Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->columnSpanFull()
                        ->rows(2),
                ]),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::getFormFields());
    }
}
