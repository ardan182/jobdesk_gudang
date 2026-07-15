<?php

namespace App\Filament\Resources\MasterTokos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MasterTokosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('nama_toko')
                    ->label('Nama Toko')
                    ->searchable()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('alamat')
                    ->label('Alamat')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d/m/Y H:i')
                    ->sortable()
                    ->grow(false),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning')
                    ->modalHeading('Edit Master Toko')
                    ->modalWidth('lg'),
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
