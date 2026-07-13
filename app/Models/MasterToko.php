<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterToko extends Model
{
    protected $table = 'master_tokos';

    protected $fillable = [
        'nama_toko',
        'alamat',
    ];
}
