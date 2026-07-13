<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE task_keluar_barangs MODIFY toko_tujuan VARCHAR(255) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE task_keluar_barangs MODIFY toko_tujuan ENUM('pusat','ujungberung','soreang','majalaya','cicaheum','barokah') NOT NULL");
    }
};
