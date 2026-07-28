<?php

namespace App\Models;

use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierReturn extends Model
{
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
        'jenis_retur',
        'no_nota_retur',
        'total_koli',
        'total_kolian',
        'status',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_datang' => 'date:Y-m-d',
        'jam_kedatangan' => 'datetime:H:i',
        'total_koli' => 'integer',
        'total_kolian' => 'integer',
    ];

    protected $attributes = [
        'status' => 'draft',
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
                'description' => match ($model->jenis_pengiriman) {
                    'retur_masuk' => "Retur Masuk: {$model->nama_supplier}",
                    'retur_keluar' => "Retur Keluar: {$model->nama_supplier}",
                    'datang_dan_keluar' => "Datang & Keluar: {$model->nama_supplier}",
                },
                'reference' => $model->no_plat_mobil,
                'action' => 'create',
            ]);
        });
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
