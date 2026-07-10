<?php

namespace App\Filament\Resources\TaskKeluarBarangs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class TaskKeluarBarangForm
{
    public static function getFormFields(): array
    {
        return [
            Select::make('toko_tujuan')
                ->label('Toko Tujuan')
                ->options([
                    'pusat' => 'Pusat',
                    'ujungberung' => 'Ujungberung',
                    'soreang' => 'Soreang',
                    'majalaya' => 'Majalaya',
                    'cicaheum' => 'Cicaheum',
                    'barokah' => 'Barokah',
                ])
                ->required(),
            TextInput::make('supplier')
                ->label('Supplier')
                ->required(),
            TextInput::make('no_referensi_sj')
                ->label('No Referensi SJ')
                ->required(),
            TextInput::make('jumlah_kolian')
                ->label('Jumlah Kolian')
                ->required()
                ->numeric(),
            TimePicker::make('jam_naik')
                ->label('Jam Naik')
                ->required(),
            TextInput::make('nama_koordinator')
                ->label('Nama Koordinator')
                ->required(),
            Select::make('status')
                ->label('Status')
                ->options([
                    'komplit' => 'Komplit',
                    'kurang' => 'Kurang',
                    'lebih' => 'Lebih',
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
