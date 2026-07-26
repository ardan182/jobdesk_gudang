<?php

use App\Models\KendaraanDokumen;
use App\Models\MasterKendaraan;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        MasterKendaraan::each(function ($kendaraan) {
            if (!KendaraanDokumen::where('master_kendaraan_id', $kendaraan->id)->exists()) {
                $kendaraan->createDokumenRecords();
            }
        });
    }

    public function down(): void
    {
        KendaraanDokumen::truncate();
    }
};
