<style>
    .fi-main {
        padding-inline: 0 !important;
    }
    /* ── Sticky Header Matrix ── */
    .leave-matrix-scroll {
        max-height: 70vh;
        overflow: auto;
    }
    .leave-matrix-table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .leave-matrix-table thead th {
        position: sticky;
        top: 0;
        z-index: 3;
        background: #f8fafc;
        box-shadow: inset 0 -1px 0 rgba(15, 23, 42, 0.08);
    }
    .dark .leave-matrix-table thead th {
        background: #1e293b;
    }
    .leave-matrix-table thead th.leave-corner-left {
        left: 0;
        z-index: 4;
        box-shadow: 0 1px 3px rgba(0,0,0,0.15);
    }
    .leave-matrix-table thead th.leave-corner-right {
        right: 0;
        z-index: 4;
        box-shadow: 0 1px 3px rgba(0,0,0,0.15);
    }
    .leave-matrix-table tbody td.leave-col-left {
        left: 0;
        z-index: 2;
    }
    .leave-matrix-table tbody td.leave-col-right {
        right: 0;
        z-index: 2;
    }
</style>
@php
    $hariIni = now();
    $jenisWarna = ['Cuti' => '#f43f5e', 'Sakit' => '#eab308', 'Izin' => '#3b82f6'];
    $jenisLabel = ['Cuti' => 'C', 'Sakit' => 'S', 'Izin' => 'I'];
    $employees = $this->employees;
    $calendar = $this->calendar;
    $bulan = (int) $this->bulan;
    $tahun = (int) $this->tahun;
    $canDeleteAbsen = auth()->user()?->can('delete_cuti_absensi') ?? false;
@endphp
{{-- Legend --}}
<div class="flex flex-wrap items-center gap-3 pb-2 text-xs text-gray-400">
    @if ($canDeleteAbsen)
        <span class="italic">klik badge hapus</span>
        <span class="text-gray-300 dark:text-gray-600">|</span>
    @endif
    @foreach (['Cuti' => '#f43f5e', 'Sakit' => '#eab308', 'Izin' => '#3b82f6'] as $label => $warna)
    <span class="flex items-center gap-1">
        <span class="inline-flex items-center justify-center w-4 h-4 rounded text-white text-[10px] font-bold" style="background:{{ $warna }}">{{ substr($label, 0, 1) }}</span>
        <span class="text-gray-600 dark:text-gray-400">{{ $label }}</span>
    </span>
    @endforeach
</div>

{{-- Table --}}
<div class="w-full border-t border-gray-200 dark:border-gray-700">
    <div class="leave-matrix-scroll">
        <table class="fi-ta-table leave-matrix-table w-full" style="table-layout:auto;width:100%">
            <thead>
                <tr>
                    <th class="fi-ta-header-cell leave-corner-left text-left" style="min-width:165px">
                        <span>Karyawan</span>
                    </th>
                    @foreach ($calendar as $day)
                        <th class="fi-ta-header-cell text-center" style="min-width:34px;max-width:34px">
                            <span>{{ str_pad($day, 2, '0', STR_PAD_LEFT) }}</span>
                        </th>
                    @endforeach
                    <th class="fi-ta-header-cell leave-corner-right text-center border-l border-gray-200 dark:border-gray-700" style="min-width:75px">
                        <span>Sisa</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $index => $emp)
                    <tr class="fi-ta-row {{ $index % 2 === 1 ? 'fi-striped' : '' }}">
                        <td class="fi-ta-cell leave-col-left" style="background:inherit">
                            <div class="fi-ta-col flex justify-start text-start">
                                <div class="fi-ta-text-item fi-ta-text text-sm font-medium text-gray-800 dark:text-gray-200">{{ $emp['nama'] }}</div>
                            </div>
                        </td>
                        @foreach ($calendar as $day)
                            @php
                                $jenis = $emp['leave_days'][$day] ?? null;
                                $dateStr = $hariIni->month($bulan)->day($day)->format('Y-m-d');
                            @endphp
                            <td class="fi-ta-cell text-center align-middle" style="min-width:34px;max-width:34px">
                                <div class="fi-ta-col flex justify-center text-center items-center">
                                    @if ($jenis)
                                        @if ($canDeleteAbsen)
                                            <button x-on:click="if (confirm('Hapus {{ strtolower($jenis) }} tgl {{ str_pad($day, 2, '0', STR_PAD_LEFT) }}/{{ str_pad($bulan, 2, '0', STR_PAD_LEFT) }}/{{ $tahun }}?')) $wire.deleteLeave({{ $emp['id'] }}, '{{ $dateStr }}')"
                                                class="inline-flex items-center justify-center w-6 h-6 rounded text-white text-xs font-bold cursor-pointer hover:opacity-80 hover:scale-110 transition-all shadow-sm"
                                                style="background:{{ $jenisWarna[$jenis] ?? '#6b7280' }}" title="{{ $jenis }}">
                                                {{ $jenisLabel[$jenis] ?? '?' }}
                                            </button>
                                        @else
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded text-white text-xs font-bold"
                                                style="background:{{ $jenisWarna[$jenis] ?? '#6b7280' }}">{{ $jenisLabel[$jenis] ?? '?' }}</span>
                                        @endif
                                    @else
                                        <span class="fi-ta-text-item fi-ta-text text-gray-200 dark:text-gray-600 select-none">·</span>
                                    @endif
                                </div>
                            </td>
                        @endforeach
                        <td class="fi-ta-cell leave-col-right text-center align-middle border-l border-gray-200 dark:border-gray-700" style="background:inherit">
                            <div class="fi-ta-col flex justify-center text-center items-center">
                                <div class="fi-ta-text-item fi-ta-text text-sm font-semibold"
                                    style="{{ $emp['sisa_cuti'] < 3 ? 'color:#ef4444' : ($emp['sisa_cuti'] < 7 ? 'color:#f59e0b' : 'color:#22c55e') }}">
                                    {{ $emp['sisa_cuti'] }}
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="fi-ta-cell text-center text-gray-400" colspan="{{ count($calendar) + 2 }}">
                            <div class="fi-ta-col flex justify-center text-center py-8">Belum ada data</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
