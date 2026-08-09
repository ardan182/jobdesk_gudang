<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierReturn extends Model
{
    use LogsActivity;

    protected $fillable = [
        'id_task',
        'arrival_supplier_truck_id',
        'jenis_pengiriman',
        'nama_supplier',
        'nama_ekspedisi',
        'nama_supir',
        'no_plat_mobil',
        'tanggal_datang',
        'jam_kedatangan',
        'jenis_retur_keluar',
        'jenis_retur_masuk',
        'no_nota_retur',
        'total_koli_keluar',
        'total_kolian_masuk',
        'status',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_datang' => 'date:Y-m-d',
        'jam_kedatangan' => 'datetime:H:i',
        'total_koli_keluar' => 'integer',
        'total_kolian_masuk' => 'integer',
    ];

    protected $attributes = [
        'status' => 'draft',
    ];

    protected static function activityModule(): string { return 'supplier_returns'; } protected static function activitySummaryAttributes(): array { return ['nama_supplier', 'jenis_pengiriman']; } protected static function activityReferenceField(): ?string { return 'no_plat_mobil'; } protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id_task)) {
                $model->id_task = TaskIdGenerator::generate('retur_supplier');
            }
            if (empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
        });

        $syncTruck = function ($model) {
            $model->arrivalSupplierTruck?->syncStatus();
        };

        static::created($syncTruck);
        static::updated($syncTruck);
        static::deleted($syncTruck);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function arrivalSupplierTruck(): BelongsTo
    {
        return $this->belongsTo(ArrivalSupplierTruck::class);
    }
}
