<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_retur_cabangs', function (Blueprint $table) {
            $table->foreignId('kiriman_mobil_id')
                ->nullable()
                ->constrained('task_kiriman_mobils')
                ->nullOnDelete();
            $table->index('kiriman_mobil_id');
        });

        // Backfill: isi kiriman_mobil_id untuk data lama yang cocok UNIK (cabang + no_plat_mobil)
        // ke tepat satu kiriman berstatus selesai & retur_option ada_retur.
        $returs = DB::table('task_retur_cabangs')
            ->whereNull('kiriman_mobil_id')
            ->whereNotNull('cabang')
            ->whereNotNull('no_plat_mobil')
            ->get(['id', 'cabang', 'no_plat_mobil']);

        foreach ($returs as $retur) {
            $matches = DB::table('task_kiriman_mobils')
                ->where('cabang', $retur->cabang)
                ->where('no_plat_mobil', $retur->no_plat_mobil)
                ->where('status', 'selesai')
                ->whereIn('retur_option', ['ada_retur'])
                ->pluck('id');

            if ($matches->count() === 1) {
                DB::table('task_retur_cabangs')
                    ->where('id', $retur->id)
                    ->update(['kiriman_mobil_id' => $matches->first()]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('task_retur_cabangs', function (Blueprint $table) {
            $table->dropIndex(['kiriman_mobil_id']);
            $table->dropForeign(['kiriman_mobil_id']);
            $table->dropColumn('kiriman_mobil_id');
        });
    }
};