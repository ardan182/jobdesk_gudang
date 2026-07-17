<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierReturnInbound extends Model
{
    protected $table = 'supplier_return_inbounds';

    protected $fillable = [
        'nama_supplier',
        'nama_ekspedisi',
        'nama_supir',
        'no_plat_mobil',
        'tanggal_datang',
        'jam_kedatangan',
        'no_nota_retur',
        'jumlah_kolian',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_datang' => 'date',
    ];
}
