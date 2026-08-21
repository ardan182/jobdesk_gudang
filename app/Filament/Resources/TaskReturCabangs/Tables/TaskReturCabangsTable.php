<?php

namespace App\Filament\Resources\TaskReturCabangs\Tables;

use App\Filament\Resources\TaskReturCabangs\Schemas\TaskReturCabangForm;
use App\Services\TableExportService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class TaskReturCabangsTable
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
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->grow(false),
                TextColumn::make('cabang')
                    ->label('Toko')
                    ->searchable()
                    ->width('130px')
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('no_plat_mobil')
                    ->label('No Plat')
                    ->width('120px')
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jam_tiba')
                    ->label('Jam Tiba')
                    ->time('H:i')
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jenis_retur')
                    ->label('Jenis Retur')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'retur_jelek' => 'danger',
                        'retur_bagus' => 'success',
                        'rb_dan_rj' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'retur_jelek' => 'Retur Jelek',
                        'retur_bagus' => 'Retur Bagus',
                        'rb_dan_rj' => 'RB dan RJ',
                        default => $state,
                    })
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('tanggal_bongkar')
                    ->label('Tgl Bongkar')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jam_bongkar')
                    ->label('Jam Bongkar')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jumlah_sj_bagus')
                    ->label('SJ Bagus')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jumlah_sj_jelek')
                    ->label('SJ Jelek')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('nama_sopir')
                    ->label('Sopir')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('helpers_list')
                    ->label('Helper')
                    ->badge()
                    ->color('info')
                    ->tooltip(fn ($record) => $record->helpers->pluck('nama_karyawan')->implode(', '))
                    ->getStateUsing(function ($record) {
                        $names = $record->helpers->pluck('nama_karyawan');
                        $result = $names->take(2)->toArray();
                        if ($names->count() > 2) {
                            $result[] = '+' . ($names->count() - 2) . ' more';
                        }
                        return $result;
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
                    ->label('Toko')
                    ->options(fn () => \App\Models\TaskKirimanMobil::whereIn('retur_option', ['ada_retur'])
                        ->pluck('cabang', 'cabang')->unique())
                    ->searchable()
                    ->placeholder('Semua Toko'),
                SelectFilter::make('jenis_retur')
                    ->label('Jenis Retur')
                    ->options([
                        'retur_bagus' => 'Retur Bagus',
                        'retur_jelek' => 'Retur Jelek',
                        'rb_dan_rj' => 'RB dan RJ',
                    ])
                    ->placeholder('Semua'),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'selesai' => 'Selesai',
                    ])
                    ->placeholder('Semua Status'),
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
                    ->modalHeading('Detail Retur Cabang')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn (Action $action) => $action->label('Tutup'))
                    ->schema([
                        Section::make('Informasi Task')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('id_task')->label('ID Task'),
                                TextEntry::make('cabang')->label('Toko'),
                                TextEntry::make('no_plat_mobil')->label('No Plat'),
                                TextEntry::make('jam_tiba')->label('Jam Tiba'),
                                TextEntry::make('jenis_retur')->label('Jenis Retur')->badge(),
                                TextEntry::make('tanggal_bongkar')->label('Tanggal Bongkar'),
                                TextEntry::make('jam_bongkar')->label('Jam Bongkar'),
                                TextEntry::make('jumlah_sj_bagus')->label('SJ Bagus'),
                                TextEntry::make('catatan_bagus')->label('Catatan Bagus'),
                                TextEntry::make('jumlah_sj_jelek')->label('SJ Jelek'),
                                TextEntry::make('catatan_jelek')->label('Catatan Jelek'),
                                TextEntry::make('nama_sopir')->label('Sopir'),
                                TextEntry::make('helpers_list')
                                    ->label('Helper')
                                    ->badge()
                                    ->color('info')
                                    ->separator(', ')
                                    ->state(fn ($record) => $record->helpers->pluck('nama_karyawan')->toArray()),
                                TextEntry::make('status')->label('Status')->badge(),
                                TextEntry::make('keterangan')->label('Keterangan')->columnSpanFull(),
                            ]),
                    ]),
                EditAction::make()
                    ->visible(fn () => auth()->user()?->can('update_task_retur_cabangs') ?? false)
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning')
                    ->modalWidth(Width::SevenExtraLarge)
                    ->form(TaskReturCabangForm::getFormFields())
                    ->using(function ($record, array $data) {
                        $helpers = $data['helpers'] ?? [];
                        unset($data['helpers']);
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
                        'retur-masuk-toko',
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
                        'retur-masuk-toko',
                        self::exportFormatters(),
                    )),
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->iconButton()
                        ->tooltip('Hapus Data')
                        ->color('danger')
                        ->visible(fn () => auth()->user()?->can('delete_task_retur_cabangs') ?? false),
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
            'Toko' => 'cabang',
            'No Plat' => 'no_plat_mobil',
            'Jam Tiba' => 'jam_tiba',
            'Jenis Retur' => 'jenis_retur',
            'Tgl Bongkar' => 'tanggal_bongkar',
            'Jam Bongkar' => 'jam_bongkar',
            'SJ Bagus' => 'jumlah_sj_bagus',
            'SJ Jelek' => 'jumlah_sj_jelek',
            'Catatan Retur Bagus' => 'catatan_bagus',
            'Catatan Retur Jelek' => 'catatan_jelek',
            'Sopir' => 'nama_sopir',
            'Helper' => 'helpers',
            'Status' => 'status',
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
            'jenis_retur' => fn ($record) => match ($record->jenis_retur) {
                'retur_bagus' => 'Retur Bagus',
                'retur_jelek' => 'Retur Jelek',
                'rb_dan_rj' => 'RB dan RJ',
                default => $record->jenis_retur ?? '',
            },
            'helpers' => fn ($record) => $record->helpers->pluck('nama_karyawan')->implode(', '),
            'status' => fn ($record) => match ($record->status) {
                'draft' => 'Draft',
                'selesai' => 'Selesai',
                default => $record->status ?? '',
            },
        ];
    }
}
