<?php

namespace App\Filament\Resources\Roles\Tables;

use App\Services\ActivityLogger;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

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
                    ->visible(fn () => auth()->user()?->isSuperAdmin() ?? false)
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning')
                    ->modalHeading('Ubah Role')
                    ->modalDescription('Perbarui nama / template permission role.')
                    ->modalWidth(Width::Full)
                    ->using(function (Model $record, array $data): void {
                        $oldName = $record->name;
                        $oldSuperAdmin = (bool) $record->is_super_admin;
                        $oldPermissionCount = count(json_decode((string) $record->permission_template, true) ?? []);

                        $record->update($data);

                        $permissions = [];
                        foreach ($data as $key => $value) {
                            if (str_starts_with($key, 'perm_') && $value) {
                                $permissions[] = str_replace('perm_', '', $key);
                            }
                        }

                        $record->is_super_admin = (bool) ($data['is_super_admin'] ?? false);
                        $record->permission_template = json_encode(array_values(array_unique($permissions)));
                        $record->save();

                        $changes = [];
                        if ($oldName !== $record->name) {
                            $changes[] = 'Nama: ' . $oldName . ' → ' . $record->name;
                        }
                        if ($oldSuperAdmin !== (bool) $record->is_super_admin) {
                            $changes[] = 'Super Admin: ' . ($oldSuperAdmin ? 'Ya' : 'Tidak') . ' → ' . ((bool) $record->is_super_admin ? 'Ya' : 'Tidak');
                        }
                        $newSuperPermissionCount = count(json_decode((string) $record->permission_template, true) ?? []);
                        if ($oldPermissionCount !== $newSuperPermissionCount) {
                            $changes[] = 'Jml Permission: ' . $oldPermissionCount . ' → ' . $newSuperPermissionCount;
                        }

                        ActivityLogger::log($record, 'roles', 'update', 'Role: ' . $record->name . ($changes ? '; ' . implode('; ', $changes) : ''), $record->name);
                    })
                    ->mutateRecordDataUsing(fn (Role $record): array => [
                        'name' => $record->name,
                        'is_super_admin' => $record->is_super_admin,
                    ]),
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
                        ActivityLogger::log($record, 'roles', 'delete', 'Role: ' . $record->name, $record->name);
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
