<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KendaraanDokumen extends Model
{
    use LogsActivity;

    protected $fillable = [
        'master_kendaraan_id',
        'jenis',
        'nomor_dokumen',
        'tanggal_terbit',
        'periode',
        'masa_berlaku',
        'user_perpanjang',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date:Y-m-d',
        'masa_berlaku' => 'date:Y-m-d',
    ];

    protected static function activityModule(): string { return 'kendaraan_dokumens'; } protected static function activitySummaryAttributes(): array { return ['nomor_dokumen']; } protected static function activityReferenceField(): ?string { return 'nomor_dokumen'; } protected static function shouldLogActivity($model, string $action): bool
    {
        return ($model->user_perpanjang ?? null) !== 'System';
    }

    protected static function booted(): void
    {
        static::saving(function ($model) {
            if ($model->tanggal_terbit && !$model->masa_berlaku) {
                $terbit = Carbon::parse($model->tanggal_terbit);
                $model->masa_berlaku = match ($model->jenis) {
                    'kir' => $terbit->copy()->addMonths(6),
                    'stnk' => match ($model->periode) {
                        '5_tahun' => $terbit->copy()->addYears(5),
                        default => $terbit->copy()->addYear(),
                    },
                };
            }
        });

        static::saved(function ($model) {
            $kendaraan = $model->kendaraan;
            if (!$kendaraan) return;

            match ($model->jenis) {
                'stnk' => match ($model->periode) {
                    '5_tahun' => $kendaraan->updateQuietly(['stnk_5_tahun_sampai' => $model->masa_berlaku]),
                    default => $kendaraan->updateQuietly(['masa_berlaku_stnk' => $model->masa_berlaku]),
                },
                'kir' => $kendaraan->updateQuietly(['masa_berlaku_kir' => $model->masa_berlaku]),
            };

            if ($model->jenis === 'stnk' && $model->periode === '5_tahun') {
                $masaBerlakuSatuTahun = $model->masa_berlaku?->copy()->subYears(4);

                KendaraanDokumen::where('master_kendaraan_id', $model->master_kendaraan_id)
                    ->where('jenis', 'stnk')
                    ->where('periode', '1_tahun')
                    ->update(['masa_berlaku' => $masaBerlakuSatuTahun]);

                $kendaraan->updateQuietly(['masa_berlaku_stnk' => $masaBerlakuSatuTahun]);
            }
        });
    }

    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(MasterKendaraan::class, 'master_kendaraan_id');
    }

    public function getMasaBerlakuDisplayAttribute(): string
    {
        if (!$this->masa_berlaku) return '-';
        return $this->masa_berlaku->format('d/m/Y');
    }

    public function getStatusWarnaAttribute(): string
    {
        if (!$this->masa_berlaku) return 'gray';
        $days = now()->startOfDay()->diffInDays($this->masa_berlaku, false);
        if ($days < 0) return 'danger';
        if ($days <= 7) return 'warning';
        return 'success';
    }

    public function getStatusLabelAttribute(): string
    {
        if (!$this->masa_berlaku) return '-';
        $days = now()->startOfDay()->diffInDays($this->masa_berlaku, false);
        if ($days < 0) return 'Expired';
        if ($days == 0) return 'Hari Ini';
        if ($days <= 7) return "{$days} hari";
        return 'Aman';
    }

    public function getPeriodeLabelAttribute(): string
    {
        return match ($this->periode) {
            '1_tahun' => '1 Tahun',
            '5_tahun' => '5 Tahun',
            default => match ($this->jenis) {
                'kir' => '6 Bulan',
                default => '-',
            },
        };
    }
}
