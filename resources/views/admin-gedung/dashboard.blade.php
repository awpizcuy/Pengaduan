<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Admin Gedung') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        {{-- Bagian Kartu Statistik (Tidak Berubah) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Total Laporan --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-shadow hover:shadow-md">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Laporan</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $totalReports }}</p>
                        </div>
                        <div class="p-3 bg-indigo-100 dark:bg-indigo-900/20 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Laporan Pending --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-shadow hover:shadow-md">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Laporan Pending</p>
                            <p class="mt-2 text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $pendingReports }}</p>
                        </div>
                        <div class="p-3 bg-orange-100 dark:bg-orange-900/20 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Laporan Selesai --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-shadow hover:shadow-md">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Laporan Selesai</p>
                            <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">{{ $completedReports }}</p>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                           <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Laporan Ditolak --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-shadow hover:shadow-md">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Laporan Ditolak</p>
                            <p class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400">{{ $rejectedReports }}</p>
                        </div>
                        <div class="p-3 bg-red-100 dark:bg-red-900/20 rounded-lg">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 md:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Daftar Laporan Aktif Anda</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola laporan yang sedang berjalan atau butuh aksi</p>
                    </div>
                    <a href="{{ route('admin.report.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Laporan Baru
                    </a>
                </div>

                <x-filter-bar
                    :action="route('admin.dashboard')"
                    :date-from="request('date_from')"
                    :date-to="request('date_to')"
                    :filter-status="request('filter_status')"
                    :filter-category="request('filter_category')"
                    :search="request('search')"
                    :statuses="['pending', 'accepted', 'on_process', 'hold', 'complain']"
                    :categories="['kerusakan', 'peminjaman', 'instalasi']"
                />

                <div class="overflow-x-auto -mx-6 md:mx-0">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deskripsi</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($laporan as $report)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                        {{ ucfirst($report->kategori) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white max-w-md truncate">
                                        {{ $report->deskripsi_pengajuan }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ \Carbon\Carbon::parse($report->tanggal_pengajuan)->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1">
                                        <div class="inline-flex">
                                            <x-status-badge :status="$report->status" />
                                        </div>

                                        @if($report->latestComplaint)
                                            @php
                                                $cs = $report->latestComplaint->status;
                                                $csLabel = match ($cs) {
                                                    'submitted' => 'Menunggu',
                                                    'approved'  => 'Disetujui',
                                                    'rejected'  => 'Ditolak',
                                                    default     => $cs ? ucfirst($cs) : '—',
                                                };
                                            @endphp

                                            <span class="inline-flex w-fit items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                                                @if($cs === 'submitted')
                                                    bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200
                                                @elseif($cs === 'approved')
                                                    bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200
                                                @elseif($cs === 'rejected')
                                                    bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200
                                                @else
                                                    bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-300
                                                @endif
                                            ">
                                                Komplain: {{ $csLabel }}
                                            </span>
                                        @endif

                                        @if(!empty($report->complaint_decision_note))
                                            <div class="text-[11px] text-gray-500 dark:text-gray-400 max-w-[16rem] truncate" title="{{ $report->complaint_decision_note }}">
                                                {{ $report->complaint_decision_note }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($report->status == 'completed')
                                        <a href="{{ route('admin.report.rateForm', $report) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                            Lihat & Beri Rating
                                        </a>
                                    @elseif(in_array($report->status, ['rated', 'rejected']))
                                        <a href="{{ route('admin.report.show', $report) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-500 dark:bg-gray-600 text-white text-sm font-medium hover:bg-gray-600 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                                            Lihat Detail
                                        </a>
                                    @else
                                        <a href="{{ route('admin.report.show', $report) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-500 dark:bg-gray-600 text-white text-sm font-medium hover:bg-gray-600 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                                            Lihat Detail
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-gray-500 dark:text-gray-400 font-medium">Anda tidak memiliki laporan aktif.</p>
                                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Klik tombol di atas untuk membuat laporan baru</p>
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
</x-app-layout>
