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
        Schema::table('arrival_supplier_trucks', function (Blueprint $table) {
            $table->string('jenis_kiriman', 20)->default('DATANG')->after('no_plat_mobil');
            $table->string('status', 20)->default('PROSES')->after('jam_selesai');
        });
    }

    public function down(): void
    {
        Schema::table('arrival_supplier_trucks', function (Blueprint $table) {
            $table->dropColumn(['jenis_kiriman', 'status']);
        });
    }
};
