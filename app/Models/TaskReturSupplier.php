<?php

namespace App\Models;

use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskReturSupplier extends Model
{
    protected $fillable = [
        'id_task',
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
            if (empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
        });

        static::created(function ($model) {
            ActivityLog::create([
                'user_id' => $model->user_id,
                'module' => 'Retur Supplier',
                'id_task' => $model->id_task,
                'description' => "Supplier: {$model->nama_supplier_ekspedisi} → {$model->status}",
                'reference' => $model->no_plat_mobil,
                'action' => 'create',
            ]);
        });

        static::updated(function ($model) {
            $changes = [];
            $tracked = ['status', 'nama_supplier_ekspedisi', 'no_plat_mobil', 'nama_sopir', 'jam_muat', 'jumlah_kolian', 'admin_sj_retur', 'keterangan'];
            foreach ($tracked as $field) {
                $old = $model->getOriginal($field);
                $new = $model->$field;
                $oldStr = $old ?? '-';
                $newStr = $new ?? '-';
                if ($oldStr !== $newStr) {
                    $changes[] = "$field: $oldStr → $newStr";
                }
            }
            if (empty($changes)) return;

            ActivityLog::create([
                'user_id' => auth()->id() ?? $model->user_id,
                'module' => 'Retur Supplier',
                'id_task' => $model->id_task,
                'description' => "Supplier: {$model->nama_supplier_ekspedisi} — " . implode('; ', $changes),
                'reference' => $model->no_plat_mobil,
                'action' => 'update',
            ]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
