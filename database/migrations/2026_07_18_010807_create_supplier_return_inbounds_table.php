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
        Schema::create('supplier_return_inbounds', function (Blueprint $table) {
            $table->id();
            $table->string('nama_supplier')->nullable();
            $table->string('nama_ekspedisi')->nullable();
            $table->string('nama_supir')->nullable();
            $table->string('no_plat_mobil')->nullable();
            $table->date('tanggal_datang')->nullable();
            $table->time('jam_kedatangan')->nullable();
            $table->string('no_nota_retur')->nullable();
            $table->integer('jumlah_kolian')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_return_inbounds');
    }
};
