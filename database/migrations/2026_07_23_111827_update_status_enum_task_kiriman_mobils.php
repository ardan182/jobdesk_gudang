<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE task_kiriman_mobils MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'draft'");
        DB::table('task_kiriman_mobils')->where('status', 'datang')->update(['status' => 'selesai']);
        DB::statement("ALTER TABLE task_kiriman_mobils MODIFY COLUMN status ENUM('draft', 'dalam pengiriman', 'selesai') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE task_kiriman_mobils MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'draft'");
    }
};
