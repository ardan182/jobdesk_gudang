<?php

namespace App\Filament\Resources\MasterKendaraans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MasterKendaraansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(25)
            ->defaultSort('created_at', 'desc')
            ->recordUrl(null)
            ->columns([
                TextColumn::make('nomor_polisi')
                    ->label('Nomor Polisi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jenis_kendaraan')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'mobil' ? 'info' : 'warning')
                    ->formatStateUsing(fn (string $state): string => $state === 'mobil' ? 'Mobil' : 'Motor'),
                TextColumn::make('merek_dan_model')
                    ->label('Merek & Model')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('masa_berlaku_stnk')
                    ->label('Masa Berlaku STNK')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('masa_berlaku_kir')
                    ->label('Masa Berlaku KIR')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('nomor_rangka')
                    ->label('Nomor Rangka')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('nomor_mesin')
                    ->label('Nomor Mesin')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('no_stnk')
                    ->label('No STNK')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('no_kir')
                    ->label('No KIR')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalHeading('Detail Kendaraan')
                    ->modalWidth('lg')
                    ->infolist([
                        TextEntry::make('nomor_polisi')->label('Nomor Polisi'),
                        TextEntry::make('jenis_kendaraan')
                            ->label('Jenis Kendaraan')
                            ->formatStateUsing(fn (string $state): string => $state === 'mobil' ? 'Mobil' : 'Motor'),
                        TextEntry::make('merek_dan_model')->label('Merek dan Model'),
                        TextEntry::make('masa_berlaku_stnk')->label('Masa Berlaku STNK')->date('d/m/Y'),
                        TextEntry::make('masa_berlaku_kir')->label('Masa Berlaku KIR')->date('d/m/Y'),
                        TextEntry::make('nomor_rangka')->label('Nomor Rangka'),
                        TextEntry::make('nomor_mesin')->label('Nomor Mesin'),
                        TextEntry::make('no_stnk')->label('No STNK'),
                        TextEntry::make('no_kir')->label('No KIR'),
                        TextEntry::make('keterangan')->label('Keterangan'),
                    ]),
                EditAction::make()
                    ->modalHeading('Edit Kendaraan')
                    ->modalWidth('lg'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasRole('Admin') ?? false),
                ]),
            ]);
    }
}
