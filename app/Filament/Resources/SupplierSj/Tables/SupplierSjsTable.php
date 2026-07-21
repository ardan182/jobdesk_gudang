<?php

namespace App\Filament\Resources\SupplierSj\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SupplierSjsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(50)
            ->defaultSort('created_at', 'desc')
            ->recordAction('view')
            ->columns([
                TextColumn::make('id_task')
                    ->label('ID Task')
                    ->searchable()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('nama_supplier')
                    ->label('Nama Supplier')
                    ->searchable()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('tanggal_datang')
                    ->label('Tgl Datang')
                    ->date('d/m/Y')
                    ->sortable()
                    ->grow(false),
                TextColumn::make('nomor_po_referensi')
                    ->label('No PO')
                    ->searchable()
                    ->grow(false),
                TextColumn::make('jumlah_koli')
                    ->label('Koli')
                    ->numeric()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('jumlah_faktur')
                    ->label('Faktur')
                    ->numeric()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('status_input')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'belum_di_cek' => 'gray',
                        'draft' => 'warning',
                        'selesai' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'belum_di_cek' => 'Belum Di Cek',
                        'draft' => 'Draft',
                        'selesai' => 'Selesai',
                        default => $state,
                    })
                    ->grow(false),
                TextColumn::make('tanggal_input')
                    ->label('Tgl Input')
                    ->date('d/m/Y')
                    ->sortable()
                    ->grow(false),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->grow(false),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make()
                    ->color('info')
                    ->iconButton()
                    ->tooltip('Lihat Detail')
                    ->modalHeading('Detail Input SJ')
                    ->modalWidth('lg')
                    ->infolist([
                        TextEntry::make('id_task')->label('ID Task'),
                        TextEntry::make('nama_supplier')->label('Nama Supplier'),
                        TextEntry::make('tanggal_datang')->label('Tgl Datang')->date('d/m/Y'),
                        TextEntry::make('nomor_po_referensi')->label('No PO Referensi'),
                        TextEntry::make('jumlah_koli')->label('Jumlah Koli'),
                        TextEntry::make('jumlah_faktur')->label('Jumlah Faktur'),
                        TextEntry::make('status_input')->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'belum_di_cek' => 'gray',
                                'draft' => 'warning',
                                'selesai' => 'success',
                                default => 'gray',
                            }),
                        TextEntry::make('tanggal_input')->label('Tgl Input')->date('d/m/Y'),
                        TextEntry::make('keterangan')->label('Keterangan'),
                    ]),
                EditAction::make()
                    ->color('warning')
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->modalHeading('Edit Input SJ')
                    ->modalWidth('lg'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->color('danger'),
                ]),
            ]);
    }
}
