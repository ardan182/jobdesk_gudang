<?php

namespace App\Filament\Resources\WarehouseDocument\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WarehouseDocumentForm
{
    public static function getFormFields(): array
    {
        return [
            Section::make('Upload Dokumen')
                ->description('Unggah file template, SOP, atau formulir lapangan.')
                ->icon('heroicon-o-document-arrow-down')
                ->columns(2)
                ->schema([
                    TextInput::make('nama_dokumen')
                        ->label('Nama Dokumen')
                        ->prefixIcon('heroicon-m-document-text')
                        ->placeholder('Contoh: Formulir Retur Barang')
                        ->required(),
                    Select::make('kategori')
                        ->label('Kategori')
                        ->prefixIcon('heroicon-m-folder')
                        ->options([
                            'Formulir Lapangan' => 'Formulir Lapangan',
                            'SOP Gudang' => 'SOP Gudang',
                            'Template Import' => 'Template Import',
                        ])
                        ->placeholder('Pilih kategori...')
                        ->required(),
                    TextInput::make('versi')
                        ->label('Versi')
                        ->prefixIcon('heroicon-m-hashtag')
                        ->default('v1.0')
                        ->placeholder('v1.0')
                        ->required(),
                    TextInput::make('format_file')
                        ->label('Format File')
                        ->prefixIcon('heroicon-m-document')
                        ->disabled()
                        ->dehydrated(true),
                    FileUpload::make('file_path')
                        ->label('File Template / Dokumen')
                        ->disk('local')
                        ->directory('document_templates')
                        ->storeFiles()
                        ->acceptedFileTypes([
                            'application/pdf',
                            'text/csv',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.oasis.opendocument.spreadsheet',
                            'application/vnd.ms-powerpoint',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            'text/plain',
                            'image/jpeg',
                            'image/png',
                        ])
                        ->maxSize(10240)
                        ->required()
                        ->columnSpanFull(),
                    Textarea::make('deskripsi')
                        ->label('Deskripsi')
                        ->placeholder('Catatan tentang dokumen ini...')
                        ->rows(3)
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
