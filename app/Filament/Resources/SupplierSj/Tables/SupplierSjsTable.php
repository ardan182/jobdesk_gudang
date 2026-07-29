<?php

namespace App\Filament\Resources\SupplierSj\Tables;

use App\Filament\Resources\SupplierSj\Schemas\SupplierSjForm;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SupplierSjsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(50)
            ->defaultSort('created_at', 'desc')
            ->description('Data dibuat otomatis dari modul Terima mobil Supplier.')
            ->recordAction('view')
            ->columns([
                TextColumn::make('id_task')
                    ->label('ID Task')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('nama_supplier')
                    ->label('Nama Supplier')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('tanggal_datang')
                    ->label('Tgl Datang')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('nomor_po_referensi')
                    ->label('No PO')
                    ->searchable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jumlah_koli')
                    ->label('Koli')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('jumlah_faktur')
                    ->label('Faktur')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('status_input')
                    ->label('Status')
                    ->badge()
                    ->toggleable()
                    ->color(fn (string $state): string => match ($state) {
                        'belum_di_cek' => 'gray',
                        'draft' => 'warning',
                        'selesai' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'belum_di_cek' => 'Belum Di Cek',
                        'draft' => 'Draft',
                        'selesai' => 'Selesai',
                        default => $state,
                    })
                    ->grow(false),
                TextColumn::make('tempo')
                    ->label('Tempo')
                    ->badge()
                    ->toggleable()
                    ->grow(false)
                    ->color(fn ($record): string => match ($record->status_input) {
                        'belum_di_cek', 'draft' => 'danger',
                        'selesai' => 'success',
                        default => 'gray',
                    })
                    ->getStateUsing(fn ($record) => $record->tempo_display),
                TextColumn::make('tanggal_input')
                    ->label('Tgl Input')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->grow(false),
            ])
            ->filters([
                SelectFilter::make('nama_supplier')
                    ->label('Nama Supplier')
                    ->options(fn () => \App\Models\SupplierSj::distinct()->pluck('nama_supplier', 'nama_supplier'))
                    ->searchable()
                    ->placeholder('Semua'),
                Filter::make('tanggal_input')
                    ->label('Tgl Input')
                    ->form([
                        Grid::make(2)->schema([
                            DatePicker::make('input_dari')
                                ->label('Dari')
                                ->helperText('tgl mulai'),
                            DatePicker::make('input_sampai')
                                ->label('Sampai')
                                ->helperText('tgl akhir'),
                        ]),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['input_dari'], fn ($q, $d) => $q->whereDate('tanggal_input', '>=', $d))
                        ->when($data['input_sampai'], fn ($q, $d) => $q->whereDate('tanggal_input', '<=', $d))
                    ),
                SelectFilter::make('status_input')
                    ->label('Status')
                    ->options([
                        'belum_di_cek' => 'Belum Di Cek',
                        'draft' => 'Draft',
                        'selesai' => 'Selesai',
                    ])
                    ->placeholder('Semua'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail')
                    ->color('info')
                    ->modalHeading('Detail Input SJ')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn (Action $action) => $action->label('Tutup'))
                    ->schema([
                        Section::make('Informasi Dokumen')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('id_task')->label('ID Task'),
                                TextEntry::make('nama_supplier')->label('Nama Supplier'),
                                TextEntry::make('tanggal_datang')->label('Tgl Datang')->date('d/m/Y'),
                                TextEntry::make('nomor_po_referensi')->label('No PO Referensi'),
                                TextEntry::make('jumlah_koli')->label('Jumlah Koli'),
                                TextEntry::make('jumlah_faktur')->label('Jumlah Faktur'),
                                TextEntry::make('terima_ref')
                                    ->label('Ref Terima Supplier')
                                    ->badge()
                                    ->color('info')
                                    ->getStateUsing(function ($record) {
                                        preg_match('/\bTRM-SUP-\d+\b/', $record->keterangan ?? '', $m);
                                        return $m[0] ?? '-';
                                    }),
                                TextEntry::make('tempo')
                                    ->label('Tempo')
                                    ->badge()
                                    ->color(fn ($record): string => match ($record->status_input) {
                                        'belum_di_cek', 'draft' => 'danger',
                                        'selesai' => 'success',
                                        default => 'gray',
                                    })
                                    ->getStateUsing(fn ($record) => $record->tempo_display),
                                TextEntry::make('status_input')->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'belum_di_cek' => 'gray',
                                        'draft' => 'warning',
                                        'selesai' => 'success',
                                        default => 'gray',
                                    }),
                                TextEntry::make('tanggal_input')->label('Tgl Input')->date('d/m/Y'),
                                TextEntry::make('keterangan')->label('Keterangan')->columnSpanFull(),
                            ]),
                    ]),
                EditAction::make()
                    ->color('warning')
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->modalHeading('Edit Input SJ')
                    ->modalWidth(Width::Full)
                    ->form(SupplierSjForm::getFormFields())
                    ->action(function ($record, array $data) {
                        if (($data['status_input'] ?? null) === 'selesai' && blank($data['nomor_po_referensi'] ?? null)) {
                            Notification::make()
                                ->title('Gagal menyimpan')
                                ->body('No PO Referensi wajib diisi jika status "Selesai".')
                                ->danger()
                                ->send();
                            return;
                        }
                        if (($data['status_input'] ?? null) === 'selesai' && $record->tanggal_datang) {
                            $tanggalInput = $data['tanggal_input'] ?? $record->tanggal_input;
                            $endDate = $tanggalInput ? \Carbon\Carbon::parse($tanggalInput) : now();
                            $data['tempo_hari'] = abs($endDate->diffInDays($record->tanggal_datang));
                        }
                        $record->update($data);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->color('danger'),
                ]),
            ]);
    }
}
