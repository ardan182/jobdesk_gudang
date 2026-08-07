<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Components\Section;

class PermissionMenu
{
    /**
     * Daftar modul per nav-group.
     *
     * @return array<string, array<int, array{key: string, label: string}>>
     */
    public static function groups(): array
    {
        return [
            'Master' => [
                ['key' => 'master_suppliers', 'label' => 'Master Supplier'],
                ['key' => 'master_tokos', 'label' => 'Master Toko'],
                ['key' => 'master_kendaraans', 'label' => 'Master Kendaraan'],
                ['key' => 'master_sopirs', 'label' => 'Master Sopir'],
                ['key' => 'expeditions', 'label' => 'Master Ekspedisi'],
                ['key' => 'warehouse_employees', 'label' => 'Master Employee Gudang'],
            ],
            'Purchasing Order' => [
                ['key' => 'komplain_pos', 'label' => 'Komplain PO'],
            ],
            'Retur' => [
                ['key' => 'task_retur_cabangs', 'label' => 'Retur Masuk dari Toko'],
                ['key' => 'supplier_returns', 'label' => 'Retur In & Out Supplier'],
            ],
            'Penerimaan' => [
                ['key' => 'task_datang_mobil_suppliers', 'label' => 'Datang Mobil Supplier'],
                ['key' => 'task_terima_suppliers', 'label' => 'Checker Terima Barang Supplier'],
                ['key' => 'supplier_sjs', 'label' => 'Input SJ dari Supplier'],
            ],
            'Pengiriman' => [
                ['key' => 'branch_shipments', 'label' => 'Input Kirim Barang'],
                ['key' => 'task_keluar_barangs', 'label' => 'Checker Keluar Barang'],
                ['key' => 'task_kiriman_mobils', 'label' => 'Kiriman Mobil'],
            ],
            'Administrasi' => [
                ['key' => 'warehouse_documents', 'label' => 'Pusat Dokumen'],
                ['key' => 'kendaraan_dokumens', 'label' => 'Masa Berlaku STNK/KIR'],
                ['key' => 'cuti_absensi', 'label' => 'Cuti & Absensi'],
            ],
            'Pengaturan' => [
                ['key' => 'users', 'label' => 'Users'],
                ['key' => 'board_tv_settings', 'label' => 'Pengaturan Board TV'],
            ],
        ];
    }

    /**
     * Daftar widget dashboard yang bisa dipilih.
     *
     * @return array<int, array{permission: string, label: string}>
     */
    public static function widgets(): array
    {
        return [
            ['permission' => 'view_widget_stats_overview', 'label' => 'Widget Summary (Stats Overview)'],
            ['permission' => 'view_widget_recent_activity', 'label' => 'Widget Aktivitas Terakhir'],
            ['permission' => 'view_widget_expiring_documents', 'label' => 'Widget Dokumen Expired'],
            ['permission' => 'view_widget_leaves_today', 'label' => 'Widget Cuti Hari Ini'],
        ];
    }

    /**
     * Bangun tree checkbox flat (Group header + menu) untuk form.
     *
     * @param  callable(object|null): array<int, string>  $granted  callable(record): daftar nama permission yang aktif
     * @return array<int, Section>
     */
    public static function buildTree(callable $granted): array
    {
        $sections = [
            self::globalSection($granted),
        ];

        foreach (self::groups() as $group => $modules) {
            $sections[] = Section::make("Group: {$group}")
                ->collapsible(false)
                ->compact()
                ->schema(array_map(
                    fn (array $m) => self::moduleSection($m['label'], $m['key'], $granted),
                    $modules
                ));
        }

        $sections[] = Section::make('Group: Dashboard & Widgets')
            ->collapsible(false)
            ->compact()
            ->schema(array_map(
                fn (array $w) => self::widgetSection($w['label'], $w['permission'], $granted),
                self::widgets()
            ));

        return $sections;
    }

    protected static function globalSection(callable $granted): Section
    {
        return Section::make('Akses Global')
            ->description('Centang untuk melihat semua data di semua modul yang bisa diakses.')
            ->compact()
            ->schema([
                Checkbox::make('perm_view_all_data')
                    ->label('Lihat Semua Data')
                    ->dehydrated(false)
                    ->live()
                    ->afterStateHydrated(function ($component, $record) use ($granted) {
                        if (!$record) return;
                        $component->state(in_array('view_all_data', $granted($record)));
                    }),
            ]);
    }

    protected static function moduleSection(string $label, string $key, callable $granted): Section
    {
        return Section::make($label)
            ->compact()
            ->columns(5)
            ->schema([
                Checkbox::make("select_all_{$key}")
                    ->label('Pilih Semua')
                    ->dehydrated(false)
                    ->live()
                    ->afterStateHydrated(function ($component, $record) use ($key, $granted) {
                        if (!$record) return;
                        $g = $granted($record);
                        $component->state(
                            in_array("view_{$key}", $g)
                            && in_array("create_{$key}", $g)
                            && in_array("update_{$key}", $g)
                            && in_array("delete_{$key}", $g)
                        );
                    })
                    ->afterStateUpdated(function ($state, $set) use ($key) {
                        $set("perm_view_{$key}", $state);
                        $set("perm_create_{$key}", $state);
                        $set("perm_update_{$key}", $state);
                        $set("perm_delete_{$key}", $state);
                    }),
                self::permCheckbox('Lihat (View)', "perm_view_{$key}", "view_{$key}", $key, $granted),
                self::permCheckbox('Tambah (Create)', "perm_create_{$key}", "create_{$key}", $key, $granted),
                self::permCheckbox('Ubah (Update)', "perm_update_{$key}", "update_{$key}", $key, $granted),
                self::permCheckbox('Hapus (Delete)', "perm_delete_{$key}", "delete_{$key}", $key, $granted),
            ]);
    }

    protected static function permCheckbox(string $label, string $field, string $permission, string $key, callable $granted): Checkbox
    {
        return Checkbox::make($field)
            ->label($label)
            ->dehydrated(false)
            ->live()
            ->afterStateHydrated(function ($component, $record) use ($permission, $granted) {
                if (!$record) return;
                $component->state(in_array($permission, $granted($record)));
            })
            ->afterStateUpdated(function ($state, $set, $get) use ($key) {
                if (!$state) {
                    $set("select_all_{$key}", false);
                    return;
                }
                $all = $get("perm_view_{$key}") && $get("perm_create_{$key}")
                    && $get("perm_update_{$key}") && $get("perm_delete_{$key}");
                $set("select_all_{$key}", $all);
            });
    }

    protected static function widgetSection(string $label, string $permission, callable $granted): Section
    {
        return Section::make($label)
            ->compact()
            ->columns(2)
            ->schema([
                Checkbox::make("perm_{$permission}")
                    ->label('Aktif')
                    ->dehydrated(false)
                    ->afterStateHydrated(function ($component, $record) use ($permission, $granted) {
                        if (!$record) return;
                        $component->state(in_array($permission, $granted($record)));
                    }),
            ]);
    }
}
