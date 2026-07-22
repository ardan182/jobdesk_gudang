<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_kiriman_mobils', function (Blueprint $table) {
            $table->time('jam_tiba')->nullable();
        });

        DB::statement("ALTER TABLE task_kiriman_mobils ADD COLUMN status ENUM('draft','dalam pengiriman','datang') NOT NULL DEFAULT 'draft' AFTER jam_berangkat");
    }

    public function down(): void
    {
        Schema::table('task_kiriman_mobils', function (Blueprint $table) {
            $table->dropColumn(['jam_tiba', 'status']);
        });
    }
};
