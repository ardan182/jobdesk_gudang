<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tv_board_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('refresh_interval')->default(60);
            $table->integer('max_items')->default(15);
            $table->boolean('show_supplier_arrivals')->default(true);
            $table->boolean('show_branch_deliveries')->default(true);
            $table->boolean('show_shipment_sj')->default(true);
            $table->boolean('show_supplier_invoices')->default(true);
            $table->string('marquee_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv_board_settings');
    }
};
