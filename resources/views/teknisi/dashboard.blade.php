<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Teknisi - Daftar Tugas') }}
        </h2>
    </x-slot>


    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-shadow hover:shadow-md">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Laporan</p>
                                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $totalReports ?? 0 }}</p>
                                </div>
                                <div class="p-3 bg-indigo-100 dark:bg-indigo-900/20 rounded-lg">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-shadow hover:shadow-md">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Laporan Pending</p>
                                    <p class="mt-2 text-3xl font-bold text-red-500">{{ $pendingReports ?? 0 }}</p>
                                </div>
                                <div class="p-3 bg-red-100 dark:bg-red-900/20 rounded-lg">
                                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 000-20z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-shadow hover:shadow-md">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rata-rata Waktu Selesai</p>
                                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $avgDurationLabel ?? '—' }}</p>
                                </div>
                                <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <x-filter-bar
                    :action="route('teknisi.dashboard')"
                    :date-from="request('date_from')"
                    :date-to="request('date_to')"
                    :filter-category="request('kategori')"
                    :search="request('search')"
                    :categories="['kerusakan', 'peminjaman', 'instalasi']"
                />

                {{-- ============================== --}}
                {{-- SECTION: MENUNGGU KEPUTUSAN KOMPLAIN --}}
                {{-- ============================== --}}
                @php
                    $tasksWaitingComplaintSafe = $tasksWaitingComplaint ?? collect();
                @endphp

                @if($tasksWaitingComplaintSafe->count() > 0)
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Menunggu Keputusan Komplain</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    Tugas ini sedang berstatus <span class="font-semibold">complain</span> dan menunggu keputusan Kepala IT. Anda belum bisa mulai/menyelesaikan.
                                </p>
                            </div>
                        </div>

                        <div class="overflow-x-auto -mx-6 md:mx-0">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900/50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pelapor</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deskripsi</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Komplain</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($tasksWaitingComplaintSafe as $task)
                                        <tr class="bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100/40 dark:hover:bg-amber-900/30 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $task->nama_pelapor }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $task->reporter->email ?? '' }}</div>
                                            </td>

                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                                    {{ ucfirst($task->kategori) }}
                                                </span>
                                            </td>

                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 dark:text-white max-w-md truncate" title="{{ $task->deskripsi_pengajuan }}">
                                                    {{ $task->deskripsi_pengajuan }}
                                                </div>
                                            </td>

                                            <td class="px-6 py-4">
                                                @if($task->complaint_note)
                                                    <div class="text-sm text-gray-900 dark:text-white max-w-md truncate">
                                                        {{ $task->complaint_note }}
                                                    </div>
                                                    <div class="mt-1 text-xs text-amber-700 dark:text-amber-300">
                                                        Komplain ke-{{ $task->complaint_count ?? 0 }}
                                                    </div>
                                                @else
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">—</span>
                                                @endif
                                            </td>

                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <x-status-badge :status="$task->status" />
                                            </td>

                                            {{-- ✅ BARU: tombol lihat detail (read-only) --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('teknisi.task.detail', $task) }}"
                                                   class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-700 text-white text-sm font-medium hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                                                    Lihat Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- ============================== --}}
                {{-- SECTION: TUGAS AKTIF --}}
                {{-- ============================== --}}
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tugas Aktif</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tugas yang sedang Anda kerjakan</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto -mx-6 md:mx-0">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pelapor</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deskripsi</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($tasksActive as $task)
                                @php
                                    // ✅ Jika pernah komplain sebelumnya (complaint_count > 0),
                                    // dan sekarang sudah kembali accepted/on_process, artinya ini REWORK.
                                    $isRework = (($task->complaint_count ?? 0) > 0) && in_array($task->status, ['accepted','on_process']);
                                @endphp

                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $task->nama_pelapor }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $task->reporter->email ?? '' }}</div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                                {{ ucfirst($task->kategori) }}
                                            </span>

                                            @if($isRework)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200">
                                                    REWORK
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white max-w-md truncate" title="{{ $task->deskripsi_pengajuan }}">
                                            {{ $task->deskripsi_pengajuan }}
                                        </div>

                                        @if($isRework && $task->complaint_note)
                                            <div class="mt-2 text-xs text-amber-700 dark:text-amber-300 italic" title="Alasan Komplain Terakhir">
                                                <span class="font-bold">Komplain terakhir:</span> {{ $task->complaint_note }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-status-badge :status="$task->status" />
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if($task->status == 'accepted')
                                            <form method="POST" action="{{ route('teknisi.task.start', $task) }}" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center px-3 py-1.5 rounded-lg
                                                           {{ $isRework ? 'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500' : 'bg-green-600 hover:bg-green-700 focus:ring-green-500' }}
                                                           text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition">
                                                    {{ $isRework ? 'Kerjakan Ulang' : 'Mulai Kerjakan' }}
                                                </button>
                                            </form>
                                        @elseif($task->status == 'on_process')
                                            <a href="{{ route('teknisi.task.completeForm', $task) }}"
                                               class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                                Selesaikan Pekerjaan
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">Anda belum memiliki tugas aktif.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ============================== --}}
                {{-- SECTION: TUGAS TERSEDIA --}}
                {{-- ============================== --}}
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tugas Tersedia</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ambil tugas yang tersedia</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto -mx-6 md:mx-0">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Laporan Masuk</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pelapor</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($tasksClaimable as $task)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white max-w-md truncate">{{ $task->deskripsi_pengajuan }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                            {{ ucfirst($task->kategori) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $task->nama_pelapor }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form method="POST" action="{{ route('teknisi.task.claim', $task) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                                Ambil Tugas
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada tugas yang tersedia untuk diklaim.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
