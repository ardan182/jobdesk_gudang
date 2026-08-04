<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('supplier_sjs', function (Blueprint $table) {
            $table->text('catatan')->nullable()->after('keterangan');
        });

        DB::table('supplier_sjs')
            ->where('keterangan', 'LIKE', '%Auto dari Terima Supplier%')
            ->whereNull('catatan')
            ->update(['catatan' => DB::raw('keterangan')]);

        DB::table('supplier_sjs')
            ->where('keterangan', 'LIKE', '%Auto dari Terima Supplier%')
            ->update(['keterangan' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('supplier_sjs')
            ->where('catatan', 'LIKE', '%Auto dari Terima Supplier%')
            ->whereNull('keterangan')
            ->update(['keterangan' => DB::raw('catatan')]);

        Schema::table('supplier_sjs', function (Blueprint $table) {
            $table->dropColumn('catatan');
        });
    }
};
