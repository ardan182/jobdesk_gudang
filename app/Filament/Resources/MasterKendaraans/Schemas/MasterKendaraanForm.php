<?php

namespace App\Filament\Resources\MasterKendaraans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MasterKendaraanForm
{
    public static function getFormFields(): array
    {
        return [
            TextInput::make('nomor_polisi')
                ->label('Nomor Polisi')
                ->required()
                ->unique(ignoreRecord: true),
            Select::make('jenis_kendaraan')
                ->label('Jenis Kendaraan')
                ->options([
                    'mobil' => 'Mobil',
                    'motor' => 'Motor',
                ])
                ->required(),
            TextInput::make('merek_dan_model')
                ->label('Merek dan Model'),
            TextInput::make('nomor_rangka')
                ->label('Nomor Rangka'),
            TextInput::make('nomor_mesin')
                ->label('Nomor Mesin'),
            TextInput::make('no_stnk')
                ->label('No STNK'),
            TextInput::make('no_kir')
                ->label('No KIR'),
            DatePicker::make('masa_berlaku_stnk')
                ->label('Masa Berlaku STNK'),
            DatePicker::make('masa_berlaku_kir')
                ->label('Masa Berlaku KIR'),
            Textarea::make('keterangan')
                ->label('Keterangan')
                ->columnSpanFull(),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components(self::getFormFields());
    }
}
