<?php

namespace App\Filament\Resources\MasterKendaraans\Tables;

use App\Filament\Resources\MasterKendaraans\Schemas\MasterKendaraanForm;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;
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
                    ->sortable()
                    ->grow(false),
                TextColumn::make('jenis_kendaraan')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'mobil' ? 'info' : 'warning')
                    ->formatStateUsing(fn (string $state): string => $state === 'mobil' ? 'Mobil' : 'Motor')
                    ->grow(false),
                TextColumn::make('merek_dan_model')
                    ->label('Merek & Model')
                    ->searchable()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('masa_berlaku_stnk')
                    ->label('STNK 1 Thn')
                    ->date('d/m/Y')
                    ->sortable()
                    ->grow(false),
                TextColumn::make('stnk_5_tahun_sampai')
                    ->label('STNK 5 Thn')
                    ->date('d/m/Y')
                    ->sortable()
                    ->badge()
                    ->color(function ($record): string {
                        if (!$record->stnk_5_tahun_sampai) return 'gray';
                        $days = now()->startOfDay()->diffInDays($record->stnk_5_tahun_sampai, false);
                        if ($days < 0) return 'danger';
                        if ($days <= 365) return 'warning';
                        return 'success';
                    })
                    ->grow(false),
                TextColumn::make('masa_berlaku_kir')
                    ->label('Masa Berlaku KIR')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('nomor_rangka')
                    ->label('Nomor Rangka')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->grow(false),
                TextColumn::make('nomor_mesin')
                    ->label('Nomor Mesin')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->grow(false),
                TextColumn::make('no_stnk')
                    ->label('No STNK')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->grow(false),
                TextColumn::make('no_kir')
                    ->label('No KIR')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn ($record) => filled($record?->masa_berlaku_kir))
                    ->grow(false),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->grow(false),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail')
                    ->color('info')
                    ->modalHeading('Detail Kendaraan')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn (Action $action) => $action->label('Tutup'))
                    ->schema([
                        Section::make('Informasi Kendaraan')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('nomor_polisi')->label('Nomor Polisi'),
                                TextEntry::make('jenis_kendaraan')
                                    ->label('Jenis Kendaraan')
                                    ->formatStateUsing(fn (string $state): string => $state === 'mobil' ? 'Mobil' : 'Motor'),
                                TextEntry::make('merek_dan_model')->label('Merek dan Model'),
                                TextEntry::make('masa_berlaku_stnk')->label('STNK 1 Thn')->date('d/m/Y'),
                                TextEntry::make('masa_berlaku_kir')->label('Masa Berlaku KIR')->date('d/m/Y')
                                    ->visible(fn ($record) => filled($record?->masa_berlaku_kir)),
                                TextEntry::make('stnk_5_tahun_sampai')->label('STNK 5 Thn')->date('d/m/Y')
                                    ->badge()
                                    ->color(function ($record): string {
                                        if (!$record->stnk_5_tahun_sampai) return 'gray';
                                        $days = now()->startOfDay()->diffInDays($record->stnk_5_tahun_sampai, false);
                                        if ($days < 0) return 'danger';
                                        if ($days <= 365) return 'warning';
                                        return 'success';
                                    }),
                                TextEntry::make('nomor_rangka')->label('Nomor Rangka'),
                                TextEntry::make('nomor_mesin')->label('Nomor Mesin'),
                                TextEntry::make('no_stnk')->label('No STNK'),
                                TextEntry::make('no_kir')->label('No KIR')
                                    ->visible(fn ($record) => filled($record?->masa_berlaku_kir)),
                                TextEntry::make('keterangan')->label('Keterangan')->columnSpanFull(),
                            ]),
                    ]),
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning')
                    ->modalHeading('Edit Kendaraan')
                    ->form(MasterKendaraanForm::getFormFields())
                    ->modalWidth(Width::Full),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->iconButton()
                        ->tooltip('Hapus Data')
                        ->color('danger')
                        ->visible(fn () => auth()->user()?->isSuperAdmin() ?? false),
                ]),
            ]);
    }
}
