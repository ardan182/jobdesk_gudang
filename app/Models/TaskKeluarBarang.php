<?php

namespace App\Models;

use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskKeluarBarang extends Model
{
    protected $fillable = [
        'id_task',
        'no_baris',
        'toko_tujuan',
        'supplier',
        'no_referensi_sj',
        'jumlah_kolian',
        'jam_naik',
        'nama_koordinator',
        'status',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'jam_naik' => 'datetime:H:i',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id_task)) {
                $model->id_task = TaskIdGenerator::generate('keluar_barang');
            }
            if (empty($model->no_baris)) {
                $model->no_baris = TaskIdGenerator::getNextBaris('keluar_barang');
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
