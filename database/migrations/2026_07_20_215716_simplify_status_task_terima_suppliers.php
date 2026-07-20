<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE task_terima_suppliers MODIFY COLUMN status VARCHAR(30) NULL DEFAULT 'DRAFT'");

        DB::table('task_terima_suppliers')
            ->whereIn('status', ['selesai_tanpa_retur', 'selesai_ada_retur'])
            ->update(['status' => 'SELESAI']);

        DB::table('task_terima_suppliers')
            ->where('status', 'draft')
            ->update(['status' => 'DRAFT']);

        DB::statement("ALTER TABLE task_terima_suppliers MODIFY COLUMN status ENUM('DRAFT', 'SELESAI') NULL DEFAULT 'DRAFT'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE task_terima_suppliers MODIFY COLUMN status VARCHAR(30) NULL DEFAULT NULL");
    }
};
