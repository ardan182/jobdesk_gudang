<?php

namespace App\Filament\Resources\TaskKirimanMobils\Tables;

use App\Filament\Resources\TaskKirimanMobils\Schemas\TaskKirimanMobilForm;
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

class TaskKirimanMobilsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with('branchShipments'))
            ->columns([
                TextColumn::make('id_task')
                    ->label('ID Task')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->grow(false),
                TextColumn::make('cabang')
                    ->label('Cabang')
                    ->searchable()
                    ->width('120px')
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('total_sj')
                    ->label('Total SJ')
                    ->numeric()
                    ->toggleable()
                    ->grow(false)
                    ->getStateUsing(fn ($record) => $record->branchShipments->count()),
                TextColumn::make('branch_sj_list')
                    ->label('SJ')
                    ->badge()
                    ->color('info')
                    ->tooltip(fn ($record) => $record->branchShipments->pluck('nomor_sj')->implode(', '))
                    ->getStateUsing(function ($record) {
                        $sj = $record->branchShipments->pluck('nomor_sj');
                        $result = $sj->take(2)->toArray();
                        if ($sj->count() > 2) {
                            $result[] = '+' . ($sj->count() - 2) . ' more';
                        }
                        return $result;
                        })
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('tanggal_kirim')
                    ->label('Tgl Kirim')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('no_plat_mobil')
                    ->label('No Plat')
                    ->searchable()
                    ->width('130px')
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jam_muat')
                    ->label('Jam Muat')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jam_selesai_muat')
                    ->label('Jam Selesai')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jam_berangkat')
                    ->label('Brkt')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jam_tiba')
                    ->label('Tiba')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'dalam pengiriman' => 'warning',
                        'selesai' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'dalam pengiriman' => 'Dalam Pengiriman',
                        'selesai' => 'Selesai',
                        default => $state,
                    })
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('retur_option')
                    ->label('Retur')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'tidak_ada_retur' => 'gray',
                        'ada_retur' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'tidak_ada_retur' => 'Tidak Ada Retur',
                        'ada_retur' => 'Ada Retur',
                        default => $state,
                    })
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('nama_supir')
                    ->label('Supir')
                    ->searchable()
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
                Filter::make('tanggal_kirim')
                    ->label('Tanggal Kirim')
                    ->form([
                        Grid::make(2)->schema([
                            DatePicker::make('kirim_dari')
                                ->label('Dari')
                                ->helperText('tgl kirim mulai'),
                            DatePicker::make('kirim_sampai')
                                ->label('Sampai')
                                ->helperText('tgl kirim akhir'),
                        ]),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['kirim_dari'], fn ($q, $d) => $q->whereDate('tanggal_kirim', '>=', $d))
                        ->when($data['kirim_sampai'], fn ($q, $d) => $q->whereDate('tanggal_kirim', '<=', $d))
                    ),
                SelectFilter::make('retur_option')
                    ->label('Retur')
                    ->options([
                        'tidak_ada_retur' => 'Tidak Ada Retur',
                        'ada_retur' => 'Ada Retur',
                    ])
                    ->placeholder('Semua'),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'dalam pengiriman' => 'Dalam Pengiriman',
                        'selesai' => 'Selesai',
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
                    ->modalHeading('Detail Kiriman Mobil')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn (Action $action) => $action->label('Tutup'))
                    ->schema([
                        Section::make('Informasi Task')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('id_task')->label('ID Task'),
                                TextEntry::make('cabang')->label('Cabang'),
                                TextEntry::make('total_sj')
                                    ->label('Total SJ')
                                    ->state(fn ($record) => $record->branchShipments->count()),
                                TextEntry::make('tanggal_kirim')->label('Tgl Kirim')->date('d/m/Y'),
                                TextEntry::make('branch_sj_list')
                                     ->label('SJ')
                                     ->badge()
                                     ->color('info')
                                     ->tooltip(fn ($record) => $record->branchShipments->pluck('nomor_sj')->implode(', '))
                                     ->state(function ($record) {
                                         $sj = $record->branchShipments->pluck('nomor_sj');
                                         $result = $sj->take(2)->toArray();
                                         if ($sj->count() > 2) {
                                             $result[] = '+' . ($sj->count() - 2) . ' more';
                                         }
                                         return $result;
                                     }),
                                TextEntry::make('no_plat_mobil')
                                    ->label('No Plat')
                                    ->formatStateUsing(function ($state) {
                                        if (!$state) return '-';
                                        $kendaraan = \App\Models\MasterKendaraan::where('nomor_polisi', $state)->first();
                                        return $kendaraan ? $state . ' - ' . $kendaraan->merek_dan_model : $state;
                                    }),
                                TextEntry::make('jam_muat')->label('Jam Muat'),
                                TextEntry::make('jam_selesai_muat')->label('Jam Selesai'),
                                TextEntry::make('jam_berangkat')->label('Jam Berangkat'),
                                TextEntry::make('jam_tiba')->label('Jam Tiba'),
                                 TextEntry::make('status')->label('Status')->badge()
                                     ->color(fn (string $state): string => match ($state) {
                                         'draft' => 'gray',
                                         'dalam pengiriman' => 'warning',
                                         'selesai' => 'success',
                                         default => 'gray',
                                     }),
                                TextEntry::make('retur_option')->label('Retur')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'tidak_ada_retur' => 'gray',
                                        'ada_retur' => 'warning',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'tidak_ada_retur' => 'Tidak Ada Retur',
                                        'ada_retur' => 'Ada Retur',
                                        default => $state,
                                    }),
                                 TextEntry::make('nama_supir')->label('Supir'),
                                TextEntry::make('keterangan')->label('Keterangan')->columnSpanFull(),
                            ]),
                    ]),
                EditAction::make()
                    ->visible(fn () => auth()->user()?->can('update_task_kiriman_mobils') ?? false)
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning')
                    ->modalWidth(Width::SevenExtraLarge)
                    ->form(TaskKirimanMobilForm::getFormFields())
                    ->using(function ($record, array $data) {
                        $sjs = $data['branch_shipments'] ?? [];
                        unset(
                            $data['branch_shipments'],
                            $data['total_sj_tampil'],
                            $data['sisa_sj_tampil'],
                            $data['durasi_tampil'],
                        );
                        $record->update($data);
                        $record->branchShipments()->sync(filled($sjs) ? $sjs : []);
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
                        'kiriman-mobil',
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
                        'kiriman-mobil',
                        self::exportFormatters(),
                    )),
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->iconButton()
                        ->tooltip('Hapus Data')
                        ->color('danger')
                        ->visible(fn () => auth()->user()?->can('delete_task_kiriman_mobils') ?? false),
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
            'Total SJ' => 'total_sj',
            'SJ' => 'branch_sj_list',
            'Tgl Kirim' => 'tanggal_kirim',
            'No Plat' => 'no_plat_mobil',
            'Jam Muat' => 'jam_muat',
            'Jam Selesai' => 'jam_selesai_muat',
            'Berangkat' => 'jam_berangkat',
            'Tiba' => 'jam_tiba',
            'Status' => 'status',
            'Retur' => 'retur_option',
            'Supir' => 'nama_supir',
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
            'total_sj' => fn ($record) => $record->branchShipments->count(),
            'branch_sj_list' => fn ($record) => $record->branchShipments->pluck('nomor_sj')->implode(', '),
            'status' => fn ($record) => match ($record->status) {
                'draft' => 'Draft',
                'dalam pengiriman' => 'Dalam Pengiriman',
                'selesai' => 'Selesai',
                default => $record->status ?? '',
            },
            'retur_option' => fn ($record) => match ($record->retur_option) {
                'tidak_ada_retur' => 'Tidak Ada Retur',
                'ada_retur' => 'Ada Retur',
                default => $record->retur_option ?? '',
            },
        ];
    }
}
