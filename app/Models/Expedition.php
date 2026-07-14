<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expedition extends Model
{
    protected $table = 'expeditions';

    protected $fillable = [
        'nama_ekspedisi',
        'no_telepon',
        'alamat',
    ];
}
