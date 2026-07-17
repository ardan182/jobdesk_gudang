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
        Schema::create('branch_return_outbounds', function (Blueprint $table) {
            $table->id();
            $table->string('toko_tujuan')->nullable();
            $table->string('nomor_sj')->nullable();
            $table->integer('total_qty')->nullable();
            $table->string('disiapkan_oleh')->nullable();
            $table->time('jam_naik')->nullable();
            $table->string('diserahkan_kepada')->nullable();
            $table->string('status')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_return_outbounds');
    }
};
