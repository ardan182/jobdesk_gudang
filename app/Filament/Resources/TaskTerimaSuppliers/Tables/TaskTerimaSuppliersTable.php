<?php

namespace App\Filament\Resources\TaskTerimaSuppliers\Tables;

use App\Filament\Resources\TaskTerimaSuppliers\Schemas\TaskTerimaSupplierForm;
use App\Services\TableExportService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
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
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('arrivalSupplierTruck.supplier.nama_supplier')
                    ->label('Supplier')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('arrivalSupplierTruck.expedition.nama_ekspedisi')
                    ->label('Ekspedisi')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('no_po_referensi')
                    ->label('No PO Referensi')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jumlah_kolian')
                    ->label('Kolian')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jam_datang')
                    ->label('Jam Datang')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jam_bongkar')
                    ->label('Jam Bongkar')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('selesai_bongkar')
                    ->label('Selesai Bongkar')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('lama_bongkar')
                    ->label('Lama Bkr')
                    ->toggleable()
                    ->grow(false)
                    ->getStateUsing(function ($record) {
                        if (!$record->jam_bongkar || !$record->selesai_bongkar) return '-';
                        $minutes = \Carbon\Carbon::parse($record->jam_bongkar)->diffInMinutes(\Carbon\Carbon::parse($record->selesai_bongkar));
                        $h = intdiv($minutes, 60);
                        $m = $minutes % 60;
                        return $h > 0 ? "{$h}j {$m}m" : "{$m}m";
                    }),
                TextColumn::make('lembar_sj')
                    ->label('Lembar SJ')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('nama_sopir')
                    ->label('Sopir')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->toggleable()
                    ->color(fn (string $state): string => match ($state) {
                        'DRAFT' => 'gray',
                        'SELESAI' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'DRAFT' => 'Draft',
                        'SELESAI' => 'Selesai',
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
                    ->color('warning')
                    ->toggleable()
                    ->tooltip(fn ($record) => $record->helpers->pluck('nama_karyawan')->implode(', '))
                    ->getStateUsing(function ($record) {
                        $names = $record->helpers->pluck('nama_karyawan');
                        $result = $names->take(2)->toArray();
                        if ($names->count() > 2) {
                            $result[] = '+' . ($names->count() - 2) . ' more';
                        }
                        return $result;
                    })
                    ->grow(false),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
            ])
            ->filters([
                SelectFilter::make('arrivalSupplierTruck.supplier_id')
                    ->label('Supplier')
                    ->relationship('arrivalSupplierTruck.supplier', 'nama_supplier')
                    ->searchable()
                    ->placeholder('Semua Supplier'),
                Filter::make('created_at')
                    ->label('Tanggal')
                    ->form([
                        Grid::make(2)->schema([
                            DatePicker::make('created_from')
                                ->label('Dari')
                                ->helperText('tgl mulai'),
                            DatePicker::make('created_until')
                                ->label('Sampai')
                                ->helperText('tgl akhir'),
                        ]),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['created_from'], fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                        ->when($data['created_until'], fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
                    ),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'DRAFT' => 'Draft',
                        'SELESAI' => 'Selesai',
                    ])
                    ->placeholder('Semua Status'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
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
                                TextEntry::make('arrivalSupplierTruck.expedition.nama_ekspedisi')->label('Ekspedisi'),
                                TextEntry::make('no_po_referensi')->label('No PO Referensi'),
                                TextEntry::make('jam_datang')->label('Jam Datang'),
                                TextEntry::make('jumlah_kolian')->label('Kolian'),
                                TextEntry::make('jam_bongkar')->label('Jam Bongkar'),
                                TextEntry::make('lama_bongkar')
                                    ->label('Lama Bongkar')
                                    ->getStateUsing(function ($record) {
                                        if (!$record->jam_bongkar || !$record->selesai_bongkar) return '-';
                                        $minutes = \Carbon\Carbon::parse($record->jam_bongkar)->diffInMinutes(\Carbon\Carbon::parse($record->selesai_bongkar));
                                        $h = intdiv($minutes, 60);
                                        $m = $minutes % 60;
                                        return $h > 0 ? "{$h} jam {$m} menit" : "{$m} menit";
                                    }),
                                TextEntry::make('selesai_bongkar')->label('Selesai Bongkar'),
                                TextEntry::make('lembar_sj')->label('Lembar SJ'),
                                TextEntry::make('nama_sopir')->label('Sopir'),
                                TextEntry::make('status')->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'DRAFT' => 'gray',
                                        'SELESAI' => 'success',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'DRAFT' => 'Draft',
                                        'SELESAI' => 'Selesai',
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
                Action::make('export_xlsx')
                    ->label('Export XLSX')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->outlined()
                    ->size(Size::Small)
                    ->action(fn (Action $action) => TableExportService::streamXlsx(
                        $action->getLivewire()->getFilteredTableQuery(),
                        self::exportColumns(),
                        'terima-supplier',
                    )),
                Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->outlined()
                    ->size(Size::Small)
                    ->action(fn (Action $action) => TableExportService::streamPdf(
                        $action->getLivewire()->getFilteredTableQuery(),
                        self::exportColumns(),
                        'terima-supplier',
                    )),
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->iconButton()
                        ->tooltip('Hapus Data')
                        ->color('danger')
                        ->visible(fn () => auth()->user()?->hasRole('Admin') ?? false),
                ]),
            ]);
    }

    /**
     * @return array<string, string>
     */
    public static function exportColumns(): array
    {
        return [
            'ID Task' => 'id_task',
            'Supplier' => 'arrivalSupplierTruck.supplier.nama_supplier',
            'Ekspedisi' => 'arrivalSupplierTruck.expedition.nama_ekspedisi',
            'No PO Referensi' => 'no_po_referensi',
            'Kolian' => 'jumlah_kolian',
            'Jam Datang' => 'jam_datang',
            'Jam Bongkar' => 'jam_bongkar',
            'Selesai Bongkar' => 'selesai_bongkar',
            'Lembar SJ' => 'lembar_sj',
            'Sopir' => 'nama_sopir',
            'Status' => 'status',
            'Keterangan' => 'keterangan',
        ];
    }
}
