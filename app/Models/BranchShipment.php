<?php

namespace App\Models;

use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchShipment extends Model
{
    protected $fillable = [
        'id_task',
        'pilih_kiriman',
        'cabang',
        'nomor_sj',
        'total_qty',
        'no_po',
        'tanggal_buat',
        'status',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_buat' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id_task)) {
                $model->id_task = TaskIdGenerator::generate('branch_shipment');
            }
            if (empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
