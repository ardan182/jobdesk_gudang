<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah foreign key column (nullable dulu)
        Schema::table('warehouse_employees', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable()->constrained()->restrictOnDelete();
        });

        // 2. Migrasi data: insert divisions dari existing divisi_gudang values
        $divisis = DB::table('warehouse_employees')
            ->whereNotNull('divisi_gudang')
            ->distinct()
            ->pluck('divisi_gudang');

        foreach ($divisis as $nama) {
            $existing = DB::table('divisions')->where('nama_divisi', $nama)->first();
            if (!$existing) {
                $id = DB::table('divisions')->insertGetId(['nama_divisi' => $nama, 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        // 3. Update division_id berdasarkan divisi_gudang
        DB::statement("UPDATE warehouse_employees e JOIN divisions d ON e.divisi_gudang = d.nama_divisi SET e.division_id = d.id WHERE e.division_id IS NULL");

        // 4. Hapus kolom lama
        Schema::table('warehouse_employees', function (Blueprint $table) {
            $table->dropColumn('divisi_gudang');
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_employees', function (Blueprint $table) {
            $table->string('divisi_gudang')->nullable();
            $table->dropForeign(['division_id']);
            $table->dropColumn('division_id');
        });
    }
};
