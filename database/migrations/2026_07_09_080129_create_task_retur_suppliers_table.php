<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_retur_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('id_task', 30)->unique();
            $table->integer('no_baris');
            $table->string('nama_supplier_ekspedisi');
            $table->string('no_plat_mobil', 20);
            $table->string('nama_sopir');
            $table->time('jam_muat');
            $table->integer('jumlah_kolian');
            $table->string('admin_sj_retur');
            $table->enum('status', ['servis', 'tukar', 'pot_nota']);
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_retur_suppliers');
    }
};
