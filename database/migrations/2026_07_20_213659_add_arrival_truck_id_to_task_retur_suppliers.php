<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_retur_suppliers', function (Blueprint $table) {
            $table->foreignId('arrival_supplier_truck_id')
                ->nullable()
                ->after('id_task')
                ->constrained('arrival_supplier_trucks')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('task_retur_suppliers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('arrival_supplier_truck_id');
        });
    }
};
