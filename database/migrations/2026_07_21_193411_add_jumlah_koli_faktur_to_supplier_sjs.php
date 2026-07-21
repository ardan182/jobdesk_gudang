<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_sjs', function (Blueprint $table) {
            $table->integer('jumlah_koli')->nullable()->after('nomor_po_referensi');
            $table->integer('jumlah_faktur')->nullable()->after('jumlah_koli');
        });
    }

    public function down(): void
    {
        Schema::table('supplier_sjs', function (Blueprint $table) {
            $table->dropColumn(['jumlah_koli', 'jumlah_faktur']);
        });
    }
};
