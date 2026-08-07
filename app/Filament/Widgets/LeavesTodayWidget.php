<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\ManageLeaves;
use App\Models\WarehouseLeave;
use ThalysJuvenal\Aurum\Widgets\AurumValueList;
use ThalysJuvenal\Aurum\Widgets\ValueListItem;

class LeavesTodayWidget extends AurumValueList
{
    protected ?string $heading = '📋 Cuti / Sakit / Izin Hari Ini';

    protected static ?int $sort = 2;

    protected function getItems(): array
    {
        $leaves = WarehouseLeave::with('employee.division')
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->orderBy('tanggal_mulai')
            ->limit(10)
            ->get();

        return $leaves->map(function (WarehouseLeave $leave) {
            $nama = $leave->employee?->nama_karyawan ?? 'Unknown';
            $divisi = $leave->employee?->division?->nama_divisi;

            return ValueListItem::make("👤 {$nama}" . ($divisi ? " — {$divisi}" : ''))
                ->value((string) $leave->jenis_absen)
                ->status(match ($leave->jenis_absen) {
                    'Cuti' => 'info',
                    'Sakit' => 'warning',
                    default => 'muted',
                })
                ->url(ManageLeaves::getUrl());
        })->all();
    }

    public static function canView(): bool
    {
        return auth()->user()?->can('view_widget_leaves_today') ?? false;
    }
}
