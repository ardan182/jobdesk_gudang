<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate data dari task_retur_suppliers (Retur Keluar)
        $returKeluar = DB::table('task_retur_suppliers')->get();
        foreach ($returKeluar as $row) {
            $jenisRetur = match ($row->status) {
                'tukar' => 'ganti_barang',
                'pot_nota' => 'potong_nota',
                default => 'servis',
            };
            DB::table('supplier_returns')->insert([
                'id_task' => $row->id_task,
                'arrival_supplier_truck_id' => $row->arrival_supplier_truck_id,
                'jenis_pengiriman' => 'retur_keluar',
                'nama_supplier' => $row->nama_supplier_ekspedisi,
                'nama_supir' => $row->nama_sopir,
                'no_plat_mobil' => $row->no_plat_mobil,
                'jenis_retur' => $jenisRetur,
                'no_nota_retur' => $row->admin_sj_retur,
                'total_kolian' => $row->jumlah_kolian,
                'jam_kedatangan' => $row->jam_muat,
                'status' => 'selesai',
                'keterangan' => $row->keterangan,
                'user_id' => $row->user_id,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }

        // Migrate data dari supplier_return_inbounds (Retur Masuk)
        $returMasuk = DB::table('supplier_return_inbounds')->get();
        foreach ($returMasuk as $row) {
            DB::table('supplier_returns')->insert([
                'jenis_pengiriman' => 'retur_masuk',
                'nama_supplier' => $row->nama_supplier,
                'nama_ekspedisi' => $row->nama_ekspedisi,
                'nama_supir' => $row->nama_supir,
                'no_plat_mobil' => $row->no_plat_mobil,
                'tanggal_datang' => $row->tanggal_datang,
                'jam_kedatangan' => $row->jam_kedatangan,
                'no_nota_retur' => $row->no_nota_retur,
                'total_koli' => $row->jumlah_kolian,
                'status' => 'selesai',
                'keterangan' => $row->keterangan,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('supplier_returns')->truncate();
    }
};
