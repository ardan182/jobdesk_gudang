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
        'datang_mobil_supplier' => 'ARR-SUP',
        'branch_shipment' => 'KRM-BRG',
    ];

    const TABLE_MAP = [
        'retur_supplier' => 'task_retur_suppliers',
        'retur_cabang' => 'task_retur_cabangs',
        'terima_supplier' => 'task_terima_suppliers',
        'keluar_barang' => 'task_keluar_barangs',
        'kiriman_mobil' => 'task_kiriman_mobils',
        'datang_mobil_supplier' => 'arrival_supplier_trucks',
        'branch_shipment' => 'branch_shipments',
    ];

    private static array $lastGenerated = [];

    public static function generate(string $type): string
    {
        $prefix = self::PREFIXES[$type] ?? strtoupper($type);
        $table = self::TABLE_MAP[$type] ?? $type;

        if (! isset(self::$lastGenerated[$prefix])) {
            $last = DB::table($table)
                ->where('id_task', 'like', $prefix . '-%')
                ->selectRaw('MAX(CAST(SUBSTRING_INDEX(id_task, \'-\', -1) AS UNSIGNED)) as last_num')
                ->value('last_num');

            self::$lastGenerated[$prefix] = (int) ($last ?? 0);
        }

        self::$lastGenerated[$prefix]++;

        $nextNumber = self::$lastGenerated[$prefix];
        $formatted = $nextNumber < 100000
            ? str_pad($nextNumber, 5, '0', STR_PAD_LEFT)
            : (string) $nextNumber;

        return "{$prefix}-{$formatted}";
    }
}
