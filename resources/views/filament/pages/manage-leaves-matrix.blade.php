<style>
    .fi-main {
        padding-inline: 0 !important;
    }
</style>
@php
    $hariIni = now();
    $jenisWarna = ['Cuti' => '#f43f5e', 'Sakit' => '#eab308', 'Izin' => '#3b82f6'];
    $jenisLabel = ['Cuti' => 'C', 'Sakit' => 'S', 'Izin' => 'I'];
@endphp
{{-- Legend --}}
<div class="flex flex-wrap items-center gap-3 pb-2 text-xs text-gray-400">
    <span class="italic">klik badge hapus</span>
    <span class="text-gray-300 dark:text-gray-600">|</span>
    @foreach (['Cuti' => '#f43f5e', 'Sakit' => '#eab308', 'Izin' => '#3b82f6'] as $label => $warna)
    <span class="flex items-center gap-1">
        <span class="inline-flex items-center justify-center w-4 h-4 rounded text-white text-[10px] font-bold" style="background:{{ $warna }}">{{ substr($label, 0, 1) }}</span>
        <span class="text-gray-600 dark:text-gray-400">{{ $label }}</span>
    </span>
    @endforeach
</div>

{{-- Table --}}
<div class="w-full border-t border-gray-200 dark:border-gray-700">
    <div style="max-height:70vh;overflow:auto">
        <table class="fi-ta-table w-full" style="table-layout:auto;width:100%">
            <thead>
                <tr>
                    <th class="fi-ta-header-cell sticky left-0 z-20 text-left" style="min-width:165px">
                        <span>Karyawan</span>
                    </th>
                    @foreach ($calendar as $day)
                        @php $isWeekend = in_array($hariIni->month($bulan)->day($day)->dayOfWeek, [0, 6]); @endphp
                        <th class="fi-ta-header-cell text-center {{ $isWeekend ? 'fi-text-color-danger bg-red-50/30 dark:bg-red-900/10' : '' }}" style="min-width:34px;max-width:34px">
                            <span>{{ str_pad($day, 2, '0', STR_PAD_LEFT) }}</span>
                        </th>
                    @endforeach
                    <th class="fi-ta-header-cell text-center sticky right-0 z-20 border-l border-gray-200 dark:border-gray-700" style="min-width:75px">
                        <span>Sisa</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $index => $emp)
                    <tr class="fi-ta-row {{ $index % 2 === 1 ? 'fi-striped' : '' }}">
                        <td class="fi-ta-cell sticky left-0 z-10" style="background:inherit">
                            <div class="fi-ta-col flex justify-start text-start">
                                <div class="fi-ta-text-item fi-ta-text text-sm font-medium text-gray-800 dark:text-gray-200">{{ $emp['nama'] }}</div>
                            </div>
                        </td>
                        @foreach ($calendar as $day)
                            @php
                                $jenis = $emp['leave_days'][$day] ?? null;
                                $dateStr = $hariIni->month($bulan)->day($day)->format('Y-m-d');
                                $isWeekend = in_array($hariIni->month($bulan)->day($day)->dayOfWeek, [0, 6]);
                            @endphp
                            <td class="fi-ta-cell text-center {{ $isWeekend ? 'bg-red-50/40 dark:bg-red-900/5' : '' }}" style="min-width:34px;max-width:34px">
                                <div class="fi-ta-col flex justify-center text-center">
                                    @if ($jenis)
                                        <button x-on:click="if (confirm('Hapus {{ strtolower($jenis) }} tgl {{ str_pad($day, 2, '0', STR_PAD_LEFT) }}/{{ str_pad($bulan, 2, '0', STR_PAD_LEFT) }}/{{ $tahun }}?')) $wire.deleteLeave({{ $emp['id'] }}, '{{ $dateStr }}')"
                                            class="inline-flex items-center justify-center w-6 h-6 rounded text-white text-xs font-bold cursor-pointer hover:opacity-80 hover:scale-110 transition-all shadow-sm"
                                            style="background:{{ $jenisWarna[$jenis] ?? '#6b7280' }}" title="{{ $jenis }}">
                                            {{ $jenisLabel[$jenis] ?? '?' }}
                                        </button>
                                    @else
                                        <span class="fi-ta-text-item fi-ta-text text-gray-200 dark:text-gray-600 select-none">·</span>
                                    @endif
                                </div>
                            </td>
                        @endforeach
                        <td class="fi-ta-cell text-center sticky right-0 z-10 border-l border-gray-200 dark:border-gray-700" style="background:inherit">
                            <div class="fi-ta-col flex justify-center text-center">
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
