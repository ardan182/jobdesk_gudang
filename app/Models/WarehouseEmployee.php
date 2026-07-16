<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseEmployee extends Model
{
    protected $table = 'warehouse_employees';

    protected $fillable = [
        'nama_karyawan',
        'no_whatsapp',
        'division_id',
    ];

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }
}
