<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_retur_cabangs', function (Blueprint $table) {
            $table->dropColumn('total_qty');
            $table->integer('jumlah_sj')->nullable()->after('jam_bongkar');
        });
    }

    public function down(): void
    {
        Schema::table('task_retur_cabangs', function (Blueprint $table) {
            $table->dropColumn('jumlah_sj');
            $table->integer('total_kolian')->nullable()->after('jam_bongkar');
        });
    }
};
