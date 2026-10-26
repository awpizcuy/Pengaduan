<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detail Tugas Selesai: #{{ $report->id }} - {{ ucfirst($report->kategori) }}
        </h2>
    </x-slot>

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
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pelapor</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $report->nama_pelapor }} ({{ $report->reporter->email }})</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nomor Telepon</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $report->no_telp ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pengajuan</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($report->tanggal_pengajuan)->isoFormat('dddd, D MMMM YYYY, HH:mm') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi Masalah</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $report->deskripsi_pengajuan }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Foto Kondisi Awal</p>
                        @if($report->foto_awal)
                            <img src="{{ asset('storage/' . $report->foto_awal) }}" alt="Foto Kerusakan Awal" class="mt-2 rounded-lg max-h-64 border dark:border-gray-700 open-image-modal cursor-pointer" />
                        @else
                            <div class="mt-2 h-40 rounded-lg border border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center text-sm text-gray-500">Tidak ada foto kondisi awal</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 2: Hasil Pekerjaan Anda -->
            @if($report->resolution)
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Hasil Pekerjaan Anda</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Waktu Mulai</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $report->start_time ? \Carbon\Carbon::parse($report->start_time)->isoFormat('D MMM YYYY, HH:mm') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Waktu Selesai</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $report->end_time ? \Carbon\Carbon::parse($report->end_time)->isoFormat('D MMM YYYY, HH:mm') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Durasi Pengerjaan</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $report->duration_minutes ? $report->duration_minutes . ' Menit' : '-' }}</p>
                        </div>
                    </div>
                    <div class="space-y-4 mb-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi Pekerjaan Anda</p>
                            <p class="mt-1 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border dark:border-gray-700 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $report->resolution->deskripsi_pekerjaan }}</p>
                        </div>
                        @if($report->resolution->barang)
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Barang yang Digunakan</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $report->resolution->barang }} (Qty: {{ $report->resolution->qty ?? 0 }})</p>
                        </div>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Foto Sebelum</p>
                            @if($report->resolution->foto_before)
                                <img src="{{ asset('storage/' . $report->resolution->foto_before) }}" alt="Foto Sebelum" class="mt-2 rounded-lg border dark:border-gray-700 max-h-96 open-image-modal cursor-pointer" />
                            @else
                                <div class="mt-2 h-48 rounded-lg border border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center text-sm text-gray-500">Tidak ada foto</div>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Foto Sesudah</p>
                            @if($report->resolution->foto_after)
                                <img src="{{ asset('storage/' . $report->resolution->foto_after) }}" alt="Foto Sesudah" class="mt-2 rounded-lg border dark:border-gray-700 max-h-96 open-image-modal cursor-pointer" />
                            @else
                                <div class="mt-2 h-48 rounded-lg border border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center text-sm text-gray-500">Tidak ada foto</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Card 3: Feedback dari Pelapor -->
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
                </div>
            </div>
            @else
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 text-sm text-gray-500 dark:text-gray-400">
                    Pelapor belum memberikan rating untuk pekerjaan ini.
                </div>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    </div>
                <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('kepala.dashboard') }}"
                       class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium
                                  hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                        &larr; Kembali ke Dashboard
                    </a>
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
