<?php

namespace App\Filament\Resources\BranchReturnOutbound\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BranchReturnOutboundsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(50)
            ->defaultSort('created_at', 'desc')
            ->recordAction('view')
            ->columns([
                TextColumn::make('nomor_sj')
                    ->label('No SJ')
                    ->searchable()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('toko_tujuan')
                    ->label('Toko Tujuan')
                    ->searchable()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('total_qty')
                    ->label('Qty')
                    ->numeric()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('disiapkan_oleh')
                    ->label('Disiapkan')
                    ->grow(false),
                TextColumn::make('jam_naik')
                    ->label('Jam Naik')
                    ->time('H:i')
                    ->grow(false),
                TextColumn::make('status')
                    ->label('Status')
                    ->grow(false),
                TextColumn::make('diserahkan_kepada')
                    ->label('Diserahkan Ke')
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
                    ->modalHeading('Detail Retur Keluar Cabang')
                    ->modalWidth('lg')
                    ->infolist([
                        TextEntry::make('nomor_sj')->label('No SJ'),
                        TextEntry::make('toko_tujuan')->label('Toko Tujuan'),
                        TextEntry::make('total_qty')->label('Total Qty'),
                        TextEntry::make('disiapkan_oleh')->label('Disiapkan Oleh'),
                        TextEntry::make('jam_naik')->label('Jam Naik'),
                        TextEntry::make('diserahkan_kepada')->label('Diserahkan Kepada'),
                        TextEntry::make('status')->label('Status'),
                        TextEntry::make('keterangan')->label('Keterangan'),
                    ]),
                EditAction::make()
                    ->color('warning')
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->modalHeading('Edit Retur Keluar Cabang')
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
