<?php

namespace App\Models;

use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskTerimaSupplier extends Model
{
    protected $fillable = [
        'id_task',
        'no_baris',
        'nama_supplier_ekspedisi',
        'no_po_referensi',
        'jumlah_kolian',
        'jam_bongkar',
        'nama_sopir',
        'status',
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
                $model->id_task = TaskIdGenerator::generate('terima_supplier');
            }
            if (empty($model->no_baris)) {
                $model->no_baris = TaskIdGenerator::getNextBaris('terima_supplier');
            }
            if (empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
        });

        static::created(function ($model) {
            ActivityLog::create([
                'user_id' => $model->user_id,
                'module' => 'Terima Supplier',
                'id_task' => $model->id_task,
                'description' => "Supplier: {$model->nama_supplier_ekspedisi} → {$model->status}",
                'reference' => $model->no_po_referensi,
                'action' => 'create',
            ]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
