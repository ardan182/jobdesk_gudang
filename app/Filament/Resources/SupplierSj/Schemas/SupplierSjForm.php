<?php

namespace App\Filament\Resources\SupplierSj\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupplierSjForm
{
    public static function getFormFields(): array
    {
        return [
            TextInput::make('nama_supplier')
                ->label('Nama Supplier')
                ->placeholder('Masukkan nama supplier')
                ->nullable(),
            DatePicker::make('tanggal_datang')
                ->label('Tanggal Datang')
                ->nullable()
                ->displayFormat('d/m/Y'),
            TextInput::make('nomor_po_referensi')
                ->label('Nomor PO Referensi')
                ->placeholder('Contoh: PO-2026-0001')
                ->nullable(),
            Select::make('status_input')
                ->label('Status Input')
                ->options([
                    'kosong' => 'Kosong',
                    'sudah' => 'Sudah',
                ])
                ->nullable()
                ->placeholder('Pilih Status'),
            DatePicker::make('tanggal_input')
                ->label('Tanggal Input')
                ->nullable()
                ->default(now())
                ->displayFormat('d/m/Y'),
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
