<?php

namespace App\Filament\Resources\MasterSopirs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MasterSopirsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordUrl(null)
            ->columns([
                TextColumn::make('nama_sopir')
                    ->label('Nama Sopir')
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
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->grow(false),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail')
                    ->color('info')
                    ->modalHeading('Detail Sopir')
                    ->modalWidth('lg')
                    ->infolist([
                        \Filament\Infolists\Components\TextEntry::make('nama_sopir')->label('Nama Sopir'),
                        \Filament\Infolists\Components\TextEntry::make('no_whatsapp')
                            ->label('No WhatsApp')
                            ->url(fn ($record) => $record->no_whatsapp
                                ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->no_whatsapp)
                                : null)
                            ->openUrlInNewTab()
                            ->icon('heroicon-o-phone')
                            ->iconColor('success'),
                    ]),
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning')
                    ->modalHeading('Edit Master Sopir')
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
