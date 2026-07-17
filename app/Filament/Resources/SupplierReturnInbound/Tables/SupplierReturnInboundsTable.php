<?php

namespace App\Filament\Resources\SupplierReturnInbound\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SupplierReturnInboundsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(50)
            ->defaultSort('created_at', 'desc')
            ->recordAction('view')
            ->columns([
                TextColumn::make('no_nota_retur')
                    ->label('No Nota Retur')
                    ->searchable()
                    ->grow(false),
                TextColumn::make('nama_supplier')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('jumlah_kolian')
                    ->label('Kolian')
                    ->numeric()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('tanggal_datang')
                    ->label('Tgl Datang')
                    ->date('d/m/Y')
                    ->sortable()
                    ->grow(false),
                TextColumn::make('nama_supir')
                    ->label('Supir')
                    ->grow(false),
                TextColumn::make('no_plat_mobil')
                    ->label('No Plat')
                    ->grow(false),
                TextColumn::make('nama_ekspedisi')
                    ->label('Ekspedisi')
                    ->toggleable(isToggledHiddenByDefault: true)
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
                    ->modalHeading('Detail Retur Masuk Supplier')
                    ->modalWidth('lg')
                    ->infolist([
                        TextEntry::make('no_nota_retur')->label('No Nota Retur'),
                        TextEntry::make('nama_supplier')->label('Nama Supplier'),
                        TextEntry::make('nama_ekspedisi')->label('Ekspedisi'),
                        TextEntry::make('nama_supir')->label('Supir'),
                        TextEntry::make('no_plat_mobil')->label('No Plat'),
                        TextEntry::make('tanggal_datang')->label('Tgl Datang')->date('d/m/Y'),
                        TextEntry::make('jam_kedatangan')->label('Jam Kedatangan'),
                        TextEntry::make('jumlah_kolian')->label('Jumlah Kolian'),
                        TextEntry::make('keterangan')->label('Keterangan'),
                    ]),
                EditAction::make()
                    ->color('warning')
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->modalHeading('Edit Retur Masuk Supplier')
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
