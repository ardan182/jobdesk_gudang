<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterMobil extends Model
{
    protected $table = 'master_mobils';

    protected $fillable = [
        'nama_mobil',
        'no_plat_mobil',
    ];
}
