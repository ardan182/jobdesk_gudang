<?php

namespace App\Filament\Resources\KomplainPo\Tables;

use App\Filament\Resources\KomplainPo\Schemas\KomplainPoForm;
use App\Services\TableExportService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KomplainPosTable
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
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('supplier.nama_supplier')
                    ->label('Supplier')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('no_po')
                    ->label('No PO')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('nama_barang')
                    ->label('Barang')
                    ->searchable()
                    ->limit(20)
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('qty_diterima')
                    ->label('Qty Terima')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('kondisi_barang')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'tidak_sesuai' => 'danger',
                        'tidak_lengkap' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'tidak_sesuai' => 'Tidak Sesuai',
                        'tidak_lengkap' => 'Tidak Lengkap',
                        default => $state,
                    })
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('penyelesaian')
                    ->label('Penyelesaian')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'potong_nota' => 'info',
                        'retur' => 'warning',
                        'ganti_barang' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'potong_nota' => 'Potong Nota',
                        'retur' => 'Retur',
                        'ganti_barang' => 'Ganti Barang',
                        default => $state,
                    })
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
                TextColumn::make('tanggal_datang_barang')
                    ->label('Tgl Datang')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('foto')
                    ->label('Foto')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($record) => count($record->foto ?? []) . ' Gambar')
                    ->tooltip(fn ($record) => implode("\n", array_map(fn ($f) => basename($f), $record->foto ?? [])))
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('user.name')
                    ->label('Dibuat')
                    ->searchable()
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
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'selesai' => 'Selesai',
                    ])
                    ->placeholder('Semua Status'),
                SelectFilter::make('penyelesaian')
                    ->label('Penyelesaian')
                    ->options([
                        'potong_nota' => 'Potong Nota',
                        'retur' => 'Retur',
                        'ganti_barang' => 'Ganti Barang',
                    ])
                    ->placeholder('Semua Penyelesaian'),
                Filter::make('created_at')
                    ->label('Tanggal')
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
            ->filtersFormColumns(4)
            ->recordAction('view')
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail')
                    ->color('info')
                    ->modalHeading('Detail Komplain PO')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn (Action $action) => $action->label('Tutup'))
                    ->schema([
                        Section::make('Informasi Komplain')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('id_task')->label('ID Task'),
                                TextEntry::make('cabang')->label('Cabang'),
                                TextEntry::make('supplier.nama_supplier')->label('Supplier'),
                                TextEntry::make('no_po')->label('No PO'),
                                TextEntry::make('barcode')->label('Barcode / CodeItem'),
                                TextEntry::make('nama_barang')->label('Nama Barang'),
                                TextEntry::make('qty_diterima')->label('Qty Diterima'),
                                TextEntry::make('no_surat_jalan')->label('No Surat Jalan'),
                                TextEntry::make('qty_disurat_jalan')->label('Qty di Surat Jalan'),
                                TextEntry::make('tanggal_datang_barang')->label('Tgl Datang')->date('d/m/Y'),
                                TextEntry::make('kondisi_barang')->label('Kondisi')->badge(),
                                TextEntry::make('penyelesaian')->label('Penyelesaian')->badge(),
                                TextEntry::make('status')->label('Status')->badge(),
                                ImageEntry::make('foto')
                                    ->label('Foto Barang')
                                    ->disk('public')
                                    ->height(200)
                                    ->columnSpanFull(),
                                TextEntry::make('keterangan')->label('Keterangan')->columnSpanFull(),
                            ]),
                    ]),
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning')
                    ->modalWidth(Width::Full)
                    ->form(KomplainPoForm::getFormFields()),
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
                        'komplain-po',
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
                        'komplain-po',
                        self::exportFormatters(),
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
            'Cabang' => 'cabang',
            'Supplier' => 'supplier.nama_supplier',
            'No PO' => 'no_po',
            'Barcode' => 'barcode',
            'Nama Barang' => 'nama_barang',
            'Qty Diterima' => 'qty_diterima',
            'No Surat Jalan' => 'no_surat_jalan',
            'Qty di Surat Jalan' => 'qty_disurat_jalan',
            'Tgl Datang' => 'tanggal_datang_barang',
            'Kondisi' => 'kondisi_barang',
            'Penyelesaian' => 'penyelesaian',
            'Status' => 'status',
            'Keterangan' => 'keterangan',
        ];
    }

    /**
     * @return array<string, callable>
     */
    public static function exportFormatters(): array
    {
        return [
            'kondisi_barang' => fn ($record) => match ($record->kondisi_barang) {
                'tidak_sesuai' => 'Tidak Sesuai',
                'tidak_lengkap' => 'Tidak Lengkap',
                default => $record->kondisi_barang ?? '',
            },
            'penyelesaian' => fn ($record) => match ($record->penyelesaian) {
                'potong_nota' => 'Potong Nota',
                'retur' => 'Retur ke Gudang',
                'ganti_barang' => 'Ganti Barang',
                default => $record->penyelesaian ?? '',
            },
            'status' => fn ($record) => match ($record->status) {
                'draft' => 'Draft',
                'selesai' => 'Selesai',
                default => $record->status ?? '',
            },
        ];
    }
}
