<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseLeave extends Model
{
    protected $table = 'warehouse_leaves';

    protected $fillable = [
        'warehouse_employee_id',
        'jenis_absen',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date:Y-m-d',
        'tanggal_selesai' => 'date:Y-m-d',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(WarehouseEmployee::class, 'warehouse_employee_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
