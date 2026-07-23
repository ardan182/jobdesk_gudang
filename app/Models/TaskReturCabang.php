<?php

namespace App\Models;

use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TaskReturCabang extends Model
{
    protected $fillable = [
        'id_task',
        'cabang',
        'no_plat_mobil',
        'jam_tiba',
        'jenis_retur',
        'tanggal_bongkar',
        'no_sj_retur',
        'total_qty',
        'jam_bongkar',
        'nama_sopir',
        'helpers',
        'status',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'jam_bongkar' => 'datetime:H:i',
        'jam_tiba' => 'datetime:H:i',
        'tanggal_bongkar' => 'date:Y-m-d',
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
            $tracked = ['cabang', 'no_plat_mobil', 'jam_tiba', 'jenis_retur', 'tanggal_bongkar', 'no_sj_retur', 'total_qty', 'jam_bongkar', 'nama_sopir', 'status', 'keterangan'];
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

    public function helpers(): BelongsToMany
    {
        return $this->belongsToMany(WarehouseEmployee::class, 'task_retur_cabang_employee');
    }
}
