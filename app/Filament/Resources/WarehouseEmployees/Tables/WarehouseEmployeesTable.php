<?php

namespace App\Filament\Resources\WarehouseEmployees\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WarehouseEmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('nama_karyawan')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('no_whatsapp')
                    ->label('No WhatsApp')
                    ->icon('heroicon-o-phone')
                    ->iconColor('success')
                    ->url(fn ($record) => $record->no_whatsapp
                        ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->no_whatsapp)
                        : null)
                    ->openUrlInNewTab()
                    ->grow(false),
                TextColumn::make('division.nama_divisi')
                    ->label('Divisi')
                    ->badge()
                    ->color('gray')
                    ->grow(false),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d/m/Y H:i')
                    ->sortable()
                    ->grow(false),
            ])
            ->filters([
                SelectFilter::make('division_id')
                    ->label('Divisi')
                    ->relationship('division', 'nama_divisi'),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning')
                    ->modalHeading('Edit Master Employee Gudang')
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
