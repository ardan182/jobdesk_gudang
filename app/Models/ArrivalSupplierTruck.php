<?php

namespace App\Models;

use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArrivalSupplierTruck extends Model
{
    protected $table = 'arrival_supplier_trucks';

    protected $fillable = [
        'id_task',
        'supplier_id',
        'expedition_id',
        'nama_sopir',
        'tanggal_datang',
        'no_plat_mobil',
        'jam_datang',
        'jam_selesai',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_datang' => 'date',
        'jam_datang' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id_task)) {
                $model->id_task = TaskIdGenerator::generate('datang_mobil_supplier');
            }
            if (empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
        });

        static::created(function ($model) {
            ActivityLog::create([
                'user_id' => $model->user_id,
                'module' => 'Datang Mobil Supplier',
                'id_task' => $model->id_task,
                'description' => "Supplier: {$model->supplier?->nama_supplier} - Plat: {$model->no_plat_mobil}",
                'reference' => $model->nama_sopir,
                'action' => 'create',
            ]);
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function expedition(): BelongsTo
    {
        return $this->belongsTo(Expedition::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
