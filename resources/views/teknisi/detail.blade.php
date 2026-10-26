<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detail Tugas (Read-only): #{{ $report->id }} - {{ ucfirst($report->kategori) }}
        </h2>
    </x-slot>

    @php
        // Komplain yang ditampilkan di card "Informasi Komplain":
        // Prioritas: komplain aktif (submitted) -> komplain terakhir yang sudah diputus -> fallback variabel lama kalau ada
        $complaint =
            $activeComplaint
            ?? $lastDecidedComplaint
            ?? ($complaint ?? ($latestComplaint ?? null));

        // Keputusan terakhir (kalau ada) untuk ditampilkan sebagai "Catatan Kepala IT" yang valid
        $decided = $lastDecidedComplaint ?? null;
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash message --}}
            @if (session('success'))
                <x-flash-message type="success">{{ session('success') }}</x-flash-message>
            @endif
            @if (session('error'))
                <x-flash-message type="error">{{ session('error') }}</x-flash-message>
            @endif

            {{-- CARD: STATUS --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Status</h3>
                </div>

                <div class="p-6 space-y-5">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Saat Ini</p>
                        <div class="mt-2">
                            <x-status-badge :status="$report->status" />
                        </div>
                    </div>

                    {{-- ✅ Catatan Kepala IT  --}}
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Catatan dari Kepala IT</p>

                        <div class="mt-2 p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                            @if($report->status === 'complain' && !empty($activeComplaint))
                                {{-- Komplain masih menunggu keputusan --}}
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    <p class="font-semibold">
                                        Menunggu keputusan komplain: {{ $activeComplaint->complaint_no }}
                                    </p>
                                    </p>
                                    <p class="mt-3 text-xs text-gray-600 dark:text-gray-400">
                                        Kepala IT belum memberikan keputusan untuk komplain ini.
                                    </p>
                                </div>

                            @elseif(!empty($decided))
                                {{-- Komplain terakhir sudah diputus --}}
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    <p class="font-semibold">
                                        Keputusan Komplain {{ $decided->complaint_no }}:
                                        <span class="{{ $decided->status === 'rejected' ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                            {{ strtoupper($decided->status) }}
                                        </span>
                                    </p>

                                    @if(!empty($decided->decision_note))
                                        <p class="mt-2 text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed">
                                            {{ $decided->decision_note }}
                                        </p>
                                    @else
                                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                            (Tidak ada catatan tambahan.)
                                        </p>
                                    @endif
                                </div>

                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">—</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD: DETAIL LAPORAN AWAL --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detail Laporan Awal</h3>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pelapor</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $report->nama_pelapor }} ({{ $report->reporter->email ?? '-' }})
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nomor Telepon Pelapor</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $report->no_telp ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pengajuan</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ \Carbon\Carbon::parse($report->tanggal_pengajuan)->isoFormat('dddd, D MMMM YYYY, HH:mm') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Kategori</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($report->kategori) }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi Masalah</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap leading-relaxed">
                                {{ $report->deskripsi_pengajuan }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Foto Kondisi Awal</p>
                        @if($report->foto_awal)
                            <img src="{{ asset('storage/' . $report->foto_awal) }}"
                                 alt="Foto Kerusakan Awal"
                                 class="mt-2 rounded-lg max-h-80 border dark:border-gray-700 open-image-modal cursor-pointer" />
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Klik gambar untuk memperbesar.</p>
                        @else
                            <div class="mt-2 h-48 rounded-lg border border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center text-sm text-gray-500">
                                Tidak ada foto kondisi awal
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- CARD: INFORMASI KOMPLAIN (read-only) --}}
            @if($complaint)
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Informasi Komplain</h3>
                    </div>

                    <div class="p-6 space-y-5">
                        {{-- Box utama komplain --}}
                        <div class="p-4 rounded-lg border border-red-200 dark:border-red-900/50 bg-red-50 dark:bg-red-900/20">
                            <div class="flex flex-col gap-1">
                                <p class="text-sm font-semibold text-red-900 dark:text-red-200">
                                    Komplain dari Pelapor • No: {{ $complaint->complaint_no ?? '-' }}
                                </p>

                                @if(!empty($complaint->message))
                                    <p class="text-sm font-semibold text-red-900 dark:text-red-200 mt-2">
                                        {{ $complaint->message }}
                                    </p>
                                @else
                                    <p class="mt-2 text-sm text-red-700 dark:text-red-300">—</p>
                                @endif

                                <p class="mt-3 text-xs text-red-700 dark:text-red-300">
                                    Catatan: Halaman ini hanya menampilkan detail (read-only). Keputusan komplain dilakukan oleh Kepala IT.
                                </p>
                            </div>
                        </div>

                        {{-- Grid info komplain  --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status Komplain</p>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ ucfirst($complaint->status ?? '-') }}
                                </p>
                            </div>

                            <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Urutan Komplain</p>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $complaint->sequence ?? ($report->complaint_count ?? '-') }}
                                </p>
                            </div>

                            <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Waktu Dibuat</p>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ !empty($complaint->created_at) ? \Carbon\Carbon::parse($complaint->created_at)->isoFormat('D MMMM YYYY, HH:mm') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- CARD: PROGRESS PENGERJAAN --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Progress Pengerjaan</h3>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Waktu Mulai</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $report->start_time ? \Carbon\Carbon::parse($report->start_time)->isoFormat('D MMMM YYYY, HH:mm') : '-' }}
                        </p>
                    </div>

                    <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Waktu Selesai</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $report->end_time ? \Carbon\Carbon::parse($report->end_time)->isoFormat('D MMMM YYYY, HH:mm') : '-' }}
                        </p>
                    </div>

                    <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Durasi</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $report->duration_minutes ? $report->duration_minutes . ' menit' : '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- CARD: HASIL PENYELESAIAN (JIKA ADA) --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Hasil Penyelesaian (Jika Ada)</h3>
                </div>

                <div class="p-6 space-y-6">
                    @if($report->resolution)
                        <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 space-y-2">
                            @if($report->resolution->barang)
                                <p class="text-sm text-gray-900 dark:text-gray-100">
                                    <span class="font-semibold">Barang:</span> {{ $report->resolution->barang }}
                                    <span class="text-gray-500 dark:text-gray-400">(Qty: {{ $report->resolution->qty ?? 1 }})</span>
                                </p>
                            @endif

                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Deskripsi Pekerjaan</p>
                            <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap leading-relaxed">
                                {{ $report->resolution->deskripsi_pekerjaan ?? '-' }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Foto Sebelum</p>
                                @if($report->resolution->foto_before)
                                    <img src="{{ asset('storage/' . $report->resolution->foto_before) }}"
                                         alt="Foto Sebelum"
                                         class="rounded-lg border dark:border-gray-700 max-h-96 open-image-modal cursor-pointer" />
                                @else
                                    <div class="h-48 rounded-lg border border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center text-sm text-gray-500">
                                        Tidak ada foto
                                    </div>
                                @endif
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Foto Sesudah</p>
                                @if($report->resolution->foto_after)
                                    <img src="{{ asset('storage/' . $report->resolution->foto_after) }}"
                                         alt="Foto Sesudah"
                                         class="rounded-lg border dark:border-gray-700 max-h-96 open-image-modal cursor-pointer" />
                                @else
                                    <div class="h-48 rounded-lg border border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center text-sm text-gray-500">
                                        Tidak ada foto
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data penyelesaian.</p>
                    @endif

                    <div>
                        <a href="{{ route('teknisi.dashboard') }}"
                           class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium
                                  hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                            &larr; Kembali ke Dashboard
                        </a>
                    </div>
                    
                </div>
            </div>

        </div>
    </div>

    {{-- Image Modal --}}
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="relative p-4 max-w-4xl max-h-full">
            <button id="closeImageModal" class="absolute top-2 right-2 text-white text-3xl font-bold">&times;</button>
            <img id="modalImage" src="" alt="Enlarged Image" class="max-w-full max-h-[90vh] object-contain">
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const imageModal = document.getElementById('imageModal');
            if (!imageModal) return;

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
        });
    </script>
</x-app-layout>
