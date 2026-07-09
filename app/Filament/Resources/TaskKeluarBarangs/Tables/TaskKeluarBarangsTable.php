<?php

namespace App\Filament\Resources\TaskKeluarBarangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class TaskKeluarBarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_task')
                    ->label('ID Task')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('no_baris')
                    ->label('No')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('toko_tujuan')
                    ->label('Toko Tujuan')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pusat' => 'Pusat',
                        'ujungberung' => 'Ujungberung',
                        'soreang' => 'Soreang',
                        'majalaya' => 'Majalaya',
                        'cicaheum' => 'Cicaheum',
                        'barokah' => 'Barokah',
                        default => $state,
                    }),
                TextColumn::make('supplier')
                    ->label('Supplier')
                    ->searchable(),
                TextColumn::make('no_referensi_sj')
                    ->label('No Referensi SJ')
                    ->searchable(),
                TextColumn::make('jumlah_kolian')
                    ->label('Kolian')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jam_naik')
                    ->label('Jam Naik')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('nama_koordinator')
                    ->label('Koordinator')
                    ->searchable(),
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
                    }),
                TextColumn::make('user.name')
                    ->label('Checker')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()?->hasRole('Admin') ?? false),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Dari Tanggal')
                            ->default(now()->format('Y-m-d')),
                        DatePicker::make('created_until')
                            ->label('Sampai Tanggal')
                            ->default(now()->format('Y-m-d')),
                    ])
                    ->default([
                        'created_from' => now()->format('Y-m-d'),
                        'created_until' => now()->format('Y-m-d'),
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
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
