<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_retur_cabangs', function (Blueprint $table) {
            $table->dropColumn('jumlah_sj');
            $table->integer('jumlah_sj_bagus')->nullable()->after('jam_bongkar');
            $table->text('catatan_bagus')->nullable()->after('jumlah_sj_bagus');
            $table->integer('jumlah_sj_jelek')->nullable()->after('catatan_bagus');
            $table->text('catatan_jelek')->nullable()->after('jumlah_sj_jelek');
        });
    }

    public function down(): void
    {
        Schema::table('task_retur_cabangs', function (Blueprint $table) {
            $table->dropColumn(['jumlah_sj_bagus', 'catatan_bagus', 'jumlah_sj_jelek', 'catatan_jelek']);
            $table->integer('jumlah_sj')->nullable()->after('jam_bongkar');
        });
    }
};
