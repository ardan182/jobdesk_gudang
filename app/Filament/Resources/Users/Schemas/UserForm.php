<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Filament\Support\PermissionMenu;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        $granted = fn ($record) => $record
            ? $record->getDirectPermissions()->pluck('name')->all()
            : [];

        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Informasi')
                            ->schema([
                                Section::make('Identitas Akun')
                                    ->description('Identitas akun dan role pengguna')
                                    ->columns(4)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Name')
                                            ->required(),
                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true),
                                        TextInput::make('password')
                                            ->label('Password')
                                            ->password()
                                            ->helperText('Kosongkan saat edit jika tidak ingin mengubah password.')
                                            ->required(fn ($component) => blank($component->getRecord()))
                                            ->dehydrated(fn ($state) => filled($state)),
                                        Select::make('roles')
                                            ->label('Role')
                                            ->relationship('roles', 'name')
                                            ->options(fn () => Role::pluck('name', 'id'))
                                            ->helperText('Permission dari template role terisi otomatis, bisa disesuaikan per-user.')
                                            ->searchable()
                                            ->preload()
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
                                                        $set("select_all_{$key}", self::moduleAllChecked($key, $template));
                                                    }
                                                }
                                            }),
                                    ]),
                            ]),
                        Tab::make('Akses Menu & Fitur')
                            ->schema([
                                PermissionMenu::globalSection($granted),
                                ...PermissionMenu::menuSections($granted),
                            ]),
                        Tab::make('Dashboard & Widgets')
                            ->schema(PermissionMenu::widgetsSections($granted)),
                    ]),
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