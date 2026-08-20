<?php

namespace App\Filament\Resources\TaskDatangMobilSuppliers\Tables;

use App\Filament\Resources\TaskDatangMobilSuppliers\Schemas\TaskDatangMobilSupplierForm;
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
                TextColumn::make('expedition.nama_ekspedisi')
                    ->label('Ekspedisi')
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('nama_sopir')
                    ->label('Sopir')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('no_plat_mobil')
                    ->label('No Plat')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jenis_kiriman')
                    ->label('Jenis Kiriman')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'DATANG' => 'info',
                        'RETUR' => 'warning',
                        'DATANG & RETUR' => 'primary',
                        default => 'gray',
                    })
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('tanggal_datang')
                    ->label('Tgl Datang')
                    ->date('d/m/Y')
                    ->sortable()
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
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'MENGANTRI' => 'gray',
                        'PROSES' => 'warning',
                        'SELESAI' => 'success',
                        default => 'gray',
                    })
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
            ->filters([
                SelectFilter::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'nama_supplier')
                    ->searchable()
                    ->placeholder('Semua Supplier'),
                SelectFilter::make('expedition_id')
                    ->label('Ekspedisi')
                    ->relationship('expedition', 'nama_ekspedisi')
                    ->searchable()
                    ->placeholder('Semua Ekspedisi'),
                SelectFilter::make('jenis_kiriman')
                    ->label('Jenis Kiriman')
                    ->options([
                        'DATANG' => 'Datang',
                        'RETUR' => 'Retur',
                        'DATANG & RETUR' => 'Datang & Retur',
                    ])
                    ->placeholder('Semua'),
                Filter::make('tanggal_datang')
                    ->label('Tgl Datang')
                    ->form([
                        Grid::make(2)->schema([
                            DatePicker::make('datang_dari')
                                ->label('Dari')
                                ->helperText('tgl mulai'),
                            DatePicker::make('datang_sampai')
                                ->label('Sampai')
                                ->helperText('tgl akhir'),
                        ]),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['datang_dari'], fn ($q, $d) => $q->whereDate('tanggal_datang', '>=', $d))
                        ->when($data['datang_sampai'], fn ($q, $d) => $q->whereDate('tanggal_datang', '<=', $d))
                    ),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'MENGANTRI' => 'Mengantri',
                        'PROSES' => 'Proses',
                        'SELESAI' => 'Selesai',
                    ])
                    ->placeholder('Semua Status'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(5)
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail')
                    ->color('info')
                    ->modalHeading('Detail Datang Mobil Supplier')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn (Action $action) => $action->label('Tutup'))
                    ->schema([
                        Section::make('Informasi Mobil Supplier')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('id_task')->label('ID Task'),
                                TextEntry::make('supplier.nama_supplier')->label('Supplier'),
                                TextEntry::make('expedition.nama_ekspedisi')->label('Ekspedisi'),
                                TextEntry::make('nama_sopir')->label('Sopir'),
                                TextEntry::make('no_plat_mobil')->label('No Plat'),
                                TextEntry::make('jenis_kiriman')->label('Jenis Kiriman')->badge(),
                                TextEntry::make('tanggal_datang')->label('Tgl Datang')->date('d/m/Y'),
                                TextEntry::make('jam_datang')->label('Jam Datang'),
                                TextEntry::make('jam_selesai')->label('Jam Selesai'),
                                TextEntry::make('status')->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'MENGANTRI' => 'gray',
                                        'PROSES' => 'warning',
                                        'SELESAI' => 'success',
                                        default => 'gray',
                                    }),
                                TextEntry::make('keterangan')->label('Keterangan'),
                            ]),
                    ]),
                EditAction::make()
                    ->visible(fn () => auth()->user()?->can('update_task_datang_mobil_suppliers') ?? false)
                    ->color('warning')
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->modalHeading('Edit Datang Mobil Supplier')
                    ->modalWidth(Width::SevenExtraLarge)
                    ->form(TaskDatangMobilSupplierForm::getFormFields()),
            ])
            ->toolbarActions([
                Action::make('export_xlsx')
                    ->label('Export XLSX')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->outlined()
                    ->size(Size::Small)
                    ->modalHeading('Export XLSX — Pilih Kolom')
                    ->modalSubmitActionLabel('Export')
                    ->form([
                        TableExportService::exportColumnCheckboxList(self::exportColumns()),
                    ])
                    ->action(fn (Action $action, array $data) => TableExportService::streamXlsx(
                        $action->getLivewire()->getFilteredTableQuery(),
                        TableExportService::filterExportColumns(self::exportColumns(), $data['columns']),
                        'datang-mobil-supplier',
                    )),
                Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->outlined()
                    ->size(Size::Small)
                    ->modalHeading('Export PDF — Pilih Kolom')
                    ->modalSubmitActionLabel('Export')
                    ->form([
                        TableExportService::exportColumnCheckboxList(self::exportColumns()),
                    ])
                    ->action(fn (Action $action, array $data) => TableExportService::streamPdf(
                        $action->getLivewire()->getFilteredTableQuery(),
                        TableExportService::filterExportColumns(self::exportColumns(), $data['columns']),
                        'datang-mobil-supplier',
                    )),
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->color('danger')
                        ->visible(fn () => auth()->user()?->can('delete_task_datang_mobil_suppliers') ?? false),
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
            'Supplier' => 'supplier.nama_supplier',
            'Ekspedisi' => 'expedition.nama_ekspedisi',
            'Sopir' => 'nama_sopir',
            'No Plat' => 'no_plat_mobil',
            'Jenis Kiriman' => 'jenis_kiriman',
            'Tgl Datang' => 'tanggal_datang',
            'Jam Datang' => 'jam_datang',
            'Jam Selesai' => 'jam_selesai',
            'Status' => 'status',
            'Keterangan' => 'keterangan',
        ];
    }
}
