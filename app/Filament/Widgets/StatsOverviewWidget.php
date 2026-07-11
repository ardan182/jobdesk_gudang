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
        $startOfDay = now()->startOfDay()->utc();
        $endOfDay = now()->endOfDay()->utc();
        $todayScope = fn ($q) => $q->whereBetween('created_at', [$startOfDay, $endOfDay]);

        if ($user?->hasRole('Admin')) {
            return [
                Stat::make('Retur ke Supplier', TaskReturSupplier::whereBetween('created_at', [$startOfDay, $endOfDay])->count())
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->color('warning'),
                Stat::make('Retur dari Cabang', TaskReturCabang::whereBetween('created_at', [$startOfDay, $endOfDay])->count())
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('info'),
                Stat::make('Terima Barang', TaskTerimaSupplier::whereBetween('created_at', [$startOfDay, $endOfDay])->count())
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success'),
                Stat::make('Keluar Barang', TaskKeluarBarang::whereBetween('created_at', [$startOfDay, $endOfDay])->count())
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('danger'),
                Stat::make('Kiriman Mobil', TaskKirimanMobil::whereBetween('created_at', [$startOfDay, $endOfDay])->count())
                    ->icon('heroicon-o-truck')
                    ->color('primary'),
            ];
        }

        $count = 0;
        $label = 'Task Hari Ini';

        if ($user?->hasRole('Checker Retur')) {
            $count = TaskReturSupplier::where('user_id', $user->id)->whereBetween('created_at', [$startOfDay, $endOfDay])->count()
                + TaskReturCabang::where('user_id', $user->id)->whereBetween('created_at', [$startOfDay, $endOfDay])->count();
            $label = 'Retur Hari Ini';
        } elseif ($user?->hasRole('Checker Terima')) {
            $count = TaskTerimaSupplier::where('user_id', $user->id)->whereBetween('created_at', [$startOfDay, $endOfDay])->count();
            $label = 'Terima Barang Hari Ini';
        } elseif ($user?->hasRole('Checker Keluar')) {
            $count = TaskKeluarBarang::where('user_id', $user->id)->whereBetween('created_at', [$startOfDay, $endOfDay])->count();
            $label = 'Keluar Barang Hari Ini';
        } elseif ($user?->hasRole('Checker Kiriman')) {
            $count = TaskKirimanMobil::where('user_id', $user->id)->whereBetween('created_at', [$startOfDay, $endOfDay])->count();
            $label = 'Kiriman Hari Ini';
        }

        return [
            Stat::make($label, $count)
                ->icon('heroicon-o-clipboard-document-check')
                ->color('primary'),
        ];
    }
}
