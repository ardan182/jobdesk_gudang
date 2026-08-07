<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Filament\Support\PermissionMenu;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Role')
                    ->required()
                    ->unique(ignoreRecord: true),
                Toggle::make('is_super_admin')
                    ->label('Super Admin (akses penuh otomatis)')
                    ->helperText('Super admin otomatis punya akses 100% tanpa harus centang permission.'),
                ...PermissionMenu::buildTree(
                    fn ($record) => $record
                        ? (json_decode((string) $record->permission_template, true) ?? [])
                        : []
                ),
            ]);
    }
}
