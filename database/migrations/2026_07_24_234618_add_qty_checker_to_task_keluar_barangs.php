<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_keluar_barangs', function (Blueprint $table) {
            $table->integer('qty_checker')->nullable()->after('total_qty');
        });
    }

    public function down(): void
    {
        Schema::table('task_keluar_barangs', function (Blueprint $table) {
            $table->dropColumn('qty_checker');
        });
    }
};
