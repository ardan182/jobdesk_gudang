<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
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
                    ->required(),
                Section::make('Akses Menu & Fitur')
                    ->description('Atur hak akses menu dan fitur secara detail. Admin otomatis punya semua akses.')
                    ->collapsible()
                    ->columns(1)
                    ->schema([
                        self::group('Master', [
                            self::module('Master Supplier', 'master_suppliers'),
                            self::module('Master Toko', 'master_tokos'),
                            self::module('Master Kendaraan', 'master_kendaraans'),
                            self::module('Master Sopir', 'master_sopirs'),
                            self::module('Master Ekspedisi', 'expeditions'),
                            self::module('Master Employee Gudang', 'warehouse_employees'),
                        ]),
                        self::group('Purchasing Order', [
                            self::module('Komplain PO', 'komplain_pos'),
                        ]),
                        self::group('Retur', [
                            self::module('Retur Masuk dari Toko', 'task_retur_cabangs'),
                            self::module('Retur In & Out Supplier', 'supplier_returns'),
                        ]),
                        self::group('Penerimaan', [
                            self::module('Datang Mobil Supplier', 'task_datang_mobil_suppliers'),
                            self::module('Checker Terima Barang Supplier', 'task_terima_suppliers'),
                            self::module('Input SJ dari Supplier', 'supplier_sjs'),
                        ]),
                        self::group('Pengiriman', [
                            self::module('Input Kirim Barang', 'branch_shipments'),
                            self::module('Checker Keluar Barang', 'task_keluar_barangs'),
                            self::module('Kiriman Mobil', 'task_kiriman_mobils'),
                        ]),
                        self::group('Administrasi', [
                            self::module('Pusat Dokumen', 'warehouse_documents'),
                            self::module('Masa Berlaku STNK/KIR', 'kendaraan_dokumens'),
                            self::module('Cuti & Absensi', 'cuti_absensi'),
                        ]),
                        self::group('Pengaturan', [
                            self::module('Users', 'users'),
                            self::module('Pengaturan Board TV', 'board_tv_settings'),
                        ]),
                        self::group('Dashboard & Widgets', [
                            self::widget('Widget Summary (Stats Overview)', 'view_widget_stats_overview'),
                            self::widget('Widget Aktivitas Terakhir', 'view_widget_recent_activity'),
                            self::widget('Widget Dokumen Expired', 'view_widget_expiring_documents'),
                            self::widget('Widget Cuti Hari Ini', 'view_widget_leaves_today'),
                        ]),
                    ]),
            ]);
    }

    protected static function group(string $label, array $components): Section
    {
        return Section::make("Group: {$label}")
            ->collapsible()
            ->compact()
            ->schema($components);
    }

    protected static function module(string $label, string $key): Section
    {
        return Section::make($label)
            ->compact()
            ->columns(5)
            ->schema([
                Checkbox::make("select_all_{$key}")
                    ->label('Pilih Semua')
                    ->dehydrated(false)
                    ->live()
                    ->afterStateHydrated(function ($component, $record) use ($key) {
                        if (!$record) return;
                        try {
                            $component->state(
                                $record->hasDirectPermission("view_{$key}")
                                && $record->hasDirectPermission("create_{$key}")
                                && $record->hasDirectPermission("update_{$key}")
                                && $record->hasDirectPermission("delete_{$key}")
                            );
                        } catch (\Throwable $e) {
                            $component->state(false);
                        }
                    })
                    ->afterStateUpdated(function ($state, $set) use ($key) {
                        $set("perm_view_{$key}", $state);
                        $set("perm_create_{$key}", $state);
                        $set("perm_update_{$key}", $state);
                        $set("perm_delete_{$key}", $state);
                    }),
                self::permCheckbox('Lihat (View)', "perm_view_{$key}", "view_{$key}", $key),
                self::permCheckbox('Tambah (Create)', "perm_create_{$key}", "create_{$key}", $key),
                self::permCheckbox('Ubah (Update)', "perm_update_{$key}", "update_{$key}", $key),
                self::permCheckbox('Hapus (Delete)', "perm_delete_{$key}", "delete_{$key}", $key),
            ]);
    }

    protected static function widget(string $label, string $permission): Section
    {
        return Section::make($label)
            ->compact()
            ->columns(2)
            ->schema([
                Checkbox::make("perm_{$permission}")
                    ->label('Aktif')
                    ->dehydrated(false)
                    ->afterStateHydrated(function ($component, $record) use ($permission) {
                        if (!$record) return;
                        try {
                            $component->state($record->hasDirectPermission($permission));
                        } catch (\Throwable $e) {
                            $component->state(false);
                        }
                    }),
            ]);
    }

    protected static function permCheckbox(string $label, string $field, string $permission, string $key): Checkbox
    {
        return Checkbox::make($field)
            ->label($label)
            ->dehydrated(false)
            ->live()
            ->afterStateHydrated(function ($component, $record) use ($permission) {
                if (!$record) return;
                try {
                    $component->state($record->hasDirectPermission($permission));
                } catch (\Throwable $e) {
                    $component->state(false);
                }
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
}
