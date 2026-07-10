<?php

namespace App\Filament\Resources\TaskReturCabangs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class TaskReturCabangForm
{
    public static function getFormFields(): array
    {
        return [
            TextInput::make('cabang')
                ->label('Cabang')
                ->required(),
            Select::make('jenis_retur')
                ->label('Jenis Retur')
                ->options([
                    'retur_jelek' => 'Retur Jelek',
                    'retur_bagus' => 'Retur Bagus',
                ])
                ->required(),
            TextInput::make('no_sj_retur')
                ->label('No SJ Retur Indri/ERP')
                ->required(),
            TextInput::make('total_kolian')
                ->label('Total Kolian')
                ->required()
                ->numeric(),
            TimePicker::make('jam_bongkar')
                ->label('Jam Bongkar')
                ->required(),
            TextInput::make('nama_sopir')
                ->label('Nama Sopir')
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
