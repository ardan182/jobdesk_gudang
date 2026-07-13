<?php

namespace App\Models;

use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskKirimanMobil extends Model
{
    protected $fillable = [
        'id_task',
        'no_baris',
        'cabang',
        'no_plat_mobil',
        'jam_muat',
        'jam_selesai_muat',
        'jam_berangkat',
        'nama_supir',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'jam_muat' => 'datetime:H:i',
        'jam_selesai_muat' => 'datetime:H:i',
        'jam_berangkat' => 'datetime:H:i',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id_task)) {
                $model->id_task = TaskIdGenerator::generate('kiriman_mobil');
            }
            if (empty($model->no_baris)) {
                $model->no_baris = TaskIdGenerator::getNextBaris('kiriman_mobil');
            }
            if (empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
        });

        static::created(function ($model) {
            ActivityLog::create([
                'user_id' => $model->user_id,
                'module' => 'Kiriman Mobil',
                'id_task' => $model->id_task,
                'description' => "Cabang: {$model->cabang} - Plat: {$model->no_plat_mobil}",
                'action' => 'create',
            ]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
