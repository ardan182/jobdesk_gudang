<?php

namespace App\Models;

use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskReturCabang extends Model
{
    protected $fillable = [
        'id_task',
        'no_baris',
        'cabang',
        'jenis_retur',
        'no_sj_retur',
        'total_kolian',
        'jam_bongkar',
        'nama_sopir',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'jam_bongkar' => 'datetime:H:i',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id_task)) {
                $model->id_task = TaskIdGenerator::generate('retur_cabang');
            }
            if (empty($model->no_baris)) {
                $model->no_baris = TaskIdGenerator::getNextBaris('retur_cabang');
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
