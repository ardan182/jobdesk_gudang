<?php

namespace App\Models;

use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TaskTerimaSupplier extends Model
{
    protected $fillable = [
        'id_task',
        'arrival_supplier_truck_id',
        'nama_supplier_ekspedisi',
        'no_po_referensi',
        'jam_datang',
        'jumlah_kolian',
        'jam_bongkar',
        'selesai_bongkar',
        'lembar_sj',
        'nama_sopir',
        'status',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'jam_datang' => 'datetime:H:i',
        'jam_bongkar' => 'datetime:H:i',
        'selesai_bongkar' => 'datetime:H:i',
        'lembar_sj' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id_task)) {
                $model->id_task = TaskIdGenerator::generate('terima_supplier');
            }
            if (empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
        });

        static::created(function ($model) {
            ActivityLog::create([
                'user_id' => $model->user_id,
                'module' => 'Terima Supplier',
                'id_task' => $model->id_task,
                'description' => "Supplier: {$model->nama_supplier_ekspedisi} → {$model->status}",
                'reference' => $model->no_po_referensi,
                'action' => 'create',
            ]);

            if ($model->arrival_supplier_truck_id) {
                $truck = ArrivalSupplierTruck::find($model->arrival_supplier_truck_id);
                $truck?->syncStatus();
            }
        });

        static::updated(function ($model) {
            $changes = [];
            $tracked = ['status', 'nama_supplier_ekspedisi', 'no_po_referensi', 'jam_datang', 'jumlah_kolian', 'jam_bongkar', 'selesai_bongkar', 'lembar_sj', 'nama_sopir', 'keterangan'];
            foreach ($tracked as $field) {
                $old = $model->getOriginal($field);
                $new = $model->$field;
                $oldStr = $old ?? '-';
                $newStr = $new ?? '-';
                if ($oldStr !== $newStr) {
                    $changes[] = "$field: $oldStr → $newStr";
                }
            }
            if (empty($changes)) return;

            ActivityLog::create([
                'user_id' => auth()->id() ?? $model->user_id,
                'module' => 'Terima Supplier',
                'id_task' => $model->id_task,
                'description' => "Supplier: {$model->nama_supplier_ekspedisi} — " . implode('; ', $changes),
                'reference' => $model->no_po_referensi,
                'action' => 'update',
            ]);

            if ($model->arrival_supplier_truck_id) {
                $truck = ArrivalSupplierTruck::find($model->arrival_supplier_truck_id);
                $truck?->syncStatus();
            }

            if ($model->status === 'SELESAI') {
                $alreadyExists = \App\Models\SupplierSj::where('keterangan', 'LIKE', '%' . $model->id_task . '%')->exists();
                if (!$alreadyExists) {
                    $arrivalTruck = $model->arrivalSupplierTruck;
                    \App\Models\SupplierSj::create([
                        'nama_supplier'      => $arrivalTruck?->supplier?->nama_supplier ?? $model->nama_supplier_ekspedisi,
                        'tanggal_datang'     => $arrivalTruck?->tanggal_datang ?? now()->toDateString(),
                        'nomor_po_referensi' => $model->no_po_referensi,
                        'jumlah_koli'        => $model->jumlah_kolian,
                        'jumlah_faktur'      => $model->lembar_sj ?? 1,
                        'status_input'       => 'belum_di_cek',
                        'keterangan'         => 'Auto dari Terima Supplier: ' . $model->id_task,
                    ]);
                }
            }
        });

        static::deleted(function ($model) {
            if ($model->arrival_supplier_truck_id) {
                $truck = ArrivalSupplierTruck::find($model->arrival_supplier_truck_id);
                $truck?->syncStatus();
            }
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

    public function helpers(): BelongsToMany
    {
        return $this->belongsToMany(WarehouseEmployee::class, 'task_terima_supplier_helpers')
            ->withTimestamps();
    }
}
