<?php

namespace App\Filament\Resources\TaskTerimaSuppliers\Tables;

use App\Filament\Resources\TaskTerimaSuppliers\Schemas\TaskTerimaSupplierForm;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;

class TaskTerimaSuppliersTable
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
                    ->grow(false),
                TextColumn::make('arrivalSupplierTruck.no_plat_mobil')
                    ->label('Plat Mobil')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('nama_supplier_ekspedisi')
                    ->label('Supplier / Ekspedisi')
                    ->searchable()
                    ->width('160px')
                    ->grow(false),
                TextColumn::make('no_po_referensi')
                    ->label('No PO Referensi')
                    ->searchable()
                    ->width('140px')
                    ->grow(false),
                TextColumn::make('jumlah_kolian')
                    ->label('Kolian')
                    ->numeric()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('jam_datang')
                    ->label('Jam Datang')
                    ->time('H:i')
                    ->sortable()
                    ->grow(false),
                TextColumn::make('jam_bongkar')
                    ->label('Jam Bongkar')
                    ->time('H:i')
                    ->sortable()
                    ->grow(false),
                TextColumn::make('selesai_bongkar')
                    ->label('Selesai Bongkar')
                    ->time('H:i')
                    ->sortable()
                    ->grow(false),
                TextColumn::make('lembar_sj')
                    ->label('Lembar SJ')
                    ->numeric()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('nama_sopir')
                    ->label('Sopir')
                    ->searchable()
                    ->grow(false),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'selesai_tanpa_retur' => 'success',
                        'selesai_ada_retur' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'selesai_tanpa_retur' => 'Selesai',
                        'selesai_ada_retur' => 'Selesai Ada Retur',
                        default => $state,
                    })
                    ->grow(false),
                TextColumn::make('user.name')
                    ->label('Checker')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()?->hasRole('Admin') ?? false)
                    ->grow(false),
                TextColumn::make('helpers_names')
                    ->label('Helpers')
                    ->badge()
                    ->color('success')
                    ->getStateUsing(function ($record) {
                        $names = $record->helpers->pluck('nama_karyawan');
                        $result = $names->take(2)->toArray();
                        if ($names->count() > 2) {
                            $result[] = '+' . ($names->count() - 2) . ' more';
                        }
                        return $result;
                    }),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->grow(false),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('created_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->recordAction('view')
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail')
                    ->color('info')
                    ->modalHeading('Detail Terima Supplier')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn (Action $action) => $action->label('Tutup'))
                    ->schema([
                        Section::make('Informasi Task')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('id_task')->label('ID Task'),
                                TextEntry::make('arrivalSupplierTruck.no_plat_mobil')->label('Plat Mobil'),
                                TextEntry::make('arrivalSupplierTruck.id_task')->label('ID Task Mobil Datang'),
                                TextEntry::make('arrivalSupplierTruck.jenis_kiriman')->label('Jenis Kiriman')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'DATANG' => 'info',
                                        'RETUR' => 'warning',
                                        'DATANG & RETUR' => 'primary',
                                        default => 'gray',
                                    }),
                                TextEntry::make('arrivalSupplierTruck.supplier.nama_supplier')->label('Supplier'),
                                TextEntry::make('nama_supplier_ekspedisi')->label('Supplier / Ekspedisi'),
                                TextEntry::make('no_po_referensi')->label('No PO Referensi'),
                                TextEntry::make('jam_datang')->label('Jam Datang'),
                                TextEntry::make('jumlah_kolian')->label('Kolian'),
                                TextEntry::make('jam_bongkar')->label('Jam Bongkar'),
                                TextEntry::make('selesai_bongkar')->label('Selesai Bongkar'),
                                TextEntry::make('lembar_sj')->label('Lembar SJ'),
                                TextEntry::make('nama_sopir')->label('Sopir'),
                                TextEntry::make('status')->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'draft' => 'gray',
                                        'selesai_tanpa_retur' => 'success',
                                        'selesai_ada_retur' => 'warning',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'draft' => 'Draft',
                                        'selesai_tanpa_retur' => 'Selesai',
                                        'selesai_ada_retur' => 'Selesai Ada Retur',
                                        default => $state,
                                    }),
                                TextEntry::make('helpers_list')
                                    ->label('Helpers')
                                    ->badge()
                                    ->color('success')
                                    ->separator(', ')
                                    ->state(function ($record) {
                                        return $record->helpers->pluck('nama_karyawan')->toArray();
                                    }),
                                TextEntry::make('keterangan')->label('Keterangan')->columnSpanFull(),
                            ]),
                    ]),
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning')
                    ->modalWidth(Width::Full)
                    ->form(TaskTerimaSupplierForm::getFormFields())
                    ->using(function ($record, array $data) {
                        $helpers = $data['helpers'] ?? [];
                        unset($data['helpers'], $data['jenis_kiriman_tampil']);
                        $record->update($data);
                        $record->helpers()->sync(filled($helpers) ? $helpers : []);
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
