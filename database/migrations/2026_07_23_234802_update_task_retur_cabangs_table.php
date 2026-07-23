<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_retur_cabangs', function (Blueprint $table) {
            $table->string('no_plat_mobil', 20)->nullable()->after('cabang');
            $table->time('jam_tiba')->nullable()->after('no_plat_mobil');
            $table->date('tanggal_bongkar')->nullable()->after('jenis_retur');
            $table->renameColumn('total_kolian', 'total_qty');
            $table->enum('status', ['draft', 'selesai'])->default('draft')->after('keterangan');
        });

        Schema::create('task_retur_cabang_employee', function (Blueprint $table) {
            $table->foreignId('task_retur_cabang_id')->constrained('task_retur_cabangs')->cascadeOnDelete();
            $table->foreignId('warehouse_employee_id')->constrained('warehouse_employees')->cascadeOnDelete();
            $table->primary(['task_retur_cabang_id', 'warehouse_employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_retur_cabang_employee');

        Schema::table('task_retur_cabangs', function (Blueprint $table) {
            $table->dropColumn(['no_plat_mobil', 'jam_tiba', 'tanggal_bongkar', 'status']);
            $table->renameColumn('total_qty', 'total_kolian');
        });
    }
};
