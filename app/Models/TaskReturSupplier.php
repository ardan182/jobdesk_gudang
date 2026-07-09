<?php

namespace App\Models;

use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskReturSupplier extends Model
{
    protected $fillable = [
        'id_task',
        'no_baris',
        'nama_supplier_ekspedisi',
        'no_plat_mobil',
        'nama_sopir',
        'jam_muat',
        'jumlah_kolian',
        'admin_sj_retur',
        'status',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'jam_muat' => 'datetime:H:i',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id_task)) {
                $model->id_task = TaskIdGenerator::generate('retur_supplier');
            }
            if (empty($model->no_baris)) {
                $model->no_baris = TaskIdGenerator::getNextBaris('retur_supplier');
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
