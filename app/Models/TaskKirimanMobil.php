<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskKirimanMobil extends Model
{
    use LogsActivity;

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

    protected static function activityModule(): string { return 'task_kiriman_mobils'; } protected static function activitySummaryAttributes(): array { return ['cabang', 'no_plat_mobil']; } protected static function activityReferenceField(): ?string { return 'nama_supir'; } protected static function activityTracked(): ?array { return ['cabang', 'no_plat_mobil', 'jam_muat', 'jam_selesai_muat', 'jam_berangkat', 'jam_tiba', 'status', 'nama_supir', 'keterangan']; } protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id_task)) {
                $model->id_task = TaskIdGenerator::generate('kiriman_mobil');
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

    public function keluarBarang(): BelongsTo
    {
        return $this->belongsTo(TaskKeluarBarang::class);
    }

    public function branchShipments(): BelongsToMany
    {
        return $this->belongsToMany(BranchShipment::class, 'branch_shipment_kiriman_mobil');
    }

    public function taskReturCabangs(): HasMany
    {
        return $this->hasMany(TaskReturCabang::class, 'kiriman_mobil_id');
    }
}
