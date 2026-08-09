<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_retur_cabangs', function (Blueprint $table) {
            $table->string('no_plat_mobil')->nullable()->change();
            $table->time('jam_tiba')->nullable()->change();
            $table->string('nama_sopir')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('task_retur_cabangs', function (Blueprint $table) {
            $table->string('no_plat_mobil')->nullable(false)->change();
            $table->time('jam_tiba')->nullable(false)->change();
            $table->string('nama_sopir')->nullable(false)->change();
        });
    }
};