<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arrival_supplier_trucks', function (Blueprint $table) {
            $table->id();
            $table->string('id_task', 30)->index();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('expedition_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('nama_sopir');
            $table->date('tanggal_datang');
            $table->string('no_plat_mobil');
            $table->time('jam_datang');
            $table->time('jam_selesai')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arrival_supplier_trucks');
    }
};
