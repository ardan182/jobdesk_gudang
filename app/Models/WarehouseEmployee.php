<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseEmployee extends Model
{
    protected $table = 'warehouse_employees';

    protected $fillable = [
        'nama_karyawan',
        'no_whatsapp',
        'divisi_gudang',
    ];
}
