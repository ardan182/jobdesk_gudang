<?php

namespace App\Models;

use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TaskKirimanMobil extends Model
{
    protected $fillable = [
        'id_task',
        'cabang',
        'no_plat_mobil',
        'jam_muat',
        'jam_selesai_muat',
        'jam_berangkat',
        'jam_tiba',
        'tanggal_kirim',
        'status',
        'retur_option',
        'nama_supir',
        'keterangan',
        'keluar_barang_id',
        'user_id',
    ];

    protected $casts = [
        'jam_muat' => 'datetime:H:i',
        'jam_selesai_muat' => 'datetime:H:i',
        'jam_berangkat' => 'datetime:H:i',
        'jam_tiba' => 'datetime:H:i',
        'tanggal_kirim' => 'date:Y-m-d',
    ];

    protected $attributes = [
        'status' => 'draft',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id_task)) {
                $model->id_task = TaskIdGenerator::generate('kiriman_mobil');
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
                'description' => "Cabang: {$model->cabang}" . ($model->no_plat_mobil ? " - Plat: {$model->no_plat_mobil}" : ''),
                'reference' => $model->nama_supir ?? '-',
                'action' => 'create',
            ]);
        });

        static::updated(function ($model) {
            $changes = [];
            $tracked = ['cabang', 'no_plat_mobil', 'jam_muat', 'jam_selesai_muat', 'jam_berangkat', 'jam_tiba', 'status', 'nama_supir', 'keterangan'];
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
                'module' => 'Kiriman Mobil',
                'id_task' => $model->id_task,
                'description' => "Cabang: {$model->cabang} — " . implode('; ', $changes),
                'reference' => $model->nama_supir ?? '-',
                'action' => 'update',
            ]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function keluarBarang(): BelongsTo
    {
        return $this->belongsTo(TaskKeluarBarang::class);
    }

    public function branchShipments(): BelongsToMany
    {
        return $this->belongsToMany(BranchShipment::class, 'branch_shipment_kiriman_mobil');
    }
}
