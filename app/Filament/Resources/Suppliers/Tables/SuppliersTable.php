<?php

namespace App\Filament\Resources\Suppliers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordAction('view')
            ->columns([
                TextColumn::make('kode_supplier')
                    ->label('Kode Supplier')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('nama_supplier')
                    ->label('Nama Supplier')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->width('160px')
                    ->grow(false),
                TextColumn::make('no_telepon')
                    ->label('No Telepon')
                    ->icon('heroicon-o-phone')
                    ->iconColor('success')
                    ->url(fn ($record) => $record->no_telepon
                        ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->no_telepon)
                        : null)
                    ->openUrlInNewTab()
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
                TextColumn::make('alamat')
                    ->label('Alamat')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->grow(false),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail')
                    ->color('info')
                    ->modalHeading('Detail Supplier')
                    ->modalWidth('lg')
                    ->infolist([
                        TextEntry::make('kode_supplier')->label('Kode Supplier'),
                        TextEntry::make('nama_supplier')->label('Nama Supplier'),
                        TextEntry::make('no_telepon')
                            ->label('No Telepon')
                            ->url(fn ($record) => $record->no_telepon
                                ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->no_telepon)
                                : null)
                            ->openUrlInNewTab()
                            ->icon('heroicon-o-phone')
                            ->iconColor('success'),
                        TextEntry::make('alamat')->label('Alamat'),
                        TextEntry::make('keterangan')->label('Keterangan'),
                    ]),
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning')
                    ->modalHeading('Edit Supplier')
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
