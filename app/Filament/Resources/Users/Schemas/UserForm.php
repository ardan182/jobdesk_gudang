<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Filament\Support\PermissionMenu;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                    ->dehydrated(fn ($state) => filled($state)),
                Select::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->options(fn () => Role::pluck('name', 'id'))
                    ->helperText('Pilih role — permission dari template role akan terisi otomatis, lalu bisa disesuaikan per-user.')
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        if (!$state) return;
                        $role = Role::find($state);
                        $template = $role ? (json_decode((string) $role->permission_template, true) ?? []) : [];

                        foreach (Permission::all() as $perm) {
                            $set("perm_{$perm->name}", in_array($perm->name, $template));
                        }

                        foreach (PermissionMenu::groups() as $group => $modules) {
                            foreach ($modules as $m) {
                                $key = $m['key'];
                                $set("select_all_{$key}", $this->moduleAllChecked($key, $template));
                            }
                        }
                    }),
                ...PermissionMenu::buildTree(
                    fn ($record) => $record
                        ? $record->getDirectPermissions()->pluck('name')->all()
                        : []
                ),
            ]);
    }

    protected static function moduleAllChecked(string $key, array $template): bool
    {
        return in_array("view_{$key}", $template)
            && in_array("create_{$key}", $template)
            && in_array("update_{$key}", $template)
            && in_array("delete_{$key}", $template);
    }
}
