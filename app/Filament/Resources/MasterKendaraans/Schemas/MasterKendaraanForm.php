<?php

namespace App\Filament\Resources\MasterKendaraans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MasterKendaraanForm
{
    public static function getFormFields(): array
    {
        return [
            Section::make('Informasi Kendaraan')
                ->columns(2)
                ->schema([
                    TextInput::make('nomor_polisi')
                        ->label('Nomor Polisi')
                        ->prefixIcon('heroicon-m-truck')
                        ->required()
                        ->unique(ignoreRecord: true),
                    Select::make('jenis_kendaraan')
                        ->label('Jenis Kendaraan')
                        ->prefixIcon('heroicon-m-tag')
                        ->options([
                            'mobil' => 'Mobil',
                            'motor' => 'Motor',
                        ])
                        ->required()
                        ->live(),
                    TextInput::make('merek_dan_model')
                        ->label('Merek dan Model')
                        ->prefixIcon('heroicon-m-tag'),
                    TextInput::make('nomor_rangka')
                        ->label('Nomor Rangka')
                        ->prefixIcon('heroicon-m-qr-code'),
                    TextInput::make('nomor_mesin')
                        ->label('Nomor Mesin')
                        ->prefixIcon('heroicon-m-cog-8-tooth'),
                    TextInput::make('no_stnk')
                        ->label('No STNK')
                        ->prefixIcon('heroicon-m-document-text'),
                    TextInput::make('no_kir')
                        ->label('No KIR')
                        ->prefixIcon('heroicon-m-document-text')
                        ->visible(fn ($get) => $get('jenis_kendaraan') !== 'motor'),
                    DatePicker::make('masa_berlaku_stnk')
                        ->label('STNK 1 Tahun')
                        ->prefixIcon('heroicon-m-calendar-days'),
                    DatePicker::make('masa_berlaku_kir')
                        ->label('Masa Berlaku KIR')
                        ->prefixIcon('heroicon-m-calendar-days')
                        ->visible(fn ($get) => $get('jenis_kendaraan') !== 'motor'),
                    DatePicker::make('stnk_5_tahun_sampai')
                        ->label('STNK 5 Tahun Sampai')
                        ->prefixIcon('heroicon-m-calendar-days')
                        ->hint('Isi sekali sebagai acuan 5 tahun'),
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
