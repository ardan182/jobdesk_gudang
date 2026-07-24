<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_retur_cabangs', function (Blueprint $table) {
            $table->string('no_sj_retur')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('task_retur_cabangs', function (Blueprint $table) {
            $table->string('no_sj_retur')->nullable(false)->change();
        });
    }
};
