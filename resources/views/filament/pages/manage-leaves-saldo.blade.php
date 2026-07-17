<div class="w-full">
    <div class="overflow-x-auto">
        <table class="fi-ta-table w-full">
            <thead>
                <tr>
                    <th class="fi-ta-header-cell text-left" style="min-width:200px">
                        <span>Karyawan</span>
                    </th>
                    <th class="fi-ta-header-cell text-center" style="min-width:120px">
                        <span>Jatah Cuti Awal</span>
                    </th>
                    <th class="fi-ta-header-cell text-center" style="min-width:120px">
                        <span>Cuti Terpakai</span>
                    </th>
                    <th class="fi-ta-header-cell text-center" style="min-width:100px">
                        <span>Sisa Cuti</span>
                    </th>
                    <th class="fi-ta-header-cell text-center" style="min-width:100px">
                        <span>Aksi</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->saldoData as $index => $item)
                    <tr class="fi-ta-row {{ $index % 2 === 1 ? 'fi-striped' : '' }}">
                        <td class="fi-ta-cell">
                            <div class="fi-ta-col flex justify-start text-start">
                                <div class="fi-ta-text-item fi-ta-text text-sm font-medium text-gray-800 dark:text-gray-200">
                                    {{ $item['nama'] }}
                                </div>
                            </div>
                        </td>
                        <td class="fi-ta-cell text-center">
                            <div class="fi-ta-col flex justify-center text-center">
                                <div class="fi-ta-text-item fi-ta-text text-sm text-gray-700 dark:text-gray-300">
                                    {{ $item['jatah_cuti'] }}
                                </div>
                            </div>
                        </td>
                        <td class="fi-ta-cell text-center">
                            <div class="fi-ta-col flex justify-center text-center">
                                <div class="fi-ta-text-item fi-ta-text text-sm text-gray-700 dark:text-gray-300">
                                    {{ $item['cuti_terpakai'] }}
                                </div>
                            </div>
                        </td>
                        <td class="fi-ta-cell text-center">
                            <div class="fi-ta-col flex justify-center text-center">
                                <div class="fi-ta-text-item fi-ta-text text-sm font-semibold"
                                    style="color: {{ $item['sisa_cuti'] < 3 ? '#ef4444' : ($item['sisa_cuti'] < 7 ? '#f59e0b' : '#22c55e') }}">
                                    {{ $item['sisa_cuti'] }}
                                </div>
                            </div>
                        </td>
                        <td class="fi-ta-cell text-center">
                            <div class="fi-ta-col flex justify-center text-center">
                                <button
                                    x-on:click="
                                        let val = prompt('Jatah cuti untuk {{ $item['nama'] }}:', '{{ $item['jatah_cuti'] }}');
                                        if (val !== null && val !== '' && !isNaN(val) && parseInt(val) >= 0) {
                                            $wire.adjustJatahCuti({{ $item['id'] }}, parseInt(val));
                                        } else if (val !== null && val !== '') {
                                            alert('Masukkan angka yang valid.');
                                        }
                                    "
                                    class="fi-btn fi-size-sm fi-color-info inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium transition-all hover:bg-opacity-80"
                                    style="background:#3b82f6;color:white">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                    Adjust
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="fi-ta-cell text-center text-gray-400" colspan="5">
                            <div class="fi-ta-col flex justify-center text-center py-8">
                                Belum ada data karyawan
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
