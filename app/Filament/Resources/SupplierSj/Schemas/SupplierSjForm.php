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
                ->placeholder('Terisi otomatis dari Terima Supplier')
                ->disabled()
                ->dehydrated(true),
            TextInput::make('tanggal_datang')
                ->label('Tanggal Datang')
                ->placeholder('Terisi otomatis')
                ->disabled()
                ->dehydrated(true),
            TextInput::make('nomor_po_referensi')
                ->label('No PO Referensi')
                ->placeholder('Terisi otomatis')
                ->disabled()
                ->dehydrated(true),
            TextInput::make('jumlah_koli')
                ->label('Jumlah Koli')
                ->prefixIcon('heroicon-m-cube')
                ->placeholder('Terisi otomatis')
                ->disabled()
                ->dehydrated(true),
            TextInput::make('jumlah_faktur')
                ->label('Jumlah Faktur / Lembar SJ')
                ->prefixIcon('heroicon-m-document-duplicate')
                ->placeholder('Terisi otomatis')
                ->disabled()
                ->dehydrated(true),
            Select::make('status_input')
                ->label('Status Input')
                ->options([
                    'belum_di_cek' => 'Belum Di Cek',
                    'draft' => 'Draft',
                    'selesai' => 'Selesai',
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
