<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => auth()->user()?->isSuperAdmin() ?? false)
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning')
                    ->modalHeading('Ubah User')
                    ->modalDescription('Perbarui identitas dan hak akses.')
                    ->modalWidth(Width::Full)
                    ->using(function (Model $record, array $data): void {
                        $record->update($data);

                        $record->syncRoles(array_values(array_filter([$data['roles'] ?? null])));

                        $permissions = [];
                        foreach ($data as $key => $value) {
                            if (str_starts_with($key, 'perm_') && $value) {
                                $permissions[] = str_replace('perm_', '', $key);
                            }
                        }
                        $record->syncPermissions($permissions);
                    })
                    ->mutateRecordDataUsing(fn (User $record): array => [
                        'name' => $record->name,
                        'email' => $record->email,
                        'roles' => $record->roles->pluck('id')->first(),
                    ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->iconButton()
                        ->tooltip('Hapus Data')
                        ->color('danger'),
                ]),
            ]);
    }
}