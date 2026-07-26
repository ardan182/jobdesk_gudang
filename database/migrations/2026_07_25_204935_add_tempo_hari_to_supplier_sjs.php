<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_sjs', function (Blueprint $table) {
            $table->integer('tempo_hari')->nullable()->after('tanggal_input');
        });
    }

    public function down(): void
    {
        Schema::table('supplier_sjs', function (Blueprint $table) {
            $table->dropColumn('tempo_hari');
        });
    }
};
