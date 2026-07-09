<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_retur_cabangs', function (Blueprint $table) {
            $table->id();
            $table->string('id_task', 30)->unique();
            $table->integer('no_baris');
            $table->string('cabang');
            $table->enum('jenis_retur', ['retur_jelek', 'retur_bagus']);
            $table->string('no_sj_retur');
            $table->integer('total_kolian');
            $table->time('jam_bongkar');
            $table->string('nama_sopir');
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_retur_cabangs');
    }
};
