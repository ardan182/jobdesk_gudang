<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierSj extends Model
{
    protected $table = 'supplier_sjs';

    protected $fillable = [
        'nama_supplier',
        'tanggal_datang',
        'nomor_po_referensi',
        'status_input',
        'tanggal_input',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_datang' => 'date',
        'tanggal_input' => 'date',
    ];
}
