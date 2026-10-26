<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat Laporan') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 md:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Riwayat Laporan Anda</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Semua laporan yang telah selesai, ditolak, atau diberi rating.
                        </p>
                    </div>
                </div>

                <x-filter-bar
                    :action="route('admin.report.history')"
                    :date-from="request('date_from')"
                    :date-to="request('date_to')"
                    :filter-status="request('filter_status')"
                    :filter-category="request('filter_category')"
                    :search="request('search')"
                    :statuses="['completed', 'rated', 'rejected']"
                    :categories="['kerusakan', 'peminjaman', 'instalasi']"
                />

                <div class="overflow-x-auto -mx-6 md:mx-0">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Kategori
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Deskripsi
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Catatan
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($laporan as $report)
                                @php
                                    // Komplain terakhir (jika ada) untuk ditampilkan di kolom STATUS / fallback catatan
                                    $latestComplaint = $report->latestComplaint;

                                    $complaintStatus = $latestComplaint?->status;
                                    $complaintStatusLabel = match ($complaintStatus) {
                                        'submitted' => 'Menunggu',
                                        'approved'  => 'Disetujui',
                                        'rejected'  => 'Ditolak',
                                        default     => $complaintStatus ? ucfirst($complaintStatus) : null,
                                    };

                                    // ✅ Ambil keputusan komplain:
                                    $complaintDecisionText = trim((string) ($report->complaint_decision_note ?? ''));
                                    if ($complaintDecisionText === '' && $latestComplaint && $latestComplaint->decision_note) {
                                        $complaintDecisionText = trim((string) $latestComplaint->decision_note);
                                    }

                                    // ✅ note utama:
                                    $note = $complaintDecisionText !== ''
                                        ? $complaintDecisionText
                                        : trim((string) ($report->status_note ?? ''));

                                    $isComplaintDecision = $complaintDecisionText !== '';

                                    // Pecah note berdasarkan titik pertama (.)
                                    $dotPos = $note !== '' ? mb_strpos($note, '.') : false;
                                    $noteTitle = $dotPos !== false ? trim(mb_substr($note, 0, $dotPos + 1)) : '';
                                    $noteReason = $dotPos !== false ? trim(mb_substr($note, $dotPos + 1)) : trim($note);

                                    $useSplit = $note !== '' && $dotPos !== false && $noteReason !== '';
                                @endphp

                                {{-- ✅ ubah align-top -> align-middle supaya semua sel ikut rapi --}}
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors align-middle">
                                    {{-- KATEGORI --}}
                                    <td class="px-6 py-4 whitespace-nowrap align-middle">
                                        <div class="min-h-[72px] flex items-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                                {{ ucfirst($report->kategori) }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- DESKRIPSI --}}
                                    <td class="px-6 py-4 align-middle">
                                        <div class="min-h-[72px] flex flex-col justify-center">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white max-w-md truncate">
                                                {{ $report->deskripsi_pengajuan }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ \Carbon\Carbon::parse($report->tanggal_pengajuan)->format('d M Y') }}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- STATUS --}}
                                    <td class="px-6 py-4 whitespace-nowrap align-middle">
                                        <div class="min-h-[72px] flex flex-col justify-center gap-2">
                                            <div class="inline-flex">
                                                <x-status-badge :status="$report->status" />
                                            </div>

                                            @if($latestComplaint)
                                                <div class="flex flex-col gap-1">
                                                    <span class="inline-flex w-fit items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                                                        @if($complaintStatus === 'submitted')
                                                            bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200
                                                        @elseif($complaintStatus === 'approved')
                                                            bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200
                                                        @elseif($complaintStatus === 'rejected')
                                                            bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200
                                                        @else
                                                            bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-300
                                                        @endif
                                                    ">
                                                        Komplain: {{ $complaintStatusLabel }}
                                                    </span>

                                                    @if($latestComplaint->complaint_no)
                                                        <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                                            {{ $latestComplaint->complaint_no }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif

                                            @if($report->status == 'rated')
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Rating: {{ $report->rating }} ⭐
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- CATATAN --}}
                                    <td class="px-6 py-4 align-middle">
                                        <div class="min-h-[72px] flex flex-col justify-center">
                                            @if($note !== '')
                                                <div class="max-w-sm">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        @if($isComplaintDecision)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                                                                        bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200">
                                                                Komplain
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                                                                        bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-300">
                                                                Catatan
                                                            </span>
                                                        @endif
                                                    </div>

                                                    @if($useSplit)
                                                        @if(! $isComplaintDecision)
                                                            <div class="text-[11px] text-gray-500 dark:text-gray-400 mb-1">
                                                                {{ $noteTitle }}
                                                            </div>
                                                        @endif

                                                        <div class="text-xs text-gray-700 dark:text-gray-200 whitespace-normal break-words leading-relaxed">
                                                            {{ $noteReason }}
                                                        </div>
                                                    @else
                                                        <div class="text-xs text-gray-700 dark:text-gray-200 whitespace-normal break-words leading-relaxed">
                                                            {{ $note }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right align-middle">
                                        <div class="min-h-[72px] flex items-center justify-end">
                                            @if($report->status == 'completed')
                                                <a href="{{ route('admin.report.rateForm', $report) }}"
                                                   class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                                    Lihat &amp; Beri Rating
                                                </a>
                                            @elseif(in_array($report->status, ['rated', 'rejected']))
                                                <a href="{{ route('admin.report.show', $report) }}"
                                                   class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-500 dark:bg-gray-600 text-white text-sm font-medium hover:bg-gray-600 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                                                    Lihat Detail
                                                </a>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500 text-sm">Tidak ada aksi</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada riwayat laporan ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    {{ $laporan->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
