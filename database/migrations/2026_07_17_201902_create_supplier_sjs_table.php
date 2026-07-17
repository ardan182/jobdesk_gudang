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
        Schema::create('supplier_sjs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_supplier')->nullable();
            $table->date('tanggal_datang')->nullable();
            $table->string('nomor_po_referensi')->nullable();
            $table->string('status_input')->nullable();
            $table->date('tanggal_input')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_sjs');
    }
};
