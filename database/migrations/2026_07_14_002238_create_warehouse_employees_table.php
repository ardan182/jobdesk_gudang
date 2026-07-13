<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_employees', function (Blueprint $table) {
            $table->id();
            $table->string('nama_karyawan');
            $table->string('no_whatsapp')->nullable();
            $table->string('divisi_gudang');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_employees');
    }
};
