<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat Tugas') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 md:p-8">

                {{-- Filter --}}
                <form method="GET" action="{{ route('teknisi.task.history') }}" class="mb-6">
                    <div class="flex flex-wrap items-center justify-start gap-3 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <input type="date" name="date_from" value="{{ request('date_from') ?? ($dateFrom ?? '') }}" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                            <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">s.d.</span>
                            <input type="date" name="date_to" value="{{ request('date_to') ?? ($dateTo ?? '') }}" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                        </div>
                        <select name="filter_status" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Filter Status</option>
                            @foreach(['completed', 'rated'] as $status)
                                <option value="{{ $status }}" {{ (request('filter_status') ?? ($filterStatus ?? '')) == $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                        <select name="kategori" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Kategori</option>
                            @if(!empty($categories))
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ (request('kategori') ?? ($kategori ?? '')) == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="flex items-center gap-2">
                            <x-primary-button type="submit">Terapkan</x-primary-button>
                            <a href="{{ route('teknisi.task.history') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>

                {{-- BAGIAN RIWAYAT PENGERJAAN --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Pengerjaan</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tugas yang telah selesai dan dinilai</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto -mx-6 md:mx-0">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Laporan</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Durasi</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($tasksHistory as $task)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $task->deskripsi_pengajuan }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pelapor: {{ $task->nama_pelapor }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                            {{ ucfirst($task->kategori) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-status-badge :status="$task->status" />
                                        @if($task->status == 'rated')
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Rating: {{ $task->rating }} ⭐</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if($task->duration_minutes !== null)
                                            @php
                                                $totalMinutes = abs($task->duration_minutes);
                                                $hours = floor($totalMinutes / 60);
                                                $minutes = $totalMinutes % 60;
                                            @endphp
                                            {{ $hours }} Jam {{ $minutes }} Menit
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('teknisi.task.show', $task->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-500 dark:bg-gray-600 text-white text-sm font-medium hover:bg-gray-600 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada riwayat pekerjaan selesai.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Link Pagination --}}
                    @if($tasksHistory->hasPages())
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        {{ $tasksHistory->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
