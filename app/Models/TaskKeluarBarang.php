<?php

namespace App\Models;

use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskKeluarBarang extends Model
{
    protected $fillable = [
        'id_task',
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
            if (empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
        });

        static::created(function ($model) {
            ActivityLog::create([
                'user_id' => $model->user_id,
                'module' => 'Keluar Barang',
                'id_task' => $model->id_task,
                'description' => "Toko: {$model->toko_tujuan} → {$model->status}",
                'reference' => $model->no_referensi_sj,
                'action' => 'create',
            ]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
