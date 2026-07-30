<?php

namespace App\Filament\Resources\SupplierReturn\Tables;

use App\Filament\Resources\SupplierReturn\Schemas\SupplierReturnForm;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SupplierReturnsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id_task')
                    ->label('ID Task')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jenis_pengiriman')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'retur_masuk' => 'info',
                        'retur_keluar' => 'warning',
                        'datang_dan_keluar' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'retur_masuk' => 'Retur Masuk',
                        'retur_keluar' => 'Retur Keluar',
                        'datang_dan_keluar' => 'Datang & Keluar',
                        default => $state,
                    })
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('nama_supplier')
                    ->label('Supplier')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('nama_ekspedisi')
                    ->label('Ekspedisi')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('nama_supir')
                    ->label('Supir')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('no_plat_mobil')
                    ->label('No Plat')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('tanggal_datang')
                    ->label('Tgl Datang')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jenis_retur_keluar')
                    ->label('Jenis Retur Keluar')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'servis' => 'warning',
                        'ganti_barang' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'servis' => 'Servis',
                        'ganti_barang' => 'Ganti Barang',
                        default => $state,
                    })
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jenis_retur_masuk')
                    ->label('Jenis Retur Masuk')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'potong_nota' => 'danger',
                        'servis' => 'warning',
                        'ganti_barang' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'potong_nota' => 'Potong Nota',
                        'servis' => 'Servis',
                        'ganti_barang' => 'Ganti Barang',
                        default => $state,
                    })
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('no_nota_retur')
                    ->label('No Nota')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('total_koli_keluar')
                    ->label('Koli Keluar')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('total_kolian_masuk')
                    ->label('Kolian Masuk')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'selesai' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'selesai' => 'Selesai',
                        default => $state,
                    })
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('user.name')
                    ->label('Checker')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()?->hasRole('Admin') ?? false)
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
            ])
            ->filters([
                SelectFilter::make('jenis_pengiriman')
                    ->label('Jenis')
                    ->options([
                        'retur_masuk' => 'Retur Masuk',
                        'retur_keluar' => 'Retur Keluar',
                        'datang_dan_keluar' => 'Datang & Keluar',
                    ])
                    ->placeholder('Semua'),
                Filter::make('created_at')
                    ->label('Tanggal Buat')
                    ->form([
                        Grid::make(2)->schema([
                            DatePicker::make('created_from')->label('Dari'),
                            DatePicker::make('created_until')->label('Sampai'),
                        ]),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['created_from'], fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                        ->when($data['created_until'], fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
                    ),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(2)
            ->recordAction('view')
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail')
                    ->color('info')
                    ->modalHeading('Detail Retur Supplier')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn (Action $action) => $action->label('Tutup'))
                    ->schema([
                        Section::make('Informasi Retur')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('id_task')->label('ID Task'),
                                TextEntry::make('jenis_pengiriman')->label('Jenis')->badge(),
                                TextEntry::make('nama_supplier')->label('Supplier'),
                                TextEntry::make('nama_ekspedisi')->label('Ekspedisi'),
                                TextEntry::make('nama_supir')->label('Supir'),
                                TextEntry::make('no_plat_mobil')->label('No Plat'),
                                TextEntry::make('tanggal_datang')->label('Tgl Datang')->date('d/m/Y'),
                                TextEntry::make('jam_kedatangan')->label('Jam Kedatangan'),
                                TextEntry::make('jenis_retur_keluar')->label('Jenis Retur Keluar')->badge(),
                                TextEntry::make('jenis_retur_masuk')->label('Jenis Retur Masuk')->badge(),
                                TextEntry::make('no_nota_retur')->label('No Nota'),
                                TextEntry::make('total_koli_keluar')->label('Koli Keluar'),
                                TextEntry::make('total_kolian_masuk')->label('Kolian Masuk'),
                                TextEntry::make('status')->label('Status')->badge(),
                                TextEntry::make('keterangan')->label('Keterangan')->columnSpanFull(),
                            ]),
                    ]),
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning')
                    ->modalWidth(Width::Full)
                    ->form(SupplierReturnForm::getFormFields()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->iconButton()
                        ->tooltip('Hapus Data')
                        ->color('danger')
                        ->visible(fn () => auth()->user()?->hasRole('Admin') ?? false),
                ]),
            ]);
    }
}
