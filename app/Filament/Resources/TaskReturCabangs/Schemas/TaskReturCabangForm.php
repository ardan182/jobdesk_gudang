<?php

namespace App\Filament\Resources\TaskReturCabangs\Schemas;

use App\Models\MasterSopir;
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
            Select::make('cabang')
                ->label('Cabang')
                ->options([
                    'pusat' => 'Pusat',
                    'ujungberung' => 'Ujungberung',
                    'soreang' => 'Soreang',
                    'majalaya' => 'Majalaya',
                    'cicaheum' => 'Cicaheum',
                    'barokah' => 'Barokah',
                ])
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
                ->seconds(false)
                ->step(60)
                ->extraAttributes(['lang' => 'id-ID'])
                ->required(),
            Select::make('nama_sopir')
                ->label('Nama Sopir')
                ->options(MasterSopir::pluck('nama_sopir', 'nama_sopir'))
                ->searchable()
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
