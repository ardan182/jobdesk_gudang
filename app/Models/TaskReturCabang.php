<?php

namespace App\Models;

use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskReturCabang extends Model
{
    protected $fillable = [
        'id_task',
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
            if (empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
        });

        static::created(function ($model) {
            ActivityLog::create([
                'user_id' => $model->user_id,
                'module' => 'Retur Cabang',
                'id_task' => $model->id_task,
                'description' => "Cabang: {$model->cabang} → {$model->jenis_retur}",
                'reference' => $model->no_sj_retur,
                'action' => 'create',
            ]);
        });

        static::updated(function ($model) {
            $changes = [];
            $tracked = ['cabang', 'jenis_retur', 'no_sj_retur', 'total_kolian', 'jam_bongkar', 'nama_sopir', 'keterangan'];
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
                'module' => 'Retur Cabang',
                'id_task' => $model->id_task,
                'description' => "Cabang: {$model->cabang} — " . implode('; ', $changes),
                'reference' => $model->no_sj_retur,
                'action' => 'update',
            ]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
