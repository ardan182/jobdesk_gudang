<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_kiriman_mobils', function (Blueprint $table) {
            $table->id();
            $table->string('id_task', 30)->unique();
            $table->integer('no_baris');
            $table->string('cabang');
            $table->string('no_plat_mobil', 20);
            $table->time('jam_muat');
            $table->time('jam_selesai_muat');
            $table->time('jam_berangkat');
            $table->string('nama_supir');
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_kiriman_mobils');
    }
};
