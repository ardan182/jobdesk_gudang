<?php

namespace App\Filament\Resources\BranchShipment\Schemas;

use App\Models\MasterToko;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BranchShipmentForm
{
    public static function getFormFields(): array
    {
        return [
            Section::make('Informasi Kirim Barang')
                ->description('Isi data pengiriman barang ke cabang.')
                ->icon('heroicon-o-paper-airplane')
                ->columns(2)
                ->schema([
                    Select::make('pilih_kiriman')
                        ->label('Pilih Kiriman')
                        ->prefixIcon('heroicon-m-clipboard-document-list')
                        ->helperText('Pilih sumber barang')
                        ->options([
                            'pembagian_po' => 'Pembagian dari PO',
                            'stock_gudang' => 'Stock Gudang',
                            'rb_pesanan' => 'RB / Pesanan',
                        ])
                        ->required(),
                    Select::make('cabang')
                        ->label('Cabang')
                        ->prefixIcon('heroicon-m-building-storefront')
                        ->options(MasterToko::pluck('nama_toko', 'nama_toko'))
                        ->searchable()
                        ->disabled(fn ($component) => $component->getRecord() !== null)
                        ->required(),
                    TextInput::make('nomor_sj')
                        ->label('Nomor SJ')
                        ->prefixIcon('heroicon-m-document-text')
                        ->placeholder('Masukkan nomor surat jalan')
                        ->helperText('Wajib jika status "Selesai"')
                        ->requiredIf('status', 'selesai')
                        ->maxLength(100),
                    TextInput::make('total_qty')
                        ->label('Total Qty')
                        ->prefixIcon('heroicon-m-cube')
                        ->placeholder('0')
                        ->helperText('Total barang/koli dikirim')
                        ->numeric()
                        ->minValue(1)
                        ->required(),
                    TextInput::make('no_po')
                        ->label('No PO')
                        ->prefixIcon('heroicon-m-receipt-percent')
                        ->placeholder('Kosongkan jika dari stock')
                        ->helperText('Nomor PO pembelian (opsional)')
                        ->maxLength(100),
                    DatePicker::make('tanggal_buat')
                        ->label('Tanggal Buat')
                        ->prefixIcon('heroicon-m-calendar')
                        ->default(now())
                        ->required(),
                    Select::make('status')
                        ->label('Status')
                        ->live()
                        ->options([
                            'draft' => 'Draft',
                            'selesai' => 'Selesai',
                        ])
                        ->default('draft')
                        ->required(),
                    Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->placeholder('Catatan tambahan...')
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
