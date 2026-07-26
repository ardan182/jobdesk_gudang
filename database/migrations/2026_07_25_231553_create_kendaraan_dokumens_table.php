<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kendaraan_dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_kendaraan_id')->constrained()->cascadeOnDelete();
            $table->enum('jenis', ['stnk', 'kir']);
            $table->string('nomor_dokumen')->nullable();
            $table->date('tanggal_terbit');
            $table->enum('periode', ['1_tahun', '5_tahun'])->nullable();
            $table->date('masa_berlaku');
            $table->string('user_perpanjang')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kendaraan_dokumens');
    }
};
