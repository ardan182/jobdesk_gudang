<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_documents', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dokumen');
            $table->string('kategori');
            $table->string('versi')->default('v1.0');
            $table->string('file_path');
            $table->string('format_file');
            $table->text('deskripsi')->nullable();
            $table->integer('download_count')->default(0);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_documents');
    }
};
