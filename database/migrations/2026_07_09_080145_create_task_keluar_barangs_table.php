<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_keluar_barangs', function (Blueprint $table) {
            $table->id();
            $table->string('id_task', 30)->unique();
            $table->integer('no_baris');
            $table->enum('toko_tujuan', ['pusat', 'ujungberung', 'soreang', 'majalaya', 'cicaheum', 'barokah']);
            $table->string('supplier');
            $table->string('no_referensi_sj', 100);
            $table->integer('jumlah_kolian');
            $table->time('jam_naik');
            $table->string('nama_koordinator');
            $table->enum('status', ['komplit', 'kurang', 'lebih']);
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_keluar_barangs');
    }
};
