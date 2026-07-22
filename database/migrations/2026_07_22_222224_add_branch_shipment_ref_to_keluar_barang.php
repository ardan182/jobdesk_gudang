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
        Schema::table('task_keluar_barangs', function (Blueprint $table) {
            $table->foreignId('branch_shipment_id')
                ->nullable()
                ->constrained('branch_shipments')
                ->restrictOnDelete();

            $table->time('jam_disiapkan')->nullable();
            $table->string('diserahkan_kepada', 100)->nullable();
            $table->text('helper')->nullable();

            $table->dropColumn([
                'toko_tujuan',
                'supplier',
                'no_referensi_sj',
                'jumlah_kolian',
                'jam_naik',
                'nama_koordinator',
            ]);
        });

        DB::statement("ALTER TABLE task_keluar_barangs MODIFY COLUMN status ENUM('draft','siap kirim','selesai') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE task_keluar_barangs MODIFY COLUMN status ENUM('komplit','kurang','lebih') NOT NULL");

        Schema::table('task_keluar_barangs', function (Blueprint $table) {
            $table->dropColumn([
                'helper',
                'diserahkan_kepada',
                'jam_disiapkan',
                'branch_shipment_id',
            ]);

            $table->string('nama_koordinator');
            $table->time('jam_naik');
            $table->integer('jumlah_kolian');
            $table->string('no_referensi_sj', 100);
            $table->string('supplier');
            $table->string('toko_tujuan');
        });
    }
};
