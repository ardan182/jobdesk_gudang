<?php

namespace App\Filament\Resources\Roles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Role')
                    ->searchable()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('is_super_admin')
                    ->label('Super Admin')
                    ->badge()
                    ->color(fn ($record): string => $record->is_super_admin ? 'success' : 'gray')
                    ->formatStateUsing(fn ($record): string => $record->is_super_admin ? 'Ya' : 'Tidak')
                    ->grow(false),
                TextColumn::make('permission_count')
                    ->label('Jml Permission (Template)')
                    ->getStateUsing(fn ($record) => count(json_decode((string) $record->permission_template, true) ?? []))
                    ->grow(false),
                TextColumn::make('users_count')
                    ->label('Jml User')
                    ->counts('users')
                    ->grow(false),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning'),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Hapus Role')
                    ->color('danger')
                    ->visible(fn ($record) => !$record->is_super_admin)
                    ->action(function ($record) {
                        if ($record->is_super_admin) {
                            Notification::make()
                                ->title('Tidak bisa dihapus')
                                ->body('Role Super Admin tidak bisa dihapus.')
                                ->danger()
                                ->send();
                            return;
                        }
                        $record->delete();
                        Notification::make()->title('Role berhasil dihapus')->success()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->iconButton()
                        ->tooltip('Hapus Data')
                        ->color('danger')
                        ->visible(fn () => false),
                ]),
            ]);
    }
}
