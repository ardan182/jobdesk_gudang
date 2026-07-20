<?php

namespace App\Models;

use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class ArrivalSupplierTruck extends Model
{
    protected $table = 'arrival_supplier_trucks';

    protected $fillable = [
        'id_task',
        'supplier_id',
        'expedition_id',
        'nama_sopir',
        'no_plat_mobil',
        'jenis_kiriman',
        'tanggal_datang',
        'jam_datang',
        'jam_selesai',
        'status',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_datang' => 'date',
        'jam_datang' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
    ];

    protected $attributes = [
        'jenis_kiriman' => 'DATANG',
        'status' => 'MENGANTRI',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id_task)) {
                $model->id_task = TaskIdGenerator::generate('datang_mobil_supplier');
            }
            if (empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
        });

        static::created(function ($model) {
            ActivityLog::create([
                'user_id' => $model->user_id,
                'module' => 'Datang Mobil Supplier',
                'id_task' => $model->id_task,
                'description' => "Supplier: {$model->supplier?->nama_supplier} - Plat: {$model->no_plat_mobil}",
                'reference' => $model->nama_sopir,
                'action' => 'create',
            ]);
        });

        static::deleting(function ($model) {
            if (TaskTerimaSupplier::where('arrival_supplier_truck_id', $model->id)->exists()) {
                throw ValidationException::withMessages([
                    'id_task' => 'Data mobil tidak dapat dihapus karena sudah atau sedang diproses di menu Checker Terima Barang.',
                ]);
            }
        });
    }

    public function syncStatus(): void
    {
        $hasTerima = $this->taskTerimaSuppliers()->exists();
        $hasRetur = $this->taskReturSuppliers()->exists();

        if (!$hasTerima && !$hasRetur) {
            $this->update(['status' => 'MENGANTRI', 'jam_selesai' => null]);
            return;
        }

        $newStatus = 'PROSES';
        $times = [];

        $terima = $this->taskTerimaSuppliers()
            ->where('status', 'SELESAI')
            ->whereNotNull('selesai_bongkar')
            ->first();

        if ($terima) {
            $times[] = $terima->selesai_bongkar->format('H:i');
        }

        if (in_array($this->jenis_kiriman, ['RETUR', 'DATANG & RETUR'])) {
            $retur = $this->taskReturSuppliers()
                ->whereNotNull('jam_muat')
                ->first();
            if ($retur) {
                $times[] = $retur->jam_muat->format('H:i');
            }
        }

        if ($terima) {
            $needRetur = in_array($this->jenis_kiriman, ['RETUR', 'DATANG & RETUR']);
            $returDone = $needRetur
                ? $this->taskReturSuppliers()->whereNotNull('jam_muat')->exists()
                : true;

            if ($returDone) {
                $newStatus = 'SELESAI';
            }
        }

        sort($times);
        $jamSelesai = !empty($times) ? end($times) : null;

        $this->update(['status' => $newStatus, 'jam_selesai' => $jamSelesai]);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function expedition(): BelongsTo
    {
        return $this->belongsTo(Expedition::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function taskTerimaSuppliers(): HasMany
    {
        return $this->hasMany(TaskTerimaSupplier::class, 'arrival_supplier_truck_id');
    }

    public function taskReturSuppliers(): HasMany
    {
        return $this->hasMany(TaskReturSupplier::class, 'arrival_supplier_truck_id');
    }
}
