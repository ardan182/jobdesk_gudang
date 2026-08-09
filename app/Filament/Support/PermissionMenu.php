<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
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
     * Bangun seluruh tree (global + menu + widgets) untuk form.
     *
     * @param  callable(object|null): array<int, string>  $granted
     * @return array<int, Section>
     */
    public static function buildTree(callable $granted): array
    {
        return [
            self::globalSection($granted),
            ...self::menuSections($granted),
            ...self::widgetsSections($granted),
        ];
    }

    /**
     * Section akses global (lihat semua data).
     *
     * @param  callable(object|null): array<int, string>  $granted
     * @return Section
     */
    public static function globalSection(callable $granted): Section
    {
        return Section::make('Akses Global')
            ->description('Centang untuk melihat semua data di semua modul yang bisa diakses.')
            ->compact()
            ->schema([
                Checkbox::make('perm_view_all_data')
                    ->label('Lihat Semua Data')
                    ->helperText('Centang agar user melihat data milik semua pengguna (bukan hanya data sendiri) di modul yang bisa diakses. Hanya memperluas tampilan — tidak menambah hak tambah/ubah/hapus.')
                    ->live()
                    ->afterStateHydrated(function ($component, $record) use ($granted) {
                        if (!$record) return;
                        $component->state(in_array('view_all_data', $granted($record)));
                    }),
            ]);
    }

    /**
     * Matriks permission per group (akordeon, 2 kolom).
     *
     * @param  callable(object|null): array<int, string>  $granted
     * @return array<int, Section>
     */
    public static function menuSections(callable $granted): array
    {
        $sections = [];

        foreach (self::groups() as $group => $modules) {
            $sections[] = Section::make("Group: {$group}")
                ->collapsible()
                ->collapsed()
                ->compact()
                ->schema([
                    Grid::make(2)
                        ->schema(array_map(
                            fn (array $m) => self::moduleMatrix($m['label'], $m['key'], $granted),
                            $modules
                        )),
                ]);
        }

        return $sections;
    }

    /**
     * Widget dashboard.
     *
     * @param  callable(object|null): array<int, string>  $granted
     * @return array<int, Section>
     */
    public static function widgetsSections(callable $granted): array
    {
        return [
            Section::make('Group: Dashboard & Widgets')
                ->collapsible(false)
                ->compact()
                ->schema(array_map(
                    fn (array $w) => self::widgetSection($w['label'], $w['permission'], $granted),
                    self::widgets()
                )),
        ];
    }

    /**
     * Kotak matriks satu modul: Pilih Semua + View/Create/Update/Delete.
     *
     * @param  callable(object|null): array<int, string>  $granted
     */
    protected static function moduleMatrix(string $label, string $key, callable $granted): Fieldset
    {
        return Fieldset::make($label)
            ->columns(5)
            ->schema([
                self::selectAllCheckbox($key, $granted),
                self::permCheckbox('View', "perm_view_{$key}", "view_{$key}", $key, $granted),
                self::permCheckbox('Create', "perm_create_{$key}", "create_{$key}", $key, $granted),
                self::permCheckbox('Update', "perm_update_{$key}", "update_{$key}", $key, $granted),
                self::permCheckbox('Delete', "perm_delete_{$key}", "delete_{$key}", $key, $granted),
            ]);
    }

    /**
     * @param  callable(object|null): array<int, string>  $granted
     */
    protected static function selectAllCheckbox(string $key, callable $granted): Checkbox
    {
        return Checkbox::make("select_all_{$key}")
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
            });
    }

    /**
     * @param  callable(object|null): array<int, string>  $granted
     */
    protected static function permCheckbox(string $label, string $field, string $permission, string $key, callable $granted): Checkbox
    {
        return Checkbox::make($field)
            ->label($label)
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

    /**
     * @param  callable(object|null): array<int, string>  $granted
     */
    protected static function widgetSection(string $label, string $permission, callable $granted): Section
    {
        return Section::make($label)
            ->compact()
            ->columns(2)
            ->schema([
                Checkbox::make("perm_{$permission}")
                    ->label('Aktif')
                    ->afterStateHydrated(function ($component, $record) use ($permission, $granted) {
                        if (!$record) return;
                        $component->state(in_array($permission, $granted($record)));
                    }),
            ]);
    }
}