<?php

namespace App\Filament\Resources\TaskReturCabangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class TaskReturCabangsTable
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
                TextColumn::make('cabang')
                    ->label('Cabang')
                    ->searchable(),
                TextColumn::make('jenis_retur')
                    ->label('Jenis Retur')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'retur_jelek' => 'danger',
                        'retur_bagus' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'retur_jelek' => 'Retur Jelek',
                        'retur_bagus' => 'Retur Bagus',
                        default => $state,
                    }),
                TextColumn::make('no_sj_retur')
                    ->label('No SJ')
                    ->searchable(),
                TextColumn::make('total_kolian')
                    ->label('Total Kolian')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jam_bongkar')
                    ->label('Jam Bongkar')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('nama_sopir')
                    ->label('Sopir')
                    ->searchable(),
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
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasRole('Admin') ?? false),
                ]),
            ]);
    }
}
