<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE task_kiriman_mobils MODIFY COLUMN retur_option VARCHAR(50) NULL AFTER status");
        DB::statement("UPDATE task_kiriman_mobils SET retur_option = 'ada_rb' WHERE retur_option = 'ada_retur'");
        DB::statement("ALTER TABLE task_kiriman_mobils MODIFY COLUMN retur_option ENUM('tidak_ada_retur','ada_rb','ada_rj','rb_dan_rj') NULL AFTER status");
    }

    public function down(): void
    {
        DB::statement("UPDATE task_kiriman_mobils SET retur_option = 'ada_retur' WHERE retur_option IN ('ada_rb','ada_rj','rb_dan_rj')");
        DB::statement("ALTER TABLE task_kiriman_mobils MODIFY COLUMN retur_option ENUM('tidak_ada_retur','ada_retur') NULL AFTER status");
    }
};
