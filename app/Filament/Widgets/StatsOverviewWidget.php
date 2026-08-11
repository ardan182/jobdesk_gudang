<?php

namespace App\Filament\Widgets;

use App\Models\ArrivalSupplierTruck;
use App\Models\KendaraanDokumen;
use App\Models\KomplainPo;
use App\Models\SupplierSj;
use App\Models\TaskKeluarBarang;
use App\Models\TaskKirimanMobil;
use App\Models\TaskReturCabang;
use App\Models\TaskTerimaSupplier;
use App\Models\SupplierReturn;
use ThalysJuvenal\Aurum\Widgets\AurumStat;
use ThalysJuvenal\Aurum\Widgets\AurumStatsOverview;

class StatsOverviewWidget extends AurumStatsOverview
{
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->can('view_widget_stats_overview') ?? false;
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $viewAll = (bool) ($user?->can('view_all_data') ?? false);

        $config = [
            'supplier_returns' => [
                'ownable' => true,
                'model' => SupplierReturn::class,
                'cards' => [
                    ['label' => 'Retur ke Supplier', 'icon' => 'heroicon-o-arrow-left-on-rectangle', 'desc' => 'Total retur keluar ke supplier', 'scope' => ['jenis_pengiriman' => 'retur_keluar']],
                    ['label' => 'Retur dari Supplier', 'icon' => 'heroicon-o-arrow-right-on-rectangle', 'desc' => 'Total retur masuk dari supplier', 'scope' => ['jenis_pengiriman' => 'retur_masuk']],
                ],
            ],
            'task_datang_mobil_suppliers' => [
                'ownable' => true,
                'model' => ArrivalSupplierTruck::class,
                'cards' => [
                    [
                        'label' => 'Datang Mobil',
                        'icon' => 'heroicon-o-truck',
                        'desc' => 'Total mobil / task selesai',
                        'scope' => [],
                        'value' => fn ($query) => $query->count()
                            . '/<span class="aurum-stat-value--green">'
                            . (clone $query)->where('status', 'SELESAI')->count()
                            . '</span>',
                    ],
                ],
            ],
            'task_terima_suppliers' => [
                'ownable' => true,
                'model' => TaskTerimaSupplier::class,
                'cards' => [
                    [
                        'label' => 'Terima Barang',
                        'icon' => 'heroicon-o-arrow-down-tray',
                        'desc' => 'Total terima / task selesai',
                        'scope' => [],
                        'value' => fn ($query) => $query->count()
                            . '/<span class="aurum-stat-value--green">'
                            . (clone $query)->where('status', 'SELESAI')->count()
                            . '</span>',
                    ],
                ],
            ],
            'task_keluar_barangs' => [
                'ownable' => true,
                'model' => TaskKeluarBarang::class,
                'cards' => [
                    ['label' => 'Keluar Barang', 'icon' => 'heroicon-o-arrow-up-tray', 'desc' => 'Total barang keluar', 'scope' => []],
                ],
            ],
            'task_kiriman_mobils' => [
                'ownable' => true,
                'model' => TaskKirimanMobil::class,
                'cards' => [
                    ['label' => 'Kiriman Mobil', 'icon' => 'heroicon-o-truck', 'desc' => 'Total pengiriman mobil', 'scope' => []],
                ],
            ],
            'task_retur_cabangs' => [
                'ownable' => true,
                'model' => TaskReturCabang::class,
                'cards' => [
                    ['label' => 'Retur Masuk Cabang', 'icon' => 'heroicon-o-arrow-path', 'desc' => 'Total retur dari cabang', 'scope' => []],
                ],
            ],
            'komplain_pos' => [
                'ownable' => true,
                'model' => KomplainPo::class,
                'cards' => [
                    ['label' => 'Komplain PO', 'icon' => 'heroicon-o-document-text', 'desc' => 'Total komplain PO', 'scope' => []],
                ],
            ],
            'supplier_sjs' => [
                'ownable' => false,
                'model' => SupplierSj::class,
                'cards' => [
                    ['label' => 'SJ Belum Di Cek', 'icon' => 'heroicon-o-document-magnifying-glass', 'desc' => 'SJ pending cek', 'scope' => ['status_input' => 'belum_di_cek']],
                ],
            ],
            'kendaraan_dokumens' => [
                'ownable' => false,
                'model' => KendaraanDokumen::class,
                'cards' => [
                    ['label' => 'STNK/KIR ≤ 30 hari', 'icon' => 'heroicon-o-exclamation-triangle', 'desc' => 'Dokumen expired / akan expired', 'scope' => ['masa_berlaku' => ['notNull' => true, 'max' => now()->addDays(30)]]],
                ],
            ],
        ];

        $stats = [];

        foreach ($config as $module => $cfg) {
            if (!$user?->can("view_{$module}")) {
                continue;
            }

            // Modul tanpa kepemilikan (SJ/dokumen global) hanya untuk user yang bisa lihat semua data
            if (!($cfg['ownable'] ?? true) && !$viewAll) {
                continue;
            }

            foreach ($cfg['cards'] as $card) {
                $query = $cfg['model']::query();

                foreach ($card['scope'] ?? [] as $field => $value) {
                    if (is_array($value)) {
                        if (!empty($value['notNull'])) {
                            $query->whereNotNull($field);
                        }
                        if (isset($value['max'])) {
                            $query->where($field, '<=', $value['max']);
                        }
                    } else {
                        $query->where($field, $value);
                    }
                }

                if (($cfg['ownable'] ?? true) && !$viewAll) {
                    $query->where('user_id', $user->id);
                }

                $value = isset($card['value'])
                    ? $card['value']($query)
                    : (string) $query->count();

                $stats[] = AurumStat::make($card['label'], $value)
                    ->icon($card['icon'])
                    ->description($card['desc']);
            }
        }

        return $stats;
    }
}
