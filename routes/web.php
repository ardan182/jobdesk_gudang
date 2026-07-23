<?php

use App\Models\ArrivalSupplierTruck;
use App\Models\BranchShipment;
use App\Models\SupplierSj;
use App\Models\TaskKirimanMobil;
use App\Models\TvBoardSetting;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/suppliers/template', function () {
    return app(\App\Exports\SuppliersExport::class)->download();
})->name('suppliers.template.download');

Route::get('/employees/template', function () {
    return app(\App\Exports\EmployeesExport::class)->download();
})->name('employees.template.download');

Route::get('/tv-board', function () {
    $settings = TvBoardSetting::first();
    $refresh = $settings?->refresh_interval ?? 60;
    $max = $settings?->max_items ?? 15;
    $marquee = $settings?->marquee_message;

    $supplierArrivals = $settings?->show_supplier_arrivals !== false
        ? ArrivalSupplierTruck::with('supplier')
            ->whereIn('status', ['MENGANTRI', 'PROSES', 'SELESAI'])
            ->latest()->limit($max)->get()->map(fn ($t) => [
                'plat' => $t->no_plat_mobil,
                'supplier' => $t->supplier?->nama_supplier ?? '-',
                'sopir' => $t->nama_sopir ?? '-',
                'tgl' => $t->jam_datang?->format('H:i') ?? '-',
                'statusCode' => $t->status,
                'statusIcon' => match ($t->status) {
                    'MENGANTRI' => '⏳', 'PROSES' => '🔧', 'SELESAI' => '✅', default => '❓',
                },
                'statusText' => match ($t->status) {
                    'MENGANTRI' => 'Mengantri', 'PROSES' => 'Proses', 'SELESAI' => 'Selesai', default => $t->status,
                },
            ])->toArray()
        : [];

    $branchDeliveries = $settings?->show_branch_deliveries !== false
        ? TaskKirimanMobil::latest()->limit($max)->get()->map(fn ($k) => [
            'cabang' => $k->cabang,
            'plat' => $k->no_plat_mobil ?? '-',
            'sopir' => $k->nama_supir ?? '-',
            'statusCode' => $k->status,
            'statusIcon' => match ($k->status) {
                'draft' => '📝', 'dalam pengiriman' => '🚀', 'selesai' => '✅', default => '❓',
            },
            'statusText' => match ($k->status) {
                'draft' => 'Draft', 'dalam pengiriman' => 'Dalam Kirim', 'selesai' => 'Selesai', default => $k->status,
            },
        ])->toArray()
        : [];

    $shipmentSj = $settings?->show_shipment_sj !== false
        ? BranchShipment::latest()->limit($max)->get()->map(fn ($s) => [
            'noSj' => $s->nomor_sj,
            'cabang' => $s->cabang,
            'qty' => $s->total_qty,
            'statusCode' => $s->status === 'selesai' ? 'SELESAI' : 'DRAFT',
            'statusIcon' => $s->status === 'selesai' ? '✅' : '📝',
            'statusText' => $s->status === 'selesai' ? 'Selesai' : 'Draft',
        ])->toArray()
        : [];

    $supplierInvoices = $settings?->show_supplier_invoices !== false
        ? SupplierSj::latest()->limit($max)->get()->map(fn ($s) => [
            'supplier' => $s->nama_supplier ?? '-',
            'noPo' => $s->nomor_po_referensi ?? '-',
            'koli' => $s->jumlah_koli ?? 0,
            'tempo' => $s->tanggal_datang
                ? abs(now()->startOfDay()->diffInDays($s->tanggal_datang)) . ' hr'
                : '-',
            'statusCode' => match ($s->status_input) {
                'belum_di_cek' => 'BELUM_CEK', 'selesai' => 'SELESAI', default => 'DRAFT',
            },
            'statusIcon' => match ($s->status_input) {
                'belum_di_cek' => '🔴', 'selesai' => '🟢', default => '🟡',
            },
            'statusText' => match ($s->status_input) {
                'belum_di_cek' => 'Belum Di Cek', 'selesai' => 'Selesai', default => 'Draft',
            },
        ])->toArray()
        : [];

    $counts = [
        count($supplierArrivals),
        count($branchDeliveries),
        count($shipmentSj),
        count($supplierInvoices),
    ];

    if (!$marquee) {
        $labels = ['Mobil Supplier', 'Kiriman', 'SJ Kiriman', 'SJ Supplier'];
        $parts = [];
        foreach ($counts as $i => $c) {
            if ($c > 0 || $settings === null) {
                $parts[] = "🚚 $c {$labels[$i]}";
            }
        }
        $marquee = implode(' | ', $parts) ?: 'Board TV — Jobdesk Gudang AP';
    }

    return view('tv-board', compact(
        'supplierArrivals', 'branchDeliveries', 'shipmentSj', 'supplierInvoices',
        'refresh', 'marquee', 'settings'
    ));
});
