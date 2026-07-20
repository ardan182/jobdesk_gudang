<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE arrival_supplier_trucks MODIFY COLUMN status ENUM('MENGANTRI', 'PROSES', 'SELESAI') NOT NULL DEFAULT 'MENGANTRI'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE arrival_supplier_trucks MODIFY COLUMN status VARCHAR(20) NOT NULL DEFAULT 'PROSES'");
    }
};
