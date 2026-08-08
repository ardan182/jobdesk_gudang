<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseLeave extends Model
{
    use LogsActivity;

    protected $table = 'warehouse_leaves';

    protected $fillable = [
        'warehouse_employee_id',
        'jenis_absen',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date:Y-m-d',
        'tanggal_selesai' => 'date:Y-m-d',
    ];

    protected static function activityModule(): string { return 'cuti_absensi'; } protected static function activitySummaryAttributes(): array { return ['jenis_absen', 'tanggal_mulai']; } protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(WarehouseEmployee::class, 'warehouse_employee_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
