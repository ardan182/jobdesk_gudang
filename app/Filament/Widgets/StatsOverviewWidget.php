<?php

namespace App\Filament\Widgets;

use App\Models\ArrivalSupplierTruck;
use App\Models\KendaraanDokumen;
use App\Models\SupplierSj;
use App\Models\TaskKeluarBarang;
use App\Models\TaskKirimanMobil;
use App\Models\TaskReturCabang;
use App\Models\SupplierReturn;
use App\Models\TaskTerimaSupplier;
use ThalysJuvenal\Aurum\Widgets\AurumStat;
use ThalysJuvenal\Aurum\Widgets\AurumStatsOverview;

class StatsOverviewWidget extends AurumStatsOverview
{
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->can('view_widget_stats_overview') ?? false;
    }

    protected function getStats(): array
    {
        $user = auth()->user();

        if ($user?->hasRole('Admin')) {
            return [
                AurumStat::make('Retur ke Supplier', (string) SupplierReturn::where('jenis_pengiriman', 'retur_keluar')->count())
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->description('Total retur keluar ke supplier'),
                AurumStat::make('Retur dari Supplier', (string) SupplierReturn::where('jenis_pengiriman', 'retur_masuk')->count())
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->description('Total retur masuk dari supplier'),
                AurumStat::make('Terima Barang', (string) TaskTerimaSupplier::count())
                    ->icon('heroicon-o-arrow-down-tray')
                    ->description('Total barang diterima'),
                AurumStat::make('Keluar Barang', (string) TaskKeluarBarang::count())
                    ->icon('heroicon-o-arrow-up-tray')
                    ->description('Total barang keluar'),
                AurumStat::make('Kiriman Mobil', (string) TaskKirimanMobil::count())
                    ->icon('heroicon-o-truck')
                    ->description('Total pengiriman mobil'),
                AurumStat::make('Retur Masuk Cabang', (string) TaskReturCabang::count())
                    ->icon('heroicon-o-arrow-path')
                    ->description('Total retur dari cabang'),
                AurumStat::make('Datang Mobil', (string) ArrivalSupplierTruck::count())
                    ->icon('heroicon-o-truck')
                    ->description('Total mobil supplier'),
                AurumStat::make('SJ Belum Di Cek', (string) SupplierSj::where('status_input', 'belum_di_cek')->count())
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->description('SJ pending cek'),
                AurumStat::make('STNK/KIR ≤ 30 hari', (string) KendaraanDokumen::whereNotNull('masa_berlaku')
                    ->where('masa_berlaku', '<=', now()->addDays(30))
                    ->count())
                    ->icon('heroicon-o-exclamation-triangle')
                    ->description('Dokumen expired / akan expired'),
            ];
        }

        if ($user?->hasRole('Checker Retur')) {
            return [
                AurumStat::make('Retur ke Supplier', (string) SupplierReturn::where('jenis_pengiriman', 'retur_keluar')->where('user_id', $user->id)->count())
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->description('Retur keluar Anda'),
                AurumStat::make('Retur dari Supplier', (string) SupplierReturn::where('jenis_pengiriman', 'retur_masuk')->where('user_id', $user->id)->count())
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->description('Retur masuk Anda'),
            ];
        }

        if ($user?->hasRole('Checker Terima')) {
            $count = TaskTerimaSupplier::where('user_id', $user->id)->count();

            return [
                AurumStat::make('Total Terima Barang', (string) $count)
                    ->icon('heroicon-o-arrow-down-tray')
                    ->description('Total terima barang Anda'),
            ];
        }

        if ($user?->hasRole('Checker Keluar')) {
            $count = TaskKeluarBarang::where('user_id', $user->id)->count();

            return [
                AurumStat::make('Total Keluar Barang', (string) $count)
                    ->icon('heroicon-o-arrow-up-tray')
                    ->description('Total keluar barang Anda'),
            ];
        }

        if ($user?->hasRole('Checker Kiriman')) {
            $count = TaskKirimanMobil::where('user_id', $user->id)->count();

            return [
                AurumStat::make('Total Kiriman', (string) $count)
                    ->icon('heroicon-o-truck')
                    ->description('Total kiriman Anda'),
            ];
        }

        return [
            AurumStat::make('Total Task', '0')
                ->icon('heroicon-o-clipboard-document-check')
                ->description('Belum ada task'),
        ];
    }
}
