<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskKeluarBarang extends Model
{
    use LogsActivity;

    protected $fillable = [
        'id_task',
        'branch_shipment_id',
        'cabang',
        'nomor_sj',
        'total_qty',
        'qty_checker',
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
        'qty_checker' => 'integer',
        'helper' => 'array',
    ];

    protected $attributes = [
        'status' => 'draft',
    ];

    protected static function activityModule(): string { return 'task_keluar_barangs'; } protected static function activitySummaryAttributes(): array { return ['cabang', 'nomor_sj']; } protected static function activityReferenceField(): ?string { return 'nomor_sj'; } protected static function activityTracked(): ?array { return ['status', 'jam_disiapkan', 'diserahkan_kepada', 'helper', 'keterangan', 'qty_checker']; } protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id_task)) {
                $model->id_task = TaskIdGenerator::generate('keluar_barang');
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

    public function branchShipment(): BelongsTo
    {
        return $this->belongsTo(BranchShipment::class);
    }
}
