<?php

namespace App\Filament\Widgets;

use App\Models\TaskKeluarBarang;
use App\Models\TaskKirimanMobil;
use App\Models\TaskReturCabang;
use App\Models\TaskReturSupplier;
use App\Models\TaskTerimaSupplier;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        if ($user?->hasRole('Admin')) {
            return [
                Stat::make('Retur ke Supplier', TaskReturSupplier::count())
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->color('warning'),
                Stat::make('Retur dari Cabang', TaskReturCabang::count())
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('info'),
                Stat::make('Terima Barang', TaskTerimaSupplier::count())
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success'),
                Stat::make('Keluar Barang', TaskKeluarBarang::count())
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('danger'),
                Stat::make('Kiriman Mobil', TaskKirimanMobil::count())
                    ->icon('heroicon-o-truck')
                    ->color('primary'),
            ];
        }

        $count = 0;
        $label = 'Total Task';

        if ($user?->hasRole('Checker Retur')) {
            return [
                Stat::make('Retur ke Supplier', TaskReturSupplier::where('user_id', $user->id)->count())
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->color('warning'),
                Stat::make('Retur dari Cabang', TaskReturCabang::where('user_id', $user->id)->count())
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('info'),
            ];
        } elseif ($user?->hasRole('Checker Terima')) {
            $count = TaskTerimaSupplier::where('user_id', $user->id)->count();
            $label = 'Total Terima Barang';
        } elseif ($user?->hasRole('Checker Keluar')) {
            $count = TaskKeluarBarang::where('user_id', $user->id)->count();
            $label = 'Total Keluar Barang';
        } elseif ($user?->hasRole('Checker Kiriman')) {
            $count = TaskKirimanMobil::where('user_id', $user->id)->count();
            $label = 'Total Kiriman';
        }

        return [
            Stat::make($label, $count)
                ->icon('heroicon-o-clipboard-document-check')
                ->color('primary'),
        ];
    }
}
