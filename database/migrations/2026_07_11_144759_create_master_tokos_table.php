<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_tokos', function (Blueprint $table) {
            $table->id();
            $table->string('nama_toko');
            $table->text('alamat')->nullable();
            $table->timestamps();
        });

        DB::table('master_tokos')->insert([
            ['nama_toko' => 'Pusat', 'alamat' => ''],
            ['nama_toko' => 'Ujungberung', 'alamat' => ''],
            ['nama_toko' => 'Soreang', 'alamat' => ''],
            ['nama_toko' => 'Majalaya', 'alamat' => ''],
            ['nama_toko' => 'Cicaheum', 'alamat' => ''],
            ['nama_toko' => 'Barokah', 'alamat' => ''],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('master_tokos');
    }
};
