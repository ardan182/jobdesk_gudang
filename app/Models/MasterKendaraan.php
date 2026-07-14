<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKendaraan extends Model
{
    protected $table = 'master_kendaraans';

    protected $fillable = [
        'nomor_polisi',
        'jenis_kendaraan',
        'merek_dan_model',
        'nomor_rangka',
        'nomor_mesin',
        'no_stnk',
        'no_kir',
        'masa_berlaku_stnk',
        'masa_berlaku_kir',
        'keterangan',
    ];
}
