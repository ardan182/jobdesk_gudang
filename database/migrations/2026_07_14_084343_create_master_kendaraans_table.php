<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_kendaraans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_polisi')->unique();
            $table->string('jenis_kendaraan');
            $table->string('merek_dan_model')->nullable();
            $table->string('nomor_rangka')->nullable();
            $table->string('nomor_mesin')->nullable();
            $table->string('no_stnk')->nullable();
            $table->string('no_kir')->nullable();
            $table->timestamps();
        });

        DB::statement("INSERT INTO master_kendaraans (nomor_polisi, jenis_kendaraan, merek_dan_model, created_at, updated_at) SELECT no_plat_mobil, 'mobil', nama_mobil, NOW(), NOW() FROM master_mobils");

        Schema::dropIfExists('master_mobils');
    }

    public function down(): void
    {
        Schema::create('master_mobils', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mobil');
            $table->string('no_plat_mobil');
            $table->timestamps();
        });

        DB::statement("INSERT INTO master_mobils (nama_mobil, no_plat_mobil, created_at, updated_at) SELECT merek_dan_model, nomor_polisi, NOW(), NOW() FROM master_kendaraans");

        Schema::dropIfExists('master_kendaraans');
    }
};
