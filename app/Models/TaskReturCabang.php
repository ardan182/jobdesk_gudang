<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TaskReturCabang extends Model
{
    use LogsActivity;

    protected $fillable = [
        'id_task',
        'kiriman_mobil_id',
        'cabang',
        'no_plat_mobil',
        'jam_tiba',
        'jenis_retur',
        'tanggal_bongkar',
        'no_sj_retur',
        'jumlah_sj_bagus',
        'catatan_bagus',
        'jumlah_sj_jelek',
        'catatan_jelek',
        'jam_bongkar',
        'nama_sopir',
        'status',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'jam_bongkar' => 'datetime:H:i',
        'jam_tiba' => 'datetime:H:i',
        'tanggal_bongkar' => 'date:Y-m-d',
        'jumlah_sj_bagus' => 'integer',
        'jumlah_sj_jelek' => 'integer',
    ];

    protected static function activityModule(): string { return 'task_retur_cabangs'; } protected static function activitySummaryAttributes(): array { return ['cabang', 'jenis_retur']; } protected static function activityReferenceField(): ?string { return 'no_sj_retur'; } protected static function activityTracked(): ?array { return ['cabang', 'no_plat_mobil', 'jam_tiba', 'jenis_retur', 'tanggal_bongkar', 'jumlah_sj_bagus', 'catatan_bagus', 'jumlah_sj_jelek', 'catatan_jelek', 'jam_bongkar', 'nama_sopir', 'status', 'keterangan']; } protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id_task)) {
                $model->id_task = TaskIdGenerator::generate('retur_cabang');
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

    public function kirimanMobil(): BelongsTo
    {
        return $this->belongsTo(TaskKirimanMobil::class);
    }

    public function helpers(): BelongsToMany
    {
        return $this->belongsToMany(WarehouseEmployee::class, 'task_retur_cabang_employee');
    }
}
