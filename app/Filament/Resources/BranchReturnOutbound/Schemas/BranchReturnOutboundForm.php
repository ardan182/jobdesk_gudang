<?php

namespace App\Filament\Resources\BranchReturnOutbound\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class BranchReturnOutboundForm
{
    public static function getFormFields(): array
    {
        return [
            TextInput::make('toko_tujuan')
                ->label('Toko Tujuan')
                ->placeholder('Nama cabang tujuan')
                ->nullable(),
            TextInput::make('nomor_sj')
                ->label('Nomor SJ')
                ->placeholder('Masukkan nomor surat jalan')
                ->nullable(),
            TextInput::make('total_qty')
                ->label('Total Qty')
                ->numeric()
                ->placeholder('0')
                ->nullable(),
            TextInput::make('disiapkan_oleh')
                ->label('Disiapkan Oleh')
                ->placeholder('Nama staff gudang')
                ->nullable(),
            TimePicker::make('jam_naik')
                ->label('Jam Naik')
                ->seconds(false)
                ->nullable(),
            TextInput::make('diserahkan_kepada')
                ->label('Diserahkan Kepada')
                ->placeholder('Nama supir/kurir')
                ->nullable(),
            TextInput::make('status')
                ->label('Status')
                ->placeholder('Contoh: Pending, Proses, Terkirim')
                ->nullable(),
            Textarea::make('keterangan')
                ->label('Keterangan')
                ->nullable()
                ->rows(3),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components(self::getFormFields());
    }
}
