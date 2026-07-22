<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('task_kiriman_mobils', function (Blueprint $table) {
            $table->foreignId('keluar_barang_id')
                ->nullable()
                ->constrained('task_keluar_barangs')
                ->restrictOnDelete();
        });

        DB::statement("ALTER TABLE task_kiriman_mobils MODIFY COLUMN no_plat_mobil VARCHAR(20) NULL");
        DB::statement("ALTER TABLE task_kiriman_mobils MODIFY COLUMN jam_muat TIME NULL");
        DB::statement("ALTER TABLE task_kiriman_mobils MODIFY COLUMN jam_selesai_muat TIME NULL");
        DB::statement("ALTER TABLE task_kiriman_mobils MODIFY COLUMN jam_berangkat TIME NULL");
        DB::statement("ALTER TABLE task_kiriman_mobils MODIFY COLUMN nama_supir VARCHAR(255) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE task_kiriman_mobils MODIFY COLUMN nama_supir VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE task_kiriman_mobils MODIFY COLUMN jam_berangkat TIME NOT NULL");
        DB::statement("ALTER TABLE task_kiriman_mobils MODIFY COLUMN jam_selesai_muat TIME NOT NULL");
        DB::statement("ALTER TABLE task_kiriman_mobils MODIFY COLUMN jam_muat TIME NOT NULL");
        DB::statement("ALTER TABLE task_kiriman_mobils MODIFY COLUMN no_plat_mobil VARCHAR(20) NOT NULL");

        Schema::table('task_kiriman_mobils', function (Blueprint $table) {
            $table->dropColumn('keluar_barang_id');
        });
    }
};
