<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Board TV — Jobdesk Gudang AP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { DEFAULT: '#0D1282', 50: '#f0f2ff', 100: '#e0e4ff', 700: '#0a0e66', 800: '#0d1282', 900: '#070a4a' },
                        softgray: { DEFAULT: '#EEEDED', 50: '#ffffff', 100: '#f7f7f7', 200: '#eeeded', 300: '#dcdcdc' }
                    },
                    fontFamily: { sans: ['Inter', 'Arial', 'sans-serif'], mono: ['JetBrains Mono', 'Consolas', 'monospace'] },
                    animation: { marquee: 'marquee 30s linear infinite' },
                    keyframes: { marquee: { '0%': { transform: 'translateX(100%)' }, '100%': { transform: 'translateX(-100%)' } } }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        html, body { height: 100%; width: 100%; margin: 0; padding: 0; overflow: hidden; font-family: 'Inter', Arial, sans-serif; background-color: #F8FAFC; color: #0F172A; user-select: none; }
        ::-webkit-scrollbar { display: none; }
        * { -ms-overflow-style: none; scrollbar-width: none; }
        .tv-card { background-color: #FFFFFF; border: 1px solid #E2E8F0; box-shadow: 0 4px 12px -2px rgba(13, 18, 130, 0.05); }
        .marquee-container:hover .marquee-content { animation-play-state: paused; }
    </style>
</head>
<body x-data="tvBoardApp()" x-init="initApp()" class="h-screen w-screen flex flex-col bg-slate-100 text-slate-800 overflow-hidden antialiased">

    <header class="h-20 bg-navy text-white px-8 flex items-center justify-between shrink-0 shadow-lg relative z-30">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-white border border-white/20 shadow-inner">
                <i class="fa-solid fa-desktop text-2xl"></i>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-black tracking-tight text-white uppercase">BOARD TV</h1>
                    <span class="bg-white/15 text-white text-xs font-black px-3 py-1 rounded-lg border border-white/20 uppercase tracking-wider">Jobdesk Gudang AP</span>
                </div>
                <p class="text-xs font-medium text-slate-200 flex items-center gap-2 mt-0.5">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Monitoring Realtime Gudang</span>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <div class="hidden lg:flex items-center gap-3">
                <div class="flex items-center gap-2 px-3.5 py-1.5 bg-white/10 rounded-xl border border-white/15">
                    <i class="fa-solid fa-truck-moving text-amber-300 text-sm"></i>
                    <span class="text-xs font-semibold text-slate-200">Datang:</span>
                    <span class="text-base font-black text-white font-mono" x-text="supplierArrivals.length">0</span>
                </div>
                <div class="flex items-center gap-2 px-3.5 py-1.5 bg-white/10 rounded-xl border border-white/15">
                    <i class="fa-solid fa-paper-plane text-blue-300 text-sm"></i>
                    <span class="text-xs font-semibold text-slate-200">Kirim:</span>
                    <span class="text-base font-black text-white font-mono" x-text="branchDeliveries.length">0</span>
                </div>
                <div class="flex items-center gap-2 px-3.5 py-1.5 bg-white/10 rounded-xl border border-white/15">
                    <i class="fa-solid fa-file-invoice text-emerald-300 text-sm"></i>
                    <span class="text-xs font-semibold text-slate-200">SJ:</span>
                    <span class="text-base font-black text-white font-mono" x-text="supplierInvoices.length">0</span>
                </div>
            </div>
            <div class="bg-white/10 px-5 py-2 rounded-2xl border border-white/20 flex items-center gap-3">
                <i class="fa-regular fa-clock text-amber-300 text-2xl"></i>
                <div class="text-right">
                    <div class="text-2xl font-black tracking-wider font-mono text-white leading-none" x-text="currentTime">09:30</div>
                    <div class="text-[11px] font-bold text-slate-200 mt-1 uppercase tracking-wide leading-none" x-text="currentDate">Kamis, 23 Juli 2026</div>
                </div>
            </div>
            <button @click="toggleFullscreen()" title="Fullscreen" class="w-11 h-11 rounded-2xl bg-white/10 hover:bg-white/20 text-white border border-white/20 flex items-center justify-center transition-all active:scale-95">
                <i class="fa-solid fa-expand text-lg"></i>
            </button>
        </div>
    </header>

    <div class="h-10 bg-softgray border-b border-softgray-300 text-slate-800 px-6 flex items-center shrink-0 relative z-20 overflow-hidden">
        <div class="bg-navy text-white px-3 py-1 rounded-lg text-xs font-extrabold uppercase tracking-wider flex items-center gap-2 shrink-0 z-10 shadow-sm">
            <i class="fa-solid fa-bullhorn text-amber-300 animate-pulse"></i>
            <span>STATUS</span>
        </div>
        <div class="overflow-hidden whitespace-nowrap w-full relative ml-4 marquee-container">
            <div class="inline-block animate-marquee marquee-content font-bold text-xs text-navy tracking-wide" x-text="marqueeMessage">{{ $marquee }}</div>
        </div>
    </div>

    <main class="flex-1 p-5 overflow-hidden">
        <div class="h-full grid grid-cols-12 grid-rows-2 gap-5">

            @php
                $showCard = $settings ? [
                    $settings->show_supplier_arrivals,
                    $settings->show_branch_deliveries,
                    $settings->show_shipment_sj,
                    $settings->show_supplier_invoices,
                ] : [true, true, true, true];

                $cardDefs = [
                    [
                        'icon' => 'fa-solid fa-truck-moving',
                        'title' => '🚚 Mobil Supplier Datang',
                        'subtitle' => 'Log kedatangan & antrian bongkar',
                        'count' => count($supplierArrivals),
                        'unit' => 'Mobil',
                        'data' => $supplierArrivals,
                        'cols' => ['Plat','Supplier','Sopir','Jam','Status'],
                        'fields' => ['plat','supplier','sopir','tgl'],
                        'legend' => 'Status: ⏳ Mengantri | 🔧 Proses | ✅ Selesai',
                        'footerNote' => 'Auto-sync ' . $refresh . 's',
                    ],
                    [
                        'icon' => 'fa-solid fa-truck-fast',
                        'title' => '🚛 Mobil Kirim ke Cabang',
                        'subtitle' => 'Status pengiriman armada toko',
                        'count' => count($branchDeliveries),
                        'unit' => 'Kiriman',
                        'data' => $branchDeliveries,
                        'cols' => ['Cabang','Plat','Sopir','Status'],
                        'fields' => ['cabang','plat','sopir'],
                        'legend' => 'Status: 📝 Draft | 🚀 Kirim | ✅ Selesai',
                        'footerNote' => 'Armada Gudang',
                    ],
                    [
                        'icon' => 'fa-solid fa-file-circle-check',
                        'title' => '📦 SJ Kiriman',
                        'subtitle' => 'Dokumen Surat Jalan Kirim Barang',
                        'count' => count($shipmentSj),
                        'unit' => 'Dokumen',
                        'data' => $shipmentSj,
                        'cols' => ['No SJ','Cabang','Qty','Status'],
                        'fields' => ['noSj','cabang','qty'],
                        'legend' => 'Prefix: KRM-BRG',
                        'footerNote' => 'Verifikasi Checker',
                    ],
                    [
                        'icon' => 'fa-solid fa-receipt',
                        'title' => '📋 Input SJ Supplier',
                        'subtitle' => 'Monitoring checklist penerimaan SJ PO',
                        'count' => count($supplierInvoices),
                        'unit' => 'SJ',
                        'data' => $supplierInvoices,
                        'cols' => ['Supplier','No PO','Koli','Tempo','Status'],
                        'fields' => ['supplier','noPo','koli','tempo'],
                        'legend' => 'Status: 🔴 Belum Di Cek | 🟢 Selesai',
                        'footerNote' => 'Checker Penerimaan',
                    ],
                ];
            @endphp

            @foreach ($cardDefs as $i => $card)
                @if ($showCard[$i])
                <section class="col-span-6 row-span-1 tv-card rounded-2xl overflow-hidden flex flex-col">
                    <div class="px-5 py-3.5 bg-softgray border-b border-softgray-300 flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-navy text-white flex items-center justify-center font-bold shadow-sm">
                                <i class="{{ $card['icon'] }} text-sm"></i>
                            </div>
                            <div>
                                <h2 class="font-extrabold text-navy text-base uppercase tracking-wide leading-none">{{ $card['title'] }}</h2>
                                <span class="text-[11px] text-slate-500 font-semibold">{{ $card['subtitle'] }}</span>
                            </div>
                        </div>
                        <span class="bg-navy/10 text-navy font-extrabold text-xs px-3 py-1 rounded-full border border-navy/20">{{ $card['count'] }} {{ $card['unit'] }}</span>
                    </div>
                    <div class="p-4 flex-1 overflow-hidden flex flex-col justify-between bg-white">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b-2 border-slate-200 text-slate-500 uppercase font-extrabold text-[11px] tracking-wider">
                                    @foreach ($card['cols'] as $col)
                                    <th class="pb-2.5 px-2 {{ $col === 'Status' ? 'text-center' : '' }}">{{ $col }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                                @forelse ($card['data'] as $item)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    @foreach ($card['fields'] as $field)
                                    <td class="py-3 px-2 {{ $loop->first ? 'font-mono font-extrabold text-navy text-sm' : ($field === 'tempo' ? 'font-semibold text-rose-600 text-xs' : ($field === 'qty' ? 'font-mono font-bold text-slate-700 text-xs' : ($field === 'tgl' ? 'font-mono text-slate-500 text-xs' : 'font-bold text-slate-900 text-sm'))) }} {{ $field === 'supplier' || $field === 'noSj' ? 'max-w-[150px] truncate' : '' }}">
                                        {{ $item[$field] ?? '-' }}
                                    </td>
                                    @endforeach
                                    @if (in_array('Status', $card['cols']))
                                    <td class="py-3 px-2 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-black uppercase
                                            @php
                                                $sc = $item['statusCode'] ?? '';
                                                $colors = match(true) {
                                                    $sc === 'PROSES' || $sc === 'dalam pengiriman' => 'bg-amber-100 text-amber-800 border border-amber-300',
                                                    $sc === 'MENGANTRI' || $sc === 'draft' || $sc === 'DRAFT' => 'bg-slate-100 text-slate-700 border border-slate-300',
                                                    $sc === 'SELESAI' || $sc === 'selesai' => 'bg-emerald-100 text-emerald-800 border border-emerald-300',
                                                    $sc === 'BELUM_CEK' || $sc === 'BELUM' => 'bg-rose-100 text-rose-800 border border-rose-300',
                                                    default => 'bg-gray-100 text-gray-700 border border-gray-300',
                                                };
                                            @endphp
                                            {{ $colors }}">
                                            <span>{{ $item['statusIcon'] ?? '❓' }}</span>
                                            <span>{{ $item['statusText'] ?? $sc }}</span>
                                        </span>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr><td colspan="{{ count($card['cols']) }}" class="py-6 text-center text-slate-400 text-xs">Tidak ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="pt-2 border-t border-slate-100 flex justify-between items-center text-[11px] font-medium text-slate-400">
                            <span>{{ $card['legend'] }}</span>
                            <span class="text-navy font-bold">{{ $card['footerNote'] }}</span>
                        </div>
                    </div>
                </section>
                @endif
            @endforeach
        </div>
    </main>

    <script>
        function tvBoardApp() {
            return {
                currentTime: '', currentDate: '',
                supplierArrivals: @json($supplierArrivals),
                branchDeliveries: @json($branchDeliveries),
                shipmentSj: @json($shipmentSj),
                supplierInvoices: @json($supplierInvoices),
                marqueeMessage: @json($marquee),
                initApp() {
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);
                    setInterval(() => { location.reload(); }, {{ $refresh }}000);
                },
                updateClock() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('id-ID', { hour12: false });
                    const opts = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    this.currentDate = now.toLocaleDateString('id-ID', opts);
                },
                toggleFullscreen() {
                    if (!document.fullscreenElement) { document.documentElement.requestFullscreen().catch(() => {}); }
                    else { if (document.exitFullscreen) document.exitFullscreen(); }
                }
            }
        }
    </script>
</body>
</html>
