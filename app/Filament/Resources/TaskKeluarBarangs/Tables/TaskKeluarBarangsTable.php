<?php

namespace App\Filament\Resources\TaskKeluarBarangs\Tables;

use App\Filament\Resources\TaskKeluarBarangs\Schemas\TaskKeluarBarangForm;
use App\Models\WarehouseEmployee;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
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
                    ->grow(false),
                TextColumn::make('cabang')
                    ->label('Cabang')
                    ->searchable()
                    ->width('120px')
                    ->grow(false),
                TextColumn::make('nomor_sj')
                    ->label('No SJ')
                    ->searchable()
                    ->width('130px')
                    ->grow(false),
                TextColumn::make('total_qty')
                    ->label('Qty')
                    ->numeric()
                    ->sortable()
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
                    ->grow(false),
                TextColumn::make('diserahkan_kepada')
                    ->label('Diserahkan')
                    ->searchable()
                    ->grow(false),
                TextColumn::make('helper')
                    ->label('Helper')
                    ->badge()
                    ->color('success')
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
                    ->grow(false),
                TextColumn::make('user.name')
                    ->label('Checker')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()?->hasRole('Admin') ?? false)
                    ->grow(false),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->grow(false),
            ])
            ->filters([
                Filter::make('status')
                    ->label('Status')
                    ->form([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'siap kirim' => 'Siap Kirim',
                                'selesai' => 'Selesai',
                            ])
                            ->placeholder('Semua Status'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['status'] ?? null,
                            fn (Builder $query, $status): Builder => $query->where('status', $status),
                        );
                    }),
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
                    }),
            ])
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
                                TextEntry::make('total_qty')->label('Total Qty'),
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
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning')
                    ->modalWidth(Width::Full)
                    ->form(TaskKeluarBarangForm::getFormFields()),
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
