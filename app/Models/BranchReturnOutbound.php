<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchReturnOutbound extends Model
{
    protected $table = 'branch_return_outbounds';

    protected $fillable = [
        'toko_tujuan',
        'nomor_sj',
        'total_qty',
        'disiapkan_oleh',
        'jam_naik',
        'diserahkan_kepada',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'jam_naik' => 'datetime:H:i',
    ];
}
