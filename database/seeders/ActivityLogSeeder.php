<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'task_retur_suppliers' => [
                'module' => 'Retur Supplier',
                'desc' => fn ($r) => "Supplier: {$r->nama_supplier_ekspedisi} → {$r->status}",
                'ref' => fn ($r) => $r->no_plat_mobil,
            ],
            'task_retur_cabangs' => [
                'module' => 'Retur Cabang',
                'desc' => fn ($r) => "Cabang: {$r->cabang} → {$r->jenis_retur}",
                'ref' => fn ($r) => $r->no_sj_retur,
            ],
            'task_terima_suppliers' => [
                'module' => 'Terima Supplier',
                'desc' => fn ($r) => "Supplier: {$r->nama_supplier_ekspedisi} → {$r->status}",
                'ref' => fn ($r) => $r->no_po_referensi,
            ],
            'task_keluar_barangs' => [
                'module' => 'Keluar Barang',
                'desc' => fn ($r) => "Toko: {$r->toko_tujuan} → {$r->status}",
                'ref' => fn ($r) => $r->no_referensi_sj,
            ],
            'task_kiriman_mobils' => [
                'module' => 'Kiriman Mobil',
                'desc' => fn ($r) => "Cabang: {$r->cabang} - Plat: {$r->no_plat_mobil}",
                'ref' => fn ($r) => $r->nama_supir,
            ],
        ];

        foreach ($modules as $table => $config) {
            DB::table($table)->orderBy('id')->chunk(100, function ($rows) use ($config) {
                foreach ($rows as $row) {
                    ActivityLog::create([
                        'user_id' => $row->user_id,
                        'module' => $config['module'],
                        'id_task' => $row->id_task,
                        'description' => $config['desc']($row),
                        'reference' => $config['ref']($row),
                        'action' => 'create',
                        'created_at' => $row->created_at,
                        'updated_at' => $row->created_at,
                    ]);
                }
            });
        }

        $this->command->info('Activity logs migrated from existing task data.');
    }
}
