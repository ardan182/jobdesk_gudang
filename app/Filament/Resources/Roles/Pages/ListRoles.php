<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Services\ActivityLogger;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Spatie\Permission\Models\Role;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => auth()->user()?->isSuperAdmin() ?? false)
                ->label('Tambah Role')
                ->icon('heroicon-m-plus')
                ->modalHeading('Buat Role')
                ->modalDescription('Buat role baru dan atur template permission.')
                ->modalSubmitActionLabel('Buat')
                ->modalWidth(Width::Full)
                ->createAnother(false)
                ->using(function (array $data): Role {
                    $role = Role::create($data);
                    $this->saveRoleExtras($role, $data);

                    ActivityLogger::log($role, 'roles', 'create', 'Role: ' . $role->name, $role->name);

                    return $role;
                }),
        ];
    }

    protected function saveRoleExtras(Role $role, array $data): void
    {
        $permissions = [];
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'perm_') && $value) {
                $permissions[] = str_replace('perm_', '', $key);
            }
        }

        $role->is_super_admin = (bool) ($data['is_super_admin'] ?? false);
        $role->permission_template = json_encode(array_values(array_unique($permissions)));
        $role->save();
    }
}