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
        Schema::table('task_kiriman_mobils', function (Blueprint $table) {
            $table->date('tanggal_kirim')->nullable()->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('task_kiriman_mobils', function (Blueprint $table) {
            $table->dropColumn('tanggal_kirim');
        });
    }
};
