<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $userClass = 'App\\Models\\User';

        // 1. Simpan permission role → roles.permission_template (template, bukan pemberi akses)
        $rolePerms = DB::table('role_has_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->get(['role_has_permissions.role_id', 'permissions.name']);

        foreach ($rolePerms->groupBy('role_id') as $roleId => $rows) {
            $names = $rows->pluck('name')->sort()->values()->all();
            DB::table('roles')->where('id', $roleId)->update([
                'permission_template' => json_encode($names),
            ]);
        }

        // 2. Role Admin → super admin (bypass implicit)
        DB::table('roles')->where('name', 'Admin')->update(['is_super_admin' => true]);

        // 3. Salin permission role → direct permission tiap user (agar akses lama tetap)
        $userRoles = DB::table('model_has_roles')
            ->where('model_type', $userClass)
            ->get(['model_id', 'role_id']);

        $existingKeys = DB::table('model_has_permissions')
            ->where('model_type', $userClass)
            ->get(['model_id', 'permission_id'])
            ->mapWithKeys(fn ($r) => ["{$r->model_id}-{$r->permission_id}" => true])
            ->all();

        $newRows = [];
        foreach ($userRoles as $ur) {
            $permissionIds = DB::table('role_has_permissions')
                ->where('role_id', $ur->role_id)
                ->pluck('permission_id');

            foreach ($permissionIds as $pid) {
                $key = "{$ur->model_id}-{$pid}";
                if (isset($existingKeys[$key])) {
                    continue;
                }
                $existingKeys[$key] = true;
                $newRows[] = [
                    'permission_id' => $pid,
                    'model_type' => $userClass,
                    'model_id' => $ur->model_id,
                ];
            }
        }

        if (!empty($newRows)) {
            DB::table('model_has_permissions')->insert($newRows);
        }

        // 4. Role berhenti mewariskan akses — akses = direct permission user
        DB::table('role_has_permissions')->truncate();
    }

    public function down(): void
    {
        // Best-effort: kembalikan role_has_permissions dari permission_template
        $roles = DB::table('roles')->whereNotNull('permission_template')->get(['id', 'permission_template']);
        foreach ($roles as $role) {
            $names = json_decode($role->permission_template, true) ?? [];
            if (empty($names)) {
                continue;
            }
            $ids = DB::table('permissions')->whereIn('name', $names)->pluck('id');
            foreach ($ids as $pid) {
                DB::table('role_has_permissions')->insertOrIgnore([
                    'permission_id' => $pid,
                    'role_id' => $role->id,
                ]);
            }
        }
    }
};
