<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseDocument extends Model
{
    protected $fillable = [
        'nama_dokumen',
        'kategori',
        'versi',
        'file_path',
        'format_file',
        'deskripsi',
        'download_count',
        'user_id',
    ];

    protected $casts = [
        'download_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
