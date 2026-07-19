<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah FK ke arrival_supplier_trucks
        Schema::table('task_terima_suppliers', function (Blueprint $table) {
            $table->foreignId('arrival_supplier_truck_id')
                ->nullable()
                ->after('id_task')
                ->constrained('arrival_supplier_trucks')
                ->cascadeOnDelete();
        });

        // 2. Migrate data lama → selesai, lalu ubah enum
        DB::table('task_terima_suppliers')
            ->whereIn('status', ['komplit', 'kurang', 'lebih'])
            ->update(['status' => 'selesai']);

        DB::statement("ALTER TABLE task_terima_suppliers MODIFY COLUMN status ENUM('selesai', 'selesai_retur') NULL DEFAULT NULL");

        // 3. Tabel pivot helpers
        Schema::create('task_terima_supplier_helpers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_terima_supplier_id')
                ->constrained('task_terima_suppliers')
                ->cascadeOnDelete();
            $table->foreignId('warehouse_employee_id')
                ->constrained('warehouse_employees')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['task_terima_supplier_id', 'warehouse_employee_id'], 'tth_uniq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_terima_supplier_helpers');

        DB::statement("ALTER TABLE task_terima_suppliers MODIFY COLUMN status ENUM('komplit', 'kurang', 'lebih') NOT NULL DEFAULT 'komplit'");

        Schema::table('task_terima_suppliers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('arrival_supplier_truck_id');
        });
    }
};
