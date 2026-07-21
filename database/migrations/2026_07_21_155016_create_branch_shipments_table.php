<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_shipments', function (Blueprint $table) {
            $table->id();
            $table->enum('pilih_kiriman', ['pembagian_po', 'stock_gudang']);
            $table->string('cabang');
            $table->string('nomor_sj', 100);
            $table->integer('total_qty');
            $table->string('no_po', 100)->nullable();
            $table->date('tanggal_buat');
            $table->enum('status', ['draft', 'selesai'])->default('draft');
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_shipments');
    }
};
