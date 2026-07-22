<?php

namespace App\Filament\Resources\SupplierSj\Tables;

use App\Filament\Resources\SupplierSj\Schemas\SupplierSjForm;
use App\Filament\Resources\SupplierSj\Pages\ListSupplierSjs as ListSupplierSj;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SupplierSjsTable
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
                    ->grow(false),
                TextColumn::make('nama_supplier')
                    ->label('Nama Supplier')
                    ->searchable()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('tanggal_datang')
                    ->label('Tgl Datang')
                    ->date('d/m/Y')
                    ->sortable()
                    ->grow(false),
                TextColumn::make('nomor_po_referensi')
                    ->label('No PO')
                    ->searchable()
                    ->grow(false),
                TextColumn::make('jumlah_koli')
                    ->label('Koli')
                    ->numeric()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('jumlah_faktur')
                    ->label('Faktur')
                    ->numeric()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('status_input')
                    ->label('Status')
                    ->badge()
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
                    ->grow(false)
                    ->color(fn ($record): string => match ($record->status_input) {
                        'belum_di_cek', 'draft' => 'danger',
                        'selesai' => 'success',
                        default => 'gray',
                    })
                    ->getStateUsing(function ($record) {
                        if (!$record->tanggal_datang) return '-';
                        $days = abs(now()->startOfDay()->diffInDays($record->tanggal_datang));
                        $prefix = in_array($record->status_input, ['belum_di_cek', 'draft']) ? 'blm input' : 'input';
                        return "{$prefix} {$days} hr";
                    }),
                TextColumn::make('tanggal_input')
                    ->label('Tgl Input')
                    ->date('d/m/Y')
                    ->sortable()
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
            ->filters([])
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
                                    ->getStateUsing(function ($record) {
                                        if (!$record->tanggal_datang) return '-';
                                        $days = abs(now()->startOfDay()->diffInDays($record->tanggal_datang));
                                        $prefix = in_array($record->status_input, ['belum_di_cek', 'draft']) ? 'blm input' : 'input';
                                        return "{$prefix} {$days} hr";
                                    }),
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
                        $record->update($data);
                        if (filled($data['nomor_po_referensi'] ?? null)) {
                            ListSupplierSj::syncPoToTerimaSupplier($record->fresh());
                        }
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
