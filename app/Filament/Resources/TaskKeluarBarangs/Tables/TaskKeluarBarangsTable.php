<?php

namespace App\Filament\Resources\TaskKeluarBarangs\Tables;

use App\Filament\Resources\TaskKeluarBarangs\Schemas\TaskKeluarBarangForm;
use App\Models\WarehouseEmployee;
use App\Services\TableExportService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class TaskKeluarBarangsTable
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
                TextColumn::make('cabang')
                    ->label('Cabang')
                    ->searchable()
                    ->width('120px')
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('nomor_sj')
                    ->label('No SJ')
                    ->searchable()
                    ->width('130px')
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('total_qty')
                    ->label('Qty Input')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('qty_checker')
                    ->label('Qty Checker')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('no_po')
                    ->label('No PO')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jam_disiapkan')
                    ->label('Jam Disiapkan')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'siap kirim' => 'warning',
                        'selesai' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'siap kirim' => 'Siap Kirim',
                        'selesai' => 'Selesai',
                        default => $state,
                    })
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('diserahkan_kepada')
                    ->label('Diserahkan')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('helper')
                    ->label('Helper')
                    ->badge()
                    ->color('warning')
                    ->tooltip(fn ($record) => $record->helper
                        ? WarehouseEmployee::whereIn('id', $record->helper)
                            ->pluck('nama_karyawan')
                            ->implode(', ')
                        : '')
                    ->getStateUsing(function ($record) {
                        if (!$record->helper) return [];
                        $names = WarehouseEmployee::whereIn('id', $record->helper)->pluck('nama_karyawan');
                        $result = $names->take(2)->toArray();
                        if ($names->count() > 2) {
                            $result[] = '+' . ($names->count() - 2) . ' more';
                        }
                        return $result;
                    })
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('user.name')
                    ->label('Checker')
                    ->searchable()
                    ->sortable()
->visible(fn () => auth()->user()?->isSuperAdmin() ?? false)
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
                SelectFilter::make('cabang')
                    ->label('Cabang')
                    ->options(fn () => \App\Models\MasterToko::pluck('nama_toko', 'nama_toko'))
                    ->searchable()
                    ->placeholder('Semua Cabang'),
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
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'siap kirim' => 'Siap Kirim',
                        'selesai' => 'Selesai',
                    ])
                    ->placeholder('Semua Status'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->recordAction('view')
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail')
                    ->color('info')
                    ->modalHeading('Detail Keluar Barang')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn (Action $action) => $action->label('Tutup'))
                    ->schema([
                        Section::make('Informasi Task')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('id_task')->label('ID Task'),
                                TextEntry::make('cabang')->label('Cabang'),
                                TextEntry::make('nomor_sj')->label('No SJ'),
                                TextEntry::make('total_qty')->label('Qty Input'),
                                TextEntry::make('qty_checker')->label('Qty Checker'),
                                TextEntry::make('no_po')->label('No PO'),
                                TextEntry::make('jam_disiapkan')->label('Jam Disiapkan'),
                                TextEntry::make('status')->label('Status')->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'draft' => 'gray',
                                        'siap kirim' => 'warning',
                                        'selesai' => 'success',
                                        default => 'gray',
                                    }),
                                TextEntry::make('diserahkan_kepada')->label('Diserahkan Kepada'),
                                TextEntry::make('helper')
                                    ->label('Helper')
                                    ->badge()
                                    ->color('success')
                                    ->separator(', ')
                                    ->state(fn ($record) => $record->helper
                                        ? WarehouseEmployee::whereIn('id', $record->helper)->pluck('nama_karyawan')->toArray()
                                        : []),
                                TextEntry::make('keterangan')->label('Keterangan')->columnSpanFull(),
                            ]),
                    ]),
                EditAction::make()
                    ->visible(fn () => auth()->user()?->can('update_task_keluar_barangs') ?? false)
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning')
                    ->modalWidth(Width::SevenExtraLarge)
                    ->form(TaskKeluarBarangForm::getFormFields()),
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
                        'checker-keluar-barang',
                        self::exportFormatters(),
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
                        'checker-keluar-barang',
                        self::exportFormatters(),
                    )),
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->iconButton()
                        ->tooltip('Hapus Data')
                        ->color('danger')
                        ->visible(fn () => auth()->user()?->can('delete_task_keluar_barangs') ?? false),
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
            'Cabang' => 'cabang',
            'No SJ' => 'nomor_sj',
            'Qty Input' => 'total_qty',
            'Qty Checker' => 'qty_checker',
            'No PO' => 'no_po',
            'Jam Disiapkan' => 'jam_disiapkan',
            'Status' => 'status',
            'Diserahkan' => 'diserahkan_kepada',
            'Helper' => 'helper',
            'Checker' => 'user.name',
            'Tanggal' => 'created_at',
        ];
    }

    /**
     * @return array<string, callable>
     */
    public static function exportFormatters(): array
    {
        return [
            'helper' => fn ($record) => $record->helper
                ? WarehouseEmployee::whereIn('id', $record->helper)->pluck('nama_karyawan')->implode(', ')
                : '',
        ];
    }
}
