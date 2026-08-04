<?php

namespace App\Filament\Resources\KomplainPo\Schemas;

use App\Models\MasterToko;
use App\Models\Supplier;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KomplainPoForm
{
    public static function getFormFields(): array
    {
        return [
            Section::make('PO Supplier')
                ->description('Data supplier dan nomor dokumen PO')
                ->columns(3)
                ->schema([
                    Select::make('cabang')
                        ->label('Pilih Cabang')
                        ->prefixIcon('heroicon-m-building-storefront')
                        ->options(MasterToko::pluck('nama_toko', 'nama_toko'))
                        ->searchable()
                        ->required(),
                    Select::make('supplier_id')
                        ->label('Pilih Supplier')
                        ->prefixIcon('heroicon-m-building-office')
                        ->options(Supplier::pluck('nama_supplier', 'id'))
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('no_po')
                        ->label('No PO')
                        ->prefixIcon('heroicon-m-document-text'),
                ]),
            Section::make('Barang')
                ->description('Detail barang yang dikomplain')
                ->columns(2)
                ->schema([
                    TextInput::make('barcode')
                        ->label('Barcode / CodeItem')
                        ->prefixIcon('heroicon-m-qr-code'),
                    TextInput::make('nama_barang')
                        ->label('Nama Barang')
                        ->prefixIcon('heroicon-m-cube'),
                    TextInput::make('qty_diterima')
                        ->label('Qty Diterima')
                        ->prefixIcon('heroicon-m-hashtag')
                        ->numeric(),
                    TextInput::make('no_surat_jalan')
                        ->label('Nomor Surat Jalan')
                        ->prefixIcon('heroicon-m-document-arrow-down'),
                    TextInput::make('qty_disurat_jalan')
                        ->label('Qty di Surat Jalan')
                        ->prefixIcon('heroicon-m-hashtag')
                        ->numeric(),
                    FileUpload::make('foto')
                        ->label('Foto Barang')
                        ->multiple()
                        ->minFiles(1)
                        ->maxFiles(5)
                        ->image()
                        ->disk('public')
                        ->directory('fotos-komplain')
                        ->required()
                        ->columnSpanFull(),
                ]),
            Section::make('Status')
                ->description('Kondisi, penyelesaian, dan status')
                ->columns(2)
                ->schema([
                    Select::make('kondisi_barang')
                        ->label('Kondisi Barang')
                        ->prefixIcon('heroicon-m-clipboard-document-list')
                        ->options([
                            'tidak_sesuai' => 'Tidak Sesuai',
                            'tidak_lengkap' => 'Tidak Lengkap',
                        ])
                        ->placeholder('Pilih kondisi...'),
                    Select::make('penyelesaian')
                        ->label('Penyelesaian')
                        ->prefixIcon('heroicon-m-check-circle')
                        ->options([
                            'potong_nota' => 'Potong Nota',
                            'retur' => 'Retur ke Gudang',
                            'ganti_barang' => 'Ganti Barang',
                        ])
                        ->placeholder('Pilih penyelesaian...'),
                    Select::make('status')
                        ->label('Status')
                        ->prefixIcon('heroicon-m-check-badge')
                        ->options([
                            'draft' => 'Draft',
                            'selesai' => 'Selesai',
                        ])
                        ->default('draft')
                        ->required()
                        ->live()
                        ->disabled(fn ($get) => blank($get('tanggal_datang_barang'))),
                    DatePicker::make('tanggal_datang_barang')
                        ->label('Tanggal Datang Barang')
                        ->prefixIcon('heroicon-m-calendar-days')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->live(),
                    Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->columnSpanFull()
                        ->rows(3),
                ]),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::getFormFields());
    }
}
