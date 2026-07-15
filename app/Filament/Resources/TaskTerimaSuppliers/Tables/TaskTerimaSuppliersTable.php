<?php

namespace App\Filament\Resources\TaskTerimaSuppliers\Tables;

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
                        'komplit' => 'success',
                        'kurang' => 'danger',
                        'lebih' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'komplit' => 'Komplit',
                        'kurang' => 'Kurang',
                        'lebih' => 'Lebih',
                        default => $state,
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
                                TextEntry::make('nama_supplier_ekspedisi')->label('Supplier / Ekspedisi'),
                                TextEntry::make('no_po_referensi')->label('No PO Referensi'),
                                TextEntry::make('jam_datang')->label('Jam Datang'),
                                TextEntry::make('jumlah_kolian')->label('Kolian'),
                                TextEntry::make('jam_bongkar')->label('Jam Bongkar'),
                                TextEntry::make('selesai_bongkar')->label('Selesai Bongkar'),
                                TextEntry::make('lembar_sj')->label('Lembar SJ'),
                                TextEntry::make('nama_sopir')->label('Sopir'),
                                TextEntry::make('status')->label('Status')->badge(),
                                TextEntry::make('keterangan')->label('Keterangan')->columnSpanFull(),
                            ]),
                    ]),
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning'),
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
