<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE task_terima_suppliers MODIFY COLUMN status ENUM('draft', 'selesai_tanpa_retur', 'selesai_ada_retur') NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE task_terima_suppliers MODIFY COLUMN status ENUM('selesai_tanpa_retur', 'selesai_ada_retur') NULL DEFAULT NULL");
    }
};
