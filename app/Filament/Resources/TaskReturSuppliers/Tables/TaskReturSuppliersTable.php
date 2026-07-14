<?php

namespace App\Filament\Resources\TaskReturSuppliers\Tables;

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

class TaskReturSuppliersTable
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
                TextColumn::make('no_plat_mobil')
                    ->label('No Plat')
                    ->searchable()
                    ->width('130px')
                    ->grow(false),
                TextColumn::make('nama_sopir')
                    ->label('Sopir')
                    ->searchable()
                    ->grow(false),
                TextColumn::make('jam_muat')
                    ->label('Jam Muat')
                    ->time('H:i')
                    ->sortable()
                    ->grow(false),
                TextColumn::make('jumlah_kolian')
                    ->label('Kolian')
                    ->numeric()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('admin_sj_retur')
                    ->label('Admin SJ')
                    ->searchable()
                    ->grow(false),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'servis' => 'warning',
                        'tukar' => 'info',
                        'pot_nota' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'servis' => 'Servis',
                        'tukar' => 'Tukar',
                        'pot_nota' => 'Pot Nota',
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
                    ->color('info')
                    ->modalHeading('Detail Retur Supplier')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn (Action $action) => $action->label('Tutup'))
                    ->schema([
                        Section::make('Informasi Task')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('id_task')->label('ID Task'),
                                TextEntry::make('nama_supplier_ekspedisi')->label('Supplier / Ekspedisi'),
                                TextEntry::make('no_plat_mobil')->label('No Plat'),
                                TextEntry::make('nama_sopir')->label('Sopir'),
                                TextEntry::make('jam_muat')->label('Jam Muat'),
                                TextEntry::make('jumlah_kolian')->label('Kolian'),
                                TextEntry::make('admin_sj_retur')->label('Admin SJ'),
                                TextEntry::make('status')->label('Status')->badge(),
                                TextEntry::make('keterangan')->label('Keterangan')->columnSpanFull(),
                            ]),
                    ]),
                EditAction::make()
                    ->color('warning'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->color('danger')
                        ->visible(fn () => auth()->user()?->hasRole('Admin') ?? false),
                ]),
            ]);
    }
}
