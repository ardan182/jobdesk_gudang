<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class ArrivalSupplierTruck extends Model
{
    use LogsActivity;

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

    protected static function activityModule(): string { return 'task_datang_mobil_suppliers'; } protected static function activitySummaryAttributes(): array { return ['no_plat_mobil', 'nama_sopir']; } protected static function activityReferenceField(): ?string { return 'nama_sopir'; } protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id_task)) {
                $model->id_task = TaskIdGenerator::generate('datang_mobil_supplier');
            }
            if (empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
        });

        static::deleting(function ($model) {
            if (TaskTerimaSupplier::where('arrival_supplier_truck_id', $model->id)->exists()) {
                throw ValidationException::withMessages([
                    'id_task' => 'Data mobil tidak dapat dihapus karena sudah atau sedang diproses di menu Checker Terima Barang.',
                ]);
            }

            if (SupplierReturn::where('arrival_supplier_truck_id', $model->id)->exists()) {
                throw ValidationException::withMessages([
                    'id_task' => 'Data mobil tidak dapat dihapus karena sudah diproses di menu Retur In & Out Supplier.',
                ]);
            }
        });
    }

    public function syncStatus(): void
    {
        $needTerima = in_array($this->jenis_kiriman, ['DATANG', 'DATANG & RETUR']);
        $needRetur = in_array($this->jenis_kiriman, ['RETUR', 'DATANG & RETUR']);

        $hasTerima = $this->taskTerimaSuppliers()->exists();
        $hasRetur = $this->supplierReturns()->exists();

        if (!$hasTerima && !$hasRetur) {
            $this->update(['status' => 'MENGANTRI', 'jam_selesai' => null]);
            return;
        }

        $times = [];

        $terimaDone = false;
        if ($needTerima) {
            $terimaSelesai = $this->taskTerimaSuppliers()
                ->where('status', 'SELESAI')
                ->whereNotNull('selesai_bongkar')
                ->first();
            $terimaDone = $terimaSelesai !== null;
            if ($terimaSelesai) {
                $times[] = $terimaSelesai->selesai_bongkar->format('H:i');
            }
        }

        $returDone = false;
        if ($needRetur) {
            $returSelesai = $this->supplierReturns()
                ->where('status', 'selesai')
                ->latest('id')
                ->first();
            $returDone = $returSelesai !== null;
            if ($returSelesai && $returSelesai->jam_kedatangan) {
                $times[] = $returSelesai->jam_kedatangan->format('H:i');
            }
        }

        $done = (! $needTerima || $terimaDone) && (! $needRetur || $returDone);

        if ($done) {
            sort($times);
            $this->update([
                'status' => 'SELESAI',
                'jam_selesai' => $times ? end($times) : null,
            ]);
            return;
        }

        $this->update(['status' => 'PROSES', 'jam_selesai' => null]);
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

    public function supplierReturns(): HasMany
    {
        return $this->hasMany(SupplierReturn::class, 'arrival_supplier_truck_id');
    }
}
