<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('task_terima_suppliers')
            ->where('status', 'selesai')
            ->update(['status' => 'selesai_tanpa_retur']);

        DB::table('task_terima_suppliers')
            ->where('status', 'selesai_retur')
            ->update(['status' => 'selesai_ada_retur']);

        DB::statement("ALTER TABLE task_terima_suppliers MODIFY COLUMN status ENUM('selesai_tanpa_retur', 'selesai_ada_retur') NULL DEFAULT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE task_terima_suppliers MODIFY COLUMN status VARCHAR(30) NULL DEFAULT NULL");
    }
};
