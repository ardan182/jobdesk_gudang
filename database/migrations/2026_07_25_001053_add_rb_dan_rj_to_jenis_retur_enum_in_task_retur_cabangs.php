<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE task_retur_cabangs MODIFY COLUMN jenis_retur VARCHAR(50) NULL");
        DB::statement("ALTER TABLE task_retur_cabangs MODIFY COLUMN jenis_retur ENUM('retur_jelek','retur_bagus','rb_dan_rj') NULL");
    }

    public function down(): void
    {
        DB::statement("UPDATE task_retur_cabangs SET jenis_retur = 'retur_bagus' WHERE jenis_retur = 'rb_dan_rj'");
        DB::statement("ALTER TABLE task_retur_cabangs MODIFY COLUMN jenis_retur ENUM('retur_jelek','retur_bagus') NULL");
    }
};
