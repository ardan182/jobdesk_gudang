<?php

namespace App\Filament\Resources\KendaraanDokumen\Tables;

use App\Filament\Resources\KendaraanDokumen\Schemas\KendaraanDokumenForm;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KendaraanDokumensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->defaultGroup('periode')
            ->description('Dibuat otomatis, jika tidak muncul lengkapi data di master kendaraan')
            ->modifyQueryUsing(fn (Builder $query) => $query->where(function (Builder $q) {
                $q->where('jenis', '!=', 'kir')
                    ->orWhereNotNull('masa_berlaku');
            }))
            ->groups([
                Group::make('periode')
                    ->getTitleFromRecordUsing(fn ($record) => match ($record->periode) {
                        '1_tahun' => 'ðŸš— STNK 1 Tahun',
                        '5_tahun' => 'ðŸš— STNK 5 Tahun (Acuan)',
                        default => 'ðŸš KIR',
                    }),
            ])
            ->groupingSettingsInDropdownOnDesktop(false)
            ->columns([
                TextColumn::make('kendaraan.nomor_polisi')
                    ->label('No Pol')
                    ->searchable()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('kendaraan.merek_dan_model')
                    ->label('Merek')
                    ->searchable()
                    ->grow(false),
                TextColumn::make('jenis_display')
                    ->label('Jenis')
                    ->badge()
                    ->getStateUsing(fn ($record) => match ($record->periode) {
                        '5_tahun' => 'STNK 5 Thn',
                        '1_tahun' => 'STNK 1 Thn',
                        default => 'KIR',
                    })
                    ->color(fn ($record): string => $record->periode === '5_tahun' ? 'purple' : ($record->jenis === 'stnk' ? 'info' : 'warning'))
                    ->grow(false),
                TextColumn::make('nomor_dokumen')
                    ->label('No Dokumen')
                    ->searchable()
                    ->grow(false),
                TextColumn::make('periode_label')
                    ->label('Periode')
                    ->grow(false),
                TextColumn::make('masa_berlaku')
                    ->label('Masa Berlaku')
                    ->date('d/m/Y')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record): string => $record->status_warna)
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('user_perpanjang')
                    ->label('User')
                    ->searchable()
                    ->grow(false),
                TextColumn::make('is_5_tahun_label')
                    ->label('Sumber')
                    ->badge()
                    ->color(fn ($record): string => $record->periode === '5_tahun' ? 'purple' : 'gray')
                    ->getStateUsing(fn ($record) => $record->periode === '5_tahun' ? 'Dari Master' : 'Input Manual')
                    ->grow(false),
                TextColumn::make('status_label')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record): string => $record->status_warna)
                    ->grow(false),
            ])
            ->filters([
                SelectFilter::make('periode')
                    ->label('Jenis Dokumen')
                    ->options([
                        '1_tahun' => 'STNK 1 Tahun',
                        '5_tahun' => 'STNK 5 Tahun',
                    ])
                    ->placeholder('Semua'),
            ])
            ->recordAction('view')
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail')
                    ->color('info')
                    ->modalHeading('Detail Dokumen')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn (Action $action) => $action->label('Tutup'))
                    ->schema([
                        Section::make('Detail Dokumen')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('kendaraan.nomor_polisi')->label('No Polisi'),
                                TextEntry::make('kendaraan.merek_dan_model')->label('Merek'),
                                TextEntry::make('jenis_display')
                                    ->label('Jenis')
                                    ->badge()
                                    ->getStateUsing(fn ($record) => match ($record->periode) {
                                        '5_tahun' => 'STNK 5 Thn',
                                        '1_tahun' => 'STNK 1 Thn',
                                        default => 'KIR',
                                    })
                                    ->color(fn ($record): string => $record->periode === '5_tahun' ? 'purple' : ($record->jenis === 'stnk' ? 'info' : 'warning')),
                                TextEntry::make('nomor_dokumen')->label('No Dokumen'),
                                TextEntry::make('periode_label')->label('Periode'),
                                TextEntry::make('masa_berlaku')->label('Masa Berlaku')->date('d/m/Y')
                                    ->badge()
                                    ->color(fn ($record): string => $record->status_warna),
                                TextEntry::make('user_perpanjang')->label('User Perpanjang'),
                                TextEntry::make('status_label')->label('Status')
                                    ->badge()
                                    ->color(fn ($record): string => $record->status_warna),
                                TextEntry::make('keterangan')->label('Keterangan')->columnSpanFull(),
                            ]),
                    ]),
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning')
                    ->modalHeading('Edit Dokumen')
                    ->modalWidth(Width::Full)
                    ->form(KendaraanDokumenForm::getFormFields()),
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
