@php
    use function Filament\Support\generate_href_html;
@endphp

<x-filament::widget>
    <div class="mb-3">
        <h2 class="text-sm font-semibold text-gray-950 dark:text-white">
            ⚡ Aktivitas Terakhir
        </h2>
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" style="border-collapse: collapse;">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5">
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">User</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Aktivitas</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activities as $act)
                        <tr class="border-t border-gray-100 dark:border-white/5">
                            <td class="px-3 py-2.5 text-sm text-gray-700 dark:text-gray-200">{{ $act['user'] }}</td>
                            <td class="px-3 py-2.5 text-sm text-gray-700 dark:text-gray-200">{{ $act['activity'] }}</td>
                            <td class="px-3 py-2.5 text-center text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($act['time'])->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada aktivitas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($lastPage > 1 || $total > 10)
            <div class="flex items-center justify-between gap-x-3 border-t border-gray-100 px-4 py-2.5 dark:border-white/10">
                <div class="flex items-center gap-x-2 text-sm text-gray-600 dark:text-gray-400">
                    <span>Baris per halaman:</span>
                    <select
                        wire:model.live="perPage"
                        class="rounded-lg border border-gray-300 bg-white px-2 py-1 text-sm text-gray-700 outline-none transition focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:focus:border-primary-400"
                    >
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="ms-2">
                        {{ ($currentPage - 1) * $perPage + 1 }}–{{ min($currentPage * $perPage, $total) }} dari {{ $total }}
                    </span>
                </div>

                <div class="flex items-center gap-x-1">
                    <button
                        wire:click="gotoPage(1)"
                        @disabled($currentPage === 1)
                        class="flex items-center justify-center rounded-lg px-2 py-1.5 text-sm text-gray-600 transition hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed dark:text-gray-400 dark:hover:bg-white/5"
                        @if ($currentPage === 1) disabled @endif
                    >
                        ««
                    </button>
                    <button
                        wire:click="gotoPage({{ $currentPage - 1 }})"
                        @disabled($currentPage === 1)
                        class="flex items-center justify-center rounded-lg px-2 py-1.5 text-sm text-gray-600 transition hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed dark:text-gray-400 dark:hover:bg-white/5"
                        @if ($currentPage === 1) disabled @endif
                    >
                        «
                    </button>

                    @php
                        $start = max(1, min($currentPage - 2, $lastPage - 4));
                        $end = min($lastPage, max(5, $currentPage + 2));
                        if ($end - $start < 4) {
                            if ($start === 1) {
                                $end = min($lastPage, $start + 4);
                            } else {
                                $start = max(1, $end - 4);
                            }
                        }
                    @endphp

                    @if ($start > 1)
                        <span class="px-2 text-sm text-gray-400">...</span>
                    @endif

                    @for ($i = $start; $i <= $end; $i++)
                        <button
                            wire:click="gotoPage({{ $i }})"
                            @class([
                                'flex items-center justify-center rounded-lg px-3 py-1.5 text-sm font-medium transition',
                                'bg-primary-600 text-white' => $i === $currentPage,
                                'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5' => $i !== $currentPage,
                            ])
                        >
                            {{ $i }}
                        </button>
                    @endfor

                    @if ($end < $lastPage)
                        <span class="px-2 text-sm text-gray-400">...</span>
                    @endif

                    <button
                        wire:click="gotoPage({{ $currentPage + 1 }})"
                        @disabled($currentPage === $lastPage)
                        class="flex items-center justify-center rounded-lg px-2 py-1.5 text-sm text-gray-600 transition hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed dark:text-gray-400 dark:hover:bg-white/5"
                        @if ($currentPage === $lastPage) disabled @endif
                    >
                        »
                    </button>
                    <button
                        wire:click="gotoPage({{ $lastPage }})"
                        @disabled($currentPage === $lastPage)
                        class="flex items-center justify-center rounded-lg px-2 py-1.5 text-sm text-gray-600 transition hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed dark:text-gray-400 dark:hover:bg-white/5"
                        @if ($currentPage === $lastPage) disabled @endif
                    >
                        »»
                    </button>
                </div>
            </div>
        @endif
    </div>
</x-filament::widget>
