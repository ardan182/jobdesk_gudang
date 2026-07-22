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
        Schema::table('task_keluar_barangs', function (Blueprint $table) {
            $table->string('cabang')->nullable();
            $table->string('nomor_sj', 100)->nullable();
            $table->integer('total_qty')->nullable();
            $table->string('no_po', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('task_keluar_barangs', function (Blueprint $table) {
            $table->dropColumn(['cabang', 'nomor_sj', 'total_qty', 'no_po']);
        });
    }
};
