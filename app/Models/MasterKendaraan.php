<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MasterKendaraan extends Model
{
    protected $table = 'master_kendaraans';

    protected $fillable = [
        'nomor_polisi',
        'jenis_kendaraan',
        'merek_dan_model',
        'nomor_rangka',
        'nomor_mesin',
        'no_stnk',
        'no_kir',
        'masa_berlaku_stnk',
        'masa_berlaku_kir',
        'stnk_5_tahun_sampai',
        'keterangan',
    ];

    protected $casts = [
        'stnk_5_tahun_sampai' => 'date:Y-m-d',
    ];

    protected static function booted(): void
    {
        static::created(function ($model) {
            $model->createDokumenRecords();
        });

        static::saved(function ($model) {
            if ($model->wasRecentlyCreated) return;

            $changes = $model->getChanges();
            $fields = ['masa_berlaku_stnk', 'stnk_5_tahun_sampai', 'masa_berlaku_kir'];

            if (!array_intersect($fields, array_keys($changes))) return;

            if (isset($changes['masa_berlaku_stnk'])) {
                $dok = KendaraanDokumen::firstOrCreate([
                    'master_kendaraan_id' => $model->id, 'jenis' => 'stnk', 'periode' => '1_tahun',
                ], ['masa_berlaku' => $changes['masa_berlaku_stnk']]);
                if ($dok->masa_berlaku?->format('Y-m-d') !== $changes['masa_berlaku_stnk']) {
                    $dok->updateQuietly(['masa_berlaku' => $changes['masa_berlaku_stnk']]);
                }
            }

            if (isset($changes['stnk_5_tahun_sampai'])) {
                $dok = KendaraanDokumen::firstOrCreate([
                    'master_kendaraan_id' => $model->id, 'jenis' => 'stnk', 'periode' => '5_tahun',
                ], [
                    'nomor_dokumen' => $model->no_stnk,
                    'user_perpanjang' => 'System',
                    'masa_berlaku' => $changes['stnk_5_tahun_sampai'],
                ]);
                if ($dok->masa_berlaku?->format('Y-m-d') !== $changes['stnk_5_tahun_sampai']) {
                    $dok->updateQuietly(['masa_berlaku' => $changes['stnk_5_tahun_sampai']]);
                }
            }

            if (isset($changes['masa_berlaku_kir']) && $model->jenis_kendaraan !== 'motor') {
                $dok = KendaraanDokumen::firstOrCreate([
                    'master_kendaraan_id' => $model->id, 'jenis' => 'kir',
                ], ['masa_berlaku' => $changes['masa_berlaku_kir']]);
                if ($dok->masa_berlaku?->format('Y-m-d') !== $changes['masa_berlaku_kir']) {
                    $dok->updateQuietly(['masa_berlaku' => $changes['masa_berlaku_kir']]);
                }
            }
        });
    }

    public function createDokumenRecords(): void
    {
        KendaraanDokumen::firstOrCreate([
            'master_kendaraan_id' => $this->id,
            'jenis' => 'stnk',
            'periode' => '1_tahun',
        ], [
            'nomor_dokumen' => $this->no_stnk,
            'tanggal_terbit' => $this->masa_berlaku_stnk ?: now(),
            'masa_berlaku' => $this->masa_berlaku_stnk,
            'user_perpanjang' => 'System',
        ]);

        if ($this->stnk_5_tahun_sampai) {
            KendaraanDokumen::firstOrCreate([
                'master_kendaraan_id' => $this->id,
                'jenis' => 'stnk',
                'periode' => '5_tahun',
            ], [
                'nomor_dokumen' => $this->no_stnk,
                'masa_berlaku' => $this->stnk_5_tahun_sampai,
                'user_perpanjang' => 'System',
            ]);
        }

        if ($this->jenis_kendaraan !== 'motor') {
            KendaraanDokumen::firstOrCreate([
                'master_kendaraan_id' => $this->id,
                'jenis' => 'kir',
            ], [
                'nomor_dokumen' => $this->no_kir,
                'tanggal_terbit' => $this->masa_berlaku_kir ?: now(),
                'masa_berlaku' => $this->masa_berlaku_kir,
                'user_perpanjang' => 'System',
            ]);
        }
    }

    public function stnkTerbaru(): HasOne
    {
        return $this->hasOne(KendaraanDokumen::class, 'master_kendaraan_id')
            ->where('jenis', 'stnk')
            ->latestOfMany();
    }

    public function kirTerbaru(): HasOne
    {
        return $this->hasOne(KendaraanDokumen::class, 'master_kendaraan_id')
            ->where('jenis', 'kir')
            ->latestOfMany();
    }

    public function dokumens(): HasMany
    {
        return $this->hasMany(KendaraanDokumen::class, 'master_kendaraan_id');
    }
}
