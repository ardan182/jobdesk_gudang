<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSopir extends Model
{
    protected $table = 'master_sopirs';

    protected $fillable = [
        'nama_sopir',
    ];
}
