<?php

namespace App\Filament\Resources\BranchShipment\Tables;

use App\Filament\Resources\BranchShipment\Schemas\BranchShipmentForm;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BranchShipmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(50)
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->columns([
                TextColumn::make('id_task')
                    ->label('ID Task')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('pilih_kiriman')
                    ->label('Kiriman')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pembagian_po' => 'Pembagian PO',
                        'stock_gudang' => 'Stock Gudang',
                        'rb_pesanan' => 'RB / Pesanan',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pembagian_po' => 'info',
                        'stock_gudang' => 'warning',
                        'rb_pesanan' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('cabang')
                    ->label('Cabang')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('nomor_sj')
                    ->label('No SJ')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('no_po')
                    ->label('No PO')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('total_qty')
                    ->label('Qty')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('tanggal_buat')
                    ->label('Tgl Buat')
                    ->date('d/m/Y')
                    ->sortable()
                    ->grow(false),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'warning',
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
                    ->label('Dibuat')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()?->hasRole('Admin') ?? false)
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->grow(false),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('pilih_kiriman')
                    ->label('Kiriman')
                    ->options([
                        'pembagian_po' => 'Pembagian PO',
                        'stock_gudang' => 'Stock Gudang',
                        'rb_pesanan' => 'RB / Pesanan',
                    ])
                    ->placeholder('Semua'),
                SelectFilter::make('cabang')
                    ->label('Cabang')
                    ->options(fn () => \App\Models\MasterToko::pluck('nama_toko', 'nama_toko'))
                    ->searchable()
                    ->placeholder('Semua'),
                Filter::make('tanggal_buat')
                    ->label('Tanggal Buat')
                    ->form([
                        Grid::make(2)->schema([
                            DatePicker::make('tanggal_dari')->label('Dari'),
                            DatePicker::make('tanggal_sampai')->label('Sampai'),
                        ]),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['tanggal_dari'], fn ($q, $d) => $q->whereDate('tanggal_buat', '>=', $d))
                        ->when($data['tanggal_sampai'], fn ($q, $d) => $q->whereDate('tanggal_buat', '<=', $d))
                    ),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'selesai' => 'Selesai',
                    ])
                    ->placeholder('Semua'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail')
                    ->color('info')
                    ->modalHeading('Detail Kirim Barang')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn (Action $action) => $action->label('Tutup'))
                    ->schema([
                        Section::make('Informasi Kirim Barang')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('pilih_kiriman')
                                    ->label('Kiriman')
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'pembagian_po' => 'Pembagian dari PO',
                                        'stock_gudang' => 'Stock Gudang',
                                        'rb_pesanan' => 'RB / Pesanan',
                                        default => $state,
                                    }),
                                TextEntry::make('cabang')->label('Cabang'),
                                TextEntry::make('nomor_sj')->label('No SJ'),
                                TextEntry::make('total_qty')->label('Total Qty'),
                                TextEntry::make('no_po')->label('No PO'),
                                TextEntry::make('tanggal_buat')->label('Tgl Buat')->date('d/m/Y'),
                                TextEntry::make('status')->label('Status')->badge(),
                                TextEntry::make('keterangan')->label('Keterangan'),
                            ]),
                    ]),
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning')
                    ->modalHeading('Edit Kirim Barang')
                    ->modalWidth(Width::Full)
                    ->form(BranchShipmentForm::getFormFields())
                    ->using(function ($record, array $data) {
                        $record->update($data);
                    }),
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
