<?php

use App\Models\KendaraanDokumen;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_kendaraans', function (Blueprint $table) {
            $table->date('stnk_5_tahun_sampai')->nullable()->after('masa_berlaku_kir');
        });

        KendaraanDokumen::where('jenis', 'stnk')->whereNull('periode')->update(['periode' => '1_tahun']);
    }

    public function down(): void
    {
        Schema::table('master_kendaraans', function (Blueprint $table) {
            $table->dropColumn('stnk_5_tahun_sampai');
        });
    }
};
