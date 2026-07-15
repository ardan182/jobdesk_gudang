<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_terima_suppliers', function (Blueprint $table) {
            $table->time('jam_datang')->nullable()->after('no_po_referensi');
            $table->time('selesai_bongkar')->nullable()->after('jam_bongkar');
        });
    }

    public function down(): void
    {
        Schema::table('task_terima_suppliers', function (Blueprint $table) {
            $table->dropColumn(['jam_datang', 'selesai_bongkar']);
        });
    }
};
