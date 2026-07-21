<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_sjs', function (Blueprint $table) {
            $table->string('id_task', 30)->nullable()->after('id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('supplier_sjs', function (Blueprint $table) {
            $table->dropColumn('id_task');
        });
    }
};
