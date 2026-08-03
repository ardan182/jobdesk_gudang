<?php

namespace App\Models;

use App\Services\TaskIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KomplainPo extends Model
{
    protected $table = 'po_complaints';

    protected $fillable = [
        'id_task',
        'cabang',
        'supplier_id',
        'no_po',
        'barcode',
        'nama_barang',
        'qty_diterima',
        'no_surat_jalan',
        'qty_disurat_jalan',
        'foto',
        'tanggal_datang_barang',
        'kondisi_barang',
        'penyelesaian',
        'status',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'foto' => 'array',
        'tanggal_datang_barang' => 'date:Y-m-d',
        'qty_diterima' => 'integer',
        'qty_disurat_jalan' => 'integer',
    ];

    protected $attributes = [
        'status' => 'draft',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id_task)) {
                $model->id_task = TaskIdGenerator::generate('komplain_po');
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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
