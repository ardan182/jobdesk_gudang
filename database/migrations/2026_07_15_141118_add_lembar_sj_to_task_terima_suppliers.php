<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_terima_suppliers', function (Blueprint $table) {
            $table->integer('lembar_sj')->nullable()->default(1)->after('selesai_bongkar');
        });
    }

    public function down(): void
    {
        Schema::table('task_terima_suppliers', function (Blueprint $table) {
            $table->dropColumn('lembar_sj');
        });
    }
};
