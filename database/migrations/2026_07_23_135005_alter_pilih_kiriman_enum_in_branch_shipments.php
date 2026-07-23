<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE branch_shipments MODIFY COLUMN pilih_kiriman ENUM('pembagian_po', 'stock_gudang', 'rb_pesanan') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE branch_shipments MODIFY COLUMN pilih_kiriman ENUM('pembagian_po', 'stock_gudang') NOT NULL");
    }
};
