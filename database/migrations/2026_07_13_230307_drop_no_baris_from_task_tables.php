<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'task_retur_suppliers',
            'task_retur_cabangs',
            'task_terima_suppliers',
            'task_keluar_barangs',
            'task_kiriman_mobils',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('no_baris');
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'task_retur_suppliers' => 'task_retur_suppliers',
            'task_retur_cabangs' => 'task_retur_cabangs',
            'task_terima_suppliers' => 'task_terima_suppliers',
            'task_keluar_barangs' => 'task_keluar_barangs',
            'task_kiriman_mobils' => 'task_kiriman_mobils',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->integer('no_baris');
            });
        }
    }
};
