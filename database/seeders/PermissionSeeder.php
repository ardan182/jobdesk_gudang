<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = [
            // Master
            'master_suppliers',
            'master_tokos',
            'master_kendaraans',
            'master_sopirs',
            'expeditions',
            'warehouse_employees',
            // Purchasing Order
            'komplain_pos',
            // Retur
            'supplier_returns',
            'task_retur_cabangs',
            // Penerimaan
            'task_datang_mobil_suppliers',
            'task_terima_suppliers',
            'supplier_sjs',
            // Pengiriman
            'branch_shipments',
            'task_keluar_barangs',
            'task_kiriman_mobils',
            // Administrasi
            'warehouse_documents',
            'kendaraan_dokumens',
            'cuti_absensi',
            // Pengaturan
            'users',
            'board_tv_settings',
        ];

        $actions = ['view', 'create', 'update', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$action}_{$module}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // Widgets (view-only)
        $widgets = [
            'view_widget_stats_overview',
            'view_widget_recent_activity',
            'view_widget_expiring_documents',
            'view_widget_leaves_today',
        ];

        foreach ($widgets as $widget) {
            Permission::firstOrCreate([
                'name' => $widget,
                'guard_name' => 'web',
            ]);
        }

        // Akses global — lihat semua data lintas modul
        Permission::firstOrCreate([
            'name' => 'view_all_data',
            'guard_name' => 'web',
        ]);

        $this->setRoleDefaults();
    }

    protected function setRoleDefaults(): void
    {
        $all = Permission::all()->pluck('name')->sort()->values()->all();

        $defaults = [
            'Admin' => $all,
            'Checker Terima' => [
                'view_task_datang_mobil_suppliers', 'create_task_datang_mobil_suppliers', 'update_task_datang_mobil_suppliers', 'delete_task_datang_mobil_suppliers',
                'view_task_terima_suppliers', 'create_task_terima_suppliers', 'update_task_terima_suppliers', 'delete_task_terima_suppliers',
                'view_supplier_sjs', 'create_supplier_sjs', 'update_supplier_sjs', 'delete_supplier_sjs',
                'view_komplain_pos', 'create_komplain_pos', 'update_komplain_pos', 'delete_komplain_pos',
                'view_warehouse_documents',
                'view_widget_stats_overview',
                'view_widget_recent_activity',
            ],
            'Checker Retur' => [
                'view_task_retur_cabangs', 'create_task_retur_cabangs', 'update_task_retur_cabangs', 'delete_task_retur_cabangs',
                'view_supplier_returns', 'create_supplier_returns', 'update_supplier_returns', 'delete_supplier_returns',
                'view_warehouse_documents',
                'view_widget_stats_overview',
                'view_widget_recent_activity',
            ],
            'Checker Keluar' => [
                'view_task_keluar_barangs', 'create_task_keluar_barangs', 'update_task_keluar_barangs', 'delete_task_keluar_barangs',
                'view_branch_shipments', 'create_branch_shipments', 'update_branch_shipments', 'delete_branch_shipments',
                'view_warehouse_documents',
                'view_widget_stats_overview',
                'view_widget_recent_activity',
            ],
            'Checker Kiriman' => [
                'view_task_kiriman_mobils', 'create_task_kiriman_mobils', 'update_task_kiriman_mobils', 'delete_task_kiriman_mobils',
                'view_warehouse_documents',
                'view_widget_stats_overview',
                'view_widget_recent_activity',
            ],
        ];

        foreach ($defaults as $roleName => $perms) {
            $role = Role::where('name', $roleName)->first();
            if (!$role) {
                continue;
            }

            $role->is_super_admin = ($roleName === 'Admin');
            $role->permission_template = json_encode(array_values($perms));
            $role->save();
        }
    }
}
