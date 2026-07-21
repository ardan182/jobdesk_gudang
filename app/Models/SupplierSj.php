<?php

namespace App\Models;

use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;

class SupplierSj extends Model
{
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
        'keterangan',
    ];

    protected $casts = [
        'tanggal_datang' => 'date',
        'tanggal_input' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id_task)) {
                $model->id_task = TaskIdGenerator::generate('supplier_sj');
            }
        });
    }
}
