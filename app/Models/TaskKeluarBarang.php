<?php

namespace App\Models;

use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskKeluarBarang extends Model
{
    protected $fillable = [
        'id_task',
        'branch_shipment_id',
        'cabang',
        'nomor_sj',
        'total_qty',
        'no_po',
        'jam_disiapkan',
        'diserahkan_kepada',
        'helper',
        'status',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'jam_disiapkan' => 'datetime:H:i',
        'total_qty' => 'integer',
        'helper' => 'array',
    ];

    protected $attributes = [
        'status' => 'draft',
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
                'description' => "Cabang: {$model->cabang} → {$model->status}",
                'reference' => $model->nomor_sj,
                'action' => 'create',
            ]);
        });

        static::updated(function ($model) {
            $changes = [];
            $tracked = ['status', 'jam_disiapkan', 'diserahkan_kepada', 'helper', 'keterangan'];
            foreach ($tracked as $field) {
                $old = $model->getOriginal($field);
                $new = $model->$field;
                $oldStr = is_object($old) ? (string) $old : ($old ?? '-');
                $newStr = is_object($new) ? (string) $new : ($new ?? '-');
                if ($oldStr !== $newStr) {
                    $changes[] = "$field: $oldStr → $newStr";
                }
            }
            if (empty($changes)) return;

            ActivityLog::create([
                'user_id' => auth()->id() ?? $model->user_id,
                'module' => 'Keluar Barang',
                'id_task' => $model->id_task,
                'description' => "Cabang: {$model->cabang} — " . implode('; ', $changes),
                'reference' => $model->nomor_sj,
                'action' => 'update',
            ]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branchShipment(): BelongsTo
    {
        return $this->belongsTo(BranchShipment::class);
    }
}
