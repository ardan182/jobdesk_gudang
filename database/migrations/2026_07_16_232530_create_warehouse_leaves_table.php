<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_employee_id')->constrained()->restrictOnDelete();
            $table->enum('jenis_absen', ['Cuti', 'Sakit', 'Izin']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->index(['warehouse_employee_id', 'tanggal_mulai', 'tanggal_selesai'], 'wl_emp_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_leaves');
    }
};
