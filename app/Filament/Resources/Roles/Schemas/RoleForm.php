<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Filament\Support\PermissionMenu;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        $granted = fn ($record) => $record
            ? (json_decode((string) $record->permission_template, true) ?? [])
            : [];

        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Informasi')
                            ->schema([
                                Section::make('Detail Role')
                                    ->description('Nama role dan status super admin')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nama Role')
                                            ->required()
                                            ->unique(ignoreRecord: true),
                                        Toggle::make('is_super_admin')
                                            ->label('Super Admin (akses penuh otomatis)')
                                            ->helperText('Super admin otomatis punya akses 100% tanpa harus centang permission.'),
                                    ]),
                            ]),
                        Tab::make('Detail Template')
                            ->schema([
                                PermissionMenu::globalSection($granted),
                                ...PermissionMenu::menuSections($granted),
                                ...PermissionMenu::widgetsSections($granted),
                            ]),
                    ]),
            ]);
    }
}