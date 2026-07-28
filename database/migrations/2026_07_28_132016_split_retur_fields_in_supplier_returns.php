<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_returns', function (Blueprint $table) {
            $table->string('jenis_retur_masuk')->nullable()->after('jenis_retur');
            $table->integer('total_kolian_masuk')->nullable()->after('total_kolian');
            $table->renameColumn('jenis_retur', 'jenis_retur_keluar');
            $table->renameColumn('total_koli', 'total_koli_keluar');
        });

        // Migrate data existing
        DB::table('supplier_returns')->where('jenis_pengiriman', 'retur_masuk')->update([
            'jenis_retur_masuk' => DB::raw('jenis_retur_keluar'),
            'total_kolian_masuk' => DB::raw('total_koli_keluar'),
            'jenis_retur_keluar' => null,
            'total_koli_keluar' => null,
        ]);

        DB::table('supplier_returns')->where('jenis_pengiriman', 'datang_dan_keluar')->update([
            'jenis_retur_masuk' => 'servis',
        ]);
    }

    public function down(): void
    {
        DB::table('supplier_returns')->where('jenis_pengiriman', 'retur_masuk')->update([
            'jenis_retur_keluar' => DB::raw('jenis_retur_masuk'),
            'total_koli_keluar' => DB::raw('total_kolian_masuk'),
        ]);

        Schema::table('supplier_returns', function (Blueprint $table) {
            $table->dropColumn('jenis_retur_masuk');
            $table->dropColumn('total_kolian_masuk');
            $table->renameColumn('jenis_retur_keluar', 'jenis_retur');
            $table->renameColumn('total_koli_keluar', 'total_koli');
        });
    }
};
