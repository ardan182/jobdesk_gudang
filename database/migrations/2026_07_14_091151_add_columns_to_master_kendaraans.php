<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_kendaraans', function (Blueprint $table) {
            $table->date('masa_berlaku_stnk')->nullable()->after('no_kir');
            $table->date('masa_berlaku_kir')->nullable()->after('masa_berlaku_stnk');
            $table->text('keterangan')->nullable()->after('masa_berlaku_kir');
        });
    }

    public function down(): void
    {
        Schema::table('master_kendaraans', function (Blueprint $table) {
            $table->dropColumn(['masa_berlaku_stnk', 'masa_berlaku_kir', 'keterangan']);
        });
    }
};
