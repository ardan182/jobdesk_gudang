<?php

namespace App\Filament\Resources\Expeditions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpeditionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordAction('view')
            ->columns([
                TextColumn::make('nama_ekspedisi')
                    ->label('Nama Ekspedisi')
                    ->searchable()
                    ->sortable()
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
                    ->grow(false),
                TextColumn::make('alamat')
                    ->label('Alamat')
                    ->limit(50),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d/m/Y H:i')
                    ->sortable()
                    ->grow(false),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail')
                    ->color('info')
                    ->modalHeading('Detail Ekspedisi')
                    ->modalWidth('lg')
                    ->infolist([
                        TextEntry::make('nama_ekspedisi')->label('Nama Ekspedisi'),
                        TextEntry::make('no_telepon')
                            ->label('No Telepon')
                            ->url(fn ($record) => $record->no_telepon
                                ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->no_telepon)
                                : null)
                            ->openUrlInNewTab()
                            ->icon('heroicon-o-phone')
                            ->iconColor('success'),
                        TextEntry::make('alamat')->label('Alamat'),
                    ]),
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning')
                    ->modalHeading('Edit Ekspedisi')
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
