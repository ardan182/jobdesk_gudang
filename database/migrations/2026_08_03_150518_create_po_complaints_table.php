<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('po_complaints', function (Blueprint $table) {
            $table->id();
            $table->string('id_task', 30)->nullable()->index();
            $table->string('cabang');
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('no_po')->nullable();
            $table->string('barcode')->nullable();
            $table->string('nama_barang')->nullable();
            $table->integer('qty_diterima')->nullable();
            $table->string('no_surat_jalan')->nullable();
            $table->integer('qty_disurat_jalan')->nullable();
            $table->json('foto')->nullable();
            $table->date('tanggal_datang_barang')->nullable();
            $table->enum('kondisi_barang', ['tidak_sesuai', 'tidak_lengkap'])->nullable();
            $table->enum('penyelesaian', ['potong_nota', 'retur', 'ganti_barang'])->nullable();
            $table->enum('status', ['draft', 'selesai'])->default('draft');
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_complaints');
    }
};
