<?php

namespace App\Filament\Resources\SupplierSj\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SupplierSjForm
{
    public static function getFormFields(): array
    {
        return [
            Section::make('Informasi Dokumen')
                ->description('Data SJ dari supplier — referensi dari Terima Supplier.')
                ->icon('heroicon-o-document-text')
                ->columns(2)
                ->schema([
                    TextInput::make('id_task')
                        ->label('ID Task')
                        ->disabled()
                        ->dehydrated(true),
                    TextInput::make('nama_supplier')
                        ->label('Nama Supplier')
                        ->disabled()
                        ->dehydrated(true),
                    DatePicker::make('tanggal_datang')
                        ->label('Tanggal Datang')
                        ->displayFormat('d/m/Y')
                        ->disabled()
                        ->dehydrated(true),
                    TextInput::make('nomor_po_referensi')
                        ->label('No PO Referensi')
                        ->prefixIcon('heroicon-m-document-text')
                        ->disabled()
                        ->dehydrated(true),
                    TextInput::make('jumlah_koli')
                        ->label('Jumlah Koli')
                        ->prefixIcon('heroicon-m-cube')
                        ->disabled()
                        ->dehydrated(true),
                    TextInput::make('jumlah_faktur')
                        ->label('Jumlah Faktur / Lembar SJ')
                        ->prefixIcon('heroicon-m-document-duplicate')
                        ->disabled()
                        ->dehydrated(true),
                    TextInput::make('tempo_tampil')
                        ->label('Tempo')
                        ->prefixIcon('heroicon-m-clock')
                        ->disabled()
                        ->dehydrated(false)
                        ->afterStateHydrated(function ($component, $state) {
                            $record = $component->getRecord();
                            if ($record && $record->tanggal_datang) {
                                $days = abs(now()->startOfDay()->diffInDays($record->tanggal_datang));
                                $prefix = in_array($record->status_input, ['belum_di_cek', 'draft']) ? 'blm input' : 'input';
                                $component->state("{$prefix} {$days} hr");
                            }
                        }),
                    TextInput::make('terima_ref_tampil')
                        ->label('Ref Terima Supplier')
                        ->prefixIcon('heroicon-m-arrow-right-circle')
                        ->disabled()
                        ->dehydrated(false)
                        ->afterStateHydrated(function ($component, $state) {
                            $record = $component->getRecord();
                            if ($record && $record->keterangan) {
                                preg_match('/\bTRM-SUP-\d+\b/', $record->keterangan, $m);
                                $component->state($m[0] ?? '-');
                            }
                        }),
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
                        ->maxDate(now())
                        ->displayFormat('d/m/Y'),
                    Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->nullable()
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
