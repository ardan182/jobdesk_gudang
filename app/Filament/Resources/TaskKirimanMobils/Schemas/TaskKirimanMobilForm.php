<?php

namespace App\Filament\Resources\TaskKirimanMobils\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class TaskKirimanMobilForm
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
            TextInput::make('no_plat_mobil')
                ->label('No Plat Mobil')
                ->required(),
            TimePicker::make('jam_muat')
                ->label('Jam Muat')
                ->required(),
            TimePicker::make('jam_selesai_muat')
                ->label('Jam Selesai Muat')
                ->required(),
            TimePicker::make('jam_berangkat')
                ->label('Jam Berangkat')
                ->required(),
            TextInput::make('nama_supir')
                ->label('Nama Supir')
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
