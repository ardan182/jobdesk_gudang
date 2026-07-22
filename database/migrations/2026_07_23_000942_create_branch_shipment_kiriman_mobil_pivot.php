<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_shipment_kiriman_mobil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_kiriman_mobil_id')->constrained('task_kiriman_mobils')->cascadeOnDelete();
            $table->foreignId('branch_shipment_id')->constrained('branch_shipments')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['task_kiriman_mobil_id', 'branch_shipment_id'], 'kiriman_mobil_sj_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_shipment_kiriman_mobil');
    }
};
