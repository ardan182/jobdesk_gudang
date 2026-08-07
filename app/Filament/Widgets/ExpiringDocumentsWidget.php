<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\KendaraanDokumen\KendaraanDokumenResource;
use App\Models\KendaraanDokumen;
use ThalysJuvenal\Aurum\Widgets\AurumValueList;
use ThalysJuvenal\Aurum\Widgets\ValueListItem;

class ExpiringDocumentsWidget extends AurumValueList
{
    protected ?string $heading = '⚠️ STNK/KIR Segera Expired';

    protected static ?int $sort = 1;

    protected function getItems(): array
    {
        $docs = KendaraanDokumen::with('kendaraan')
            ->whereNotNull('masa_berlaku')
            ->where('masa_berlaku', '<=', now()->addDays(7))
            ->orderBy('masa_berlaku', 'asc')
            ->limit(10)
            ->get();

        return $docs->map(function (KendaraanDokumen $doc) {
            $days = now()->startOfDay()->diffInDays($doc->masa_berlaku, false);
            $jenis = strtoupper($doc->jenis);

            if ($days < 0) {
                $value = "{$jenis} - EXPIRED";
            } elseif ($days == 0) {
                $value = "{$jenis} - HARI INI";
            } else {
                $value = "{$jenis} - {$days} hari";
            }

            $plat = $doc->kendaraan?->nomor_polisi ?? '-';
            $merek = $doc->kendaraan?->merek_dan_model ?? '';

            return ValueListItem::make("🚗 {$plat} {$merek}")
                ->value($value)
                ->status('danger')
                ->url(KendaraanDokumenResource::getUrl('index'));
        })->all();
    }

    public static function canView(): bool
    {
        return auth()->user()?->can('view_widget_expiring_documents') ?? false;
    }
}
