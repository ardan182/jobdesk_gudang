<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TaskIdGenerator
{
    const PREFIXES = [
        'retur_supplier' => 'RET-SUP',
        'retur_cabang' => 'RET-CAB',
        'terima_supplier' => 'TRM-SUP',
        'keluar_barang' => 'KLR',
        'kiriman_mobil' => 'KRM',
    ];

    public static function generate(string $type): string
    {
        $prefix = self::PREFIXES[$type] ?? strtoupper($type);
        $date = now()->format('Ymd');
        $table = self::getTableName($type);

        $lastTask = DB::table($table)
            ->whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $lastNumber = 0;
        if ($lastTask && preg_match('/' . preg_quote($prefix) . '-' . $date . '-(\d+)/', $lastTask->id_task, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return "{$prefix}-{$date}-{$nextNumber}";
    }

    public static function getNextBaris(string $type): int
    {
        $table = self::getTableName($type);

        $lastTask = DB::table($table)
            ->whereDate('created_at', today())
            ->orderBy('no_baris', 'desc')
            ->first();

        return $lastTask ? $lastTask->no_baris + 1 : 1;
    }

    public static function getLastIdNumber(string $type): int
    {
        $prefix = self::PREFIXES[$type] ?? strtoupper($type);
        $date = now()->format('Ymd');
        $table = self::getTableName($type);

        $lastTask = DB::table($table)
            ->whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTask && preg_match('/' . preg_quote($prefix) . '-' . $date . '-(\d+)/', $lastTask->id_task, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }

    public static function formatId(string $type, int $number): string
    {
        $prefix = self::PREFIXES[$type] ?? strtoupper($type);
        $date = now()->format('Ymd');

        return "{$prefix}-{$date}-" . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    private static function getTableName(string $type): string
    {
        $tables = [
            'retur_supplier' => 'task_retur_suppliers',
            'retur_cabang' => 'task_retur_cabangs',
            'terima_supplier' => 'task_terima_suppliers',
            'keluar_barang' => 'task_keluar_barangs',
            'kiriman_mobil' => 'task_kiriman_mobils',
        ];

        return $tables[$type] ?? $type;
    }
}
