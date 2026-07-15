<?php

namespace App\Filament\Resources\TaskDatangMobilSuppliers\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TaskDatangMobilSuppliersTable
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
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('supplier.nama_supplier')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->width('160px')
                    ->grow(false),
                TextColumn::make('nama_sopir')
                    ->label('Sopir')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('tanggal_datang')
                    ->label('Tgl Datang')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('no_plat_mobil')
                    ->label('No Plat')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jam_datang')
                    ->label('Jam Datang')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jam_selesai')
                    ->label('Jam Selesai')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('expedition.nama_ekspedisi')
                    ->label('Ekspedisi')
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d/m/Y H:i')
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
                    ->modalHeading('Detail Datang Mobil Supplier')
                    ->modalWidth('lg')
                    ->infolist([
                        TextEntry::make('id_task')->label('ID Task'),
                        TextEntry::make('supplier.nama_supplier')->label('Supplier'),
                        TextEntry::make('expedition.nama_ekspedisi')->label('Ekspedisi'),
                        TextEntry::make('nama_sopir')->label('Sopir'),
                        TextEntry::make('no_plat_mobil')->label('No Plat'),
                        TextEntry::make('tanggal_datang')->label('Tgl Datang')->date('d/m/Y'),
                        TextEntry::make('jam_datang')->label('Jam Datang'),
                        TextEntry::make('jam_selesai')->label('Jam Selesai'),
                        TextEntry::make('keterangan')->label('Keterangan'),
                    ]),
                EditAction::make()
                    ->color('warning')
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->modalHeading('Edit Datang Mobil Supplier')
                    ->modalWidth('lg'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->color('danger')
                        ->visible(fn () => auth()->user()?->hasRole('Admin') ?? false),
                ]),
            ]);
    }
}
