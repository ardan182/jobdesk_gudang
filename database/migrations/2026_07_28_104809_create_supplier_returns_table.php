<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_returns', function (Blueprint $table) {
            $table->id();
            $table->string('id_task', 30)->nullable()->index();
            $table->foreignId('arrival_supplier_truck_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('jenis_pengiriman', ['retur_masuk', 'retur_keluar', 'datang_dan_keluar']);
            $table->string('nama_supplier')->nullable();
            $table->string('nama_ekspedisi')->nullable();
            $table->string('nama_supir')->nullable();
            $table->string('no_plat_mobil')->nullable();
            $table->date('tanggal_datang')->nullable();
            $table->time('jam_kedatangan')->nullable();
            $table->string('jenis_retur')->nullable();
            $table->string('no_nota_retur')->nullable();
            $table->integer('total_koli')->nullable();
            $table->integer('total_kolian')->nullable();
            $table->enum('status', ['draft', 'selesai'])->default('draft');
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_returns');
    }
};
