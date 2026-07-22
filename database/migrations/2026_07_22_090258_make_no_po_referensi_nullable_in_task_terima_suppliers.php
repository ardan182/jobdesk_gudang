<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_terima_suppliers', function (Blueprint $table) {
            $table->string('no_po_referensi', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('task_terima_suppliers', function (Blueprint $table) {
            $table->string('no_po_referensi', 100)->nullable(false)->change();
        });
    }
};
