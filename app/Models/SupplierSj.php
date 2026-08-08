<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;

class SupplierSj extends Model
{
    use LogsActivity;

    protected $table = 'supplier_sjs';

    protected $fillable = [
        'id_task',
        'nama_supplier',
        'tanggal_datang',
        'nomor_po_referensi',
        'jumlah_koli',
        'jumlah_faktur',
        'status_input',
        'tanggal_input',
        'tempo_hari',
        'keterangan',
        'catatan',
    ];

    protected $casts = [
        'tanggal_datang' => 'date',
        'tanggal_input' => 'date',
        'tempo_hari' => 'integer',
    ];

    public function getTempoDisplayAttribute(): string
    {
        if (!$this->tanggal_datang) return '-';

        if ($this->tempo_hari !== null) {
            $days = $this->tempo_hari;
        } else {
            $base = $this->status_input === 'selesai' && $this->tanggal_input
                ? $this->tanggal_input
                : now();
            $days = abs($base->startOfDay()->diffInDays($this->tanggal_datang));
        }

        $prefix = in_array($this->status_input, ['belum_di_cek', 'draft']) ? 'blm input' : 'input';
        return "{$prefix} {$days} hr";
    }

    protected static function activityModule(): string { return 'supplier_sjs'; } protected static function activitySummaryAttributes(): array { return ['nama_supplier']; } protected static function activityReferenceField(): ?string { return 'nomor_po_referensi'; } protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id_task)) {
                $model->id_task = TaskIdGenerator::generate('supplier_sj');
            }
        });
    }
}
