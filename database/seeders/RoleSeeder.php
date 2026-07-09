<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'Admin',
            'Checker Retur',
            'Checker Terima',
            'Checker Keluar',
            'Checker Kiriman',
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
