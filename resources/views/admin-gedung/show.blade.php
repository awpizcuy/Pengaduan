<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detail Laporan Selesai: #{{ $report->id }} - {{ ucfirst($report->kategori) }}
        </h2>
    </x-slot>

    @php
        $note = trim((string) ($report->status_note ?? ''));
        $isComplaintDecision = $note !== '' && \Illuminate\Support\Str::startsWith($note, 'Komplain ');

        // ✅ Dedup rule:
        // Jika status = rejected/hold dan ada status_note → tampilkan kotak alasan saja (jangan tampilkan "Catatan Kepala IT")
        $showReasonBox = in_array($report->status, ['rejected', 'hold']) && $note !== '';
        // Selain itu, kalau ada note → tampilkan "Catatan Kepala IT"
        $showHeadNoteBox = ($note !== '') && !$showReasonBox;
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Card 1: Detail Laporan Awal -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detail Laporan Awal</h3>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Laporan</p>
                            <div class="mt-1"><x-status-badge :status="$report->status" /></div>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pengajuan</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ \Carbon\Carbon::parse($report->tanggal_pengajuan)->isoFormat('dddd, D MMMM YYYY, HH:mm') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nomor Telepon</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $report->no_telp ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi Masalah</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $report->deskripsi_pengajuan }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Foto Kondisi Awal</p>
                        @if($report->foto_awal)
                            <img
                                src="{{ asset('storage/' . $report->foto_awal) }}"
                                alt="Foto Kerusakan Awal"
                                class="mt-2 rounded-lg max-h-64 border dark:border-gray-700 open-image-modal cursor-pointer"
                            />
                        @else
                            <div class="mt-2 h-40 rounded-lg border border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center text-sm text-gray-500">
                                Tidak ada foto kondisi awal
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ✅ 1) Jika ada komplain terakhir (tetap seperti punya kamu) --}}
                @if(isset($latestComplaint) && $latestComplaint)
                    <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="p-4 rounded-lg border
                            @if($latestComplaint->status === 'submitted')
                                bg-yellow-50 dark:bg-yellow-900/30 border-yellow-200 dark:border-yellow-800
                            @elseif($latestComplaint->status === 'approved')
                                bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-800
                            @elseif($latestComplaint->status === 'rejected')
                                bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800
                            @else
                                bg-gray-50 dark:bg-gray-900/30 border-gray-200 dark:border-gray-700
                            @endif
                        ">
                            <h4 class="text-md font-bold text-gray-900 dark:text-gray-100">
                                Hasil Komplain Terakhir
                            </h4>

                            <div class="mt-2 text-sm text-gray-800 dark:text-gray-200 space-y-1">
                                <p><strong>No Komplain:</strong> {{ $latestComplaint->complaint_no }}</p>
                                <p><strong>Urutan:</strong> Komplain ke-{{ $latestComplaint->sequence }}</p>

                                <p>
                                    <strong>Status:</strong>
                                    @if($latestComplaint->status === 'submitted')
                                        <span class="font-semibold text-yellow-700 dark:text-yellow-300">Menunggu keputusan</span>
                                    @elseif($latestComplaint->status === 'approved')
                                        <span class="font-semibold text-green-700 dark:text-green-300">Disetujui</span>
                                    @elseif($latestComplaint->status === 'rejected')
                                        <span class="font-semibold text-red-700 dark:text-red-300">Ditolak</span>
                                    @else
                                        <span class="font-semibold">{{ $latestComplaint->status }}</span>
                                    @endif
                                </p>

                                <p class="mt-2">
                                    <strong>Isi Komplain:</strong><br>
                                    <span class="whitespace-pre-wrap">{{ $latestComplaint->message }}</span>
                                </p>

                                @if($latestComplaint->decision_note)
                                    <p class="mt-2">
                                        <strong>Catatan Kepala IT:</strong><br>
                                        <span class="whitespace-pre-wrap">{{ $latestComplaint->decision_note }}</span>
                                    </p>
                                @endif

                                @if($latestComplaint->decided_at)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                        Diputuskan pada:
                                        {{ \Carbon\Carbon::parse($latestComplaint->decided_at)->isoFormat('dddd, D MMMM YYYY, HH:mm') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- 2) Alasan reject/hold (hanya tampil untuk rejected/hold) --}}
                @if ($showReasonBox)
                    <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="p-4 rounded-lg {{ $report->status == 'rejected' ? 'bg-red-50 dark:bg-red-900/30' : 'bg-yellow-50 dark:bg-yellow-900/30' }}">
                            <div class="flex items-center gap-2">
                                <h4 class="text-md font-bold {{ $report->status == 'rejected' ? 'text-red-800 dark:text-red-200' : 'text-yellow-800 dark:text-yellow-200' }}">
                                    {{ $report->status == 'rejected' ? 'Alasan Laporan Ditolak' : 'Alasan Laporan Ditunda' }}
                                </h4>

                                {{-- Label sumber --}}
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

                            <p class="mt-2 text-sm {{ $report->status == 'rejected' ? 'text-red-700 dark:text-red-300' : 'text-yellow-700 dark:text-yellow-300' }} whitespace-pre-wrap leading-relaxed">{{ $note }}
                            </p>
                        </div>
                    </div>
                @endif

                {{-- ✅ 3) Catatan Kepala IT (hanya tampil kalau BUKAN rejected/hold) --}}
                @if($showHeadNoteBox)
                    <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30">
                            <div class="flex items-center gap-2">
                                <h4 class="text-md font-bold text-gray-900 dark:text-gray-100">Catatan Kepala IT</h4>

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

                            <p class="mt-2 text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed">{{ $note }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Card 2: Hasil Penyelesaian -->
            @if($report->resolution)
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Hasil Penyelesaian Teknisi</h3>
                    </div>

                    <div class="p-6">
                        <div class="space-y-4 mb-6">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Teknisi yang Mengerjakan</p>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $report->technician->name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Waktu Mulai</p>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $report->start_time ? \Carbon\Carbon::parse($report->start_time)->isoFormat('D MMM YYYY, HH:mm') : '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Waktu Selesai</p>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $report->end_time ? \Carbon\Carbon::parse($report->end_time)->isoFormat('D MMM YYYY, HH:mm') : '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Durasi Pengerjaan</p>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $report->duration_minutes ? $report->duration_minutes . ' Menit' : '-' }}</p>
                            </div>
                        </div>

                        <div class="space-y-4 mb-6">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi Pekerjaan</p>
                                <p class="mt-1 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border dark:border-gray-700 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $report->resolution->deskripsi_pekerjaan }}
                                </p>
                            </div>

                            @if($report->resolution->barang)
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Barang yang Digunakan</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $report->resolution->barang }} (Qty: {{ $report->resolution->qty ?? 0 }})
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Foto Sebelum</p>
                                @if($report->resolution->foto_before)
                                    <img
                                        src="{{ asset('storage/' . $report->resolution->foto_before) }}"
                                        alt="Foto Sebelum"
                                        class="mt-2 rounded-lg border dark:border-gray-700 max-h-96 open-image-modal cursor-pointer"
                                    />
                                @else
                                    <div class="mt-2 h-48 rounded-lg border border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center text-sm text-gray-500">
                                        Tidak ada foto
                                    </div>
                                @endif
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Foto Sesudah</p>
                                @if($report->resolution->foto_after)
                                    <img
                                        src="{{ asset('storage/' . $report->resolution->foto_after) }}"
                                        alt="Foto Sesudah"
                                        class="mt-2 rounded-lg border dark:border-gray-700 max-h-96 open-image-modal cursor-pointer"
                                    />
                                @else
                                    <div class="mt-2 h-48 rounded-lg border border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center text-sm text-gray-500">
                                        Tidak ada foto
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Card 3: Feedback Anda -->
            @if($report->status == 'rated')
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Feedback Anda</h3>
                    </div>

                    <div class="p-6">
                    <div class="flex items-center gap-1 mt-1 py-1 overflow-visible">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg
                                class="block w-6 h-6 shrink-0 {{ $i <= $report->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                                fill="currentColor"
                                viewBox="-2 -2 24 24"
                                style="overflow: visible;"
                                aria-hidden="true"
                            >
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.835 5.641a1 1 0 00.95.69h5.92c.969 0 1.371 1.24.588 1.81l-4.79 3.478a1 1 0 00-.364 1.118l1.835 5.641c.3.921-.755 1.688-1.54 1.118l-4.79-3.478a1 1 0 00-1.175 0l-4.79 3.478c-.784.57-1.839-.197-1.54-1.118l1.835-5.641a1 1 0 00-.364-1.118L.93 11.068c-.783-.57-.38-1.81.588-1.81h5.92a1 1 0 00.95-.69L9.049 2.927z"></path>
                            </svg>
                        @endfor

                        <span class="ml-2 text-sm font-medium leading-none text-gray-900 dark:text-gray-100">
                            ({{ $report->rating }}/5)
                        </span>
                    </div>
                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Feedback Anda</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 italic">
                                "{{ $report->rating_feedback ?? '(Tidak ada feedback)' }}"
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($report->status == 'rejected')
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <div class="p-6 text-sm text-red-600 dark:text-red-400">
                        Laporan ini ditolak oleh Kepala IT.
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 flex items-center justify-start">
                    <a href="{{ route('admin.dashboard') }}">
                        <x-secondary-button>
                            &larr; Kembali ke Dashboard
                        </x-secondary-button>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="relative p-4 max-w-3xl max-h-full">
            <button id="closeImageModal" class="absolute top-2 right-2 text-white text-3xl font-bold">&times;</button>
            <img id="modalImage" src="" alt="Enlarged Image" class="max-w-full max-h-[90vh] object-contain">
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const imageModal = document.getElementById('imageModal');
            if (imageModal) {
                const modalImage = document.getElementById('modalImage');
                const closeImageModal = document.getElementById('closeImageModal');

                document.querySelectorAll('.open-image-modal').forEach(img => {
                    img.addEventListener('click', function(e) {
                        e.preventDefault();
                        modalImage.src = this.src;
                        imageModal.classList.remove('hidden');
                    });
                });

                closeImageModal.addEventListener('click', function() {
                    imageModal.classList.add('hidden');
                    modalImage.src = '';
                });

                imageModal.addEventListener('click', function(e) {
                    if (e.target === imageModal) {
                        imageModal.classList.add('hidden');
                        modalImage.src = '';
                    }
                });
            }
        });
    </script>
</x-app-layout>
