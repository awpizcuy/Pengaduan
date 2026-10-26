<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white leading-tight">
            {{ __('Review Pekerjaan: ') }} #{{ $report->id }} - {{ $report->kategori }}
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
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pelapor</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $report->nama_pelapor }} ({{ $report->reporter->email }})</p>
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

            <!-- Card 2: Hasil Penyelesaian Teknisi -->
            @if($report->resolution)
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Hasil Penyelesaian dari Teknisi</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4 mb-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Teknisi</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $report->technician->name ?? 'N/A' }}</p>
                        </div>
                    </div>
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
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi Pekerjaan</p>
                            <p class="mt-1 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border dark:border-gray-700 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $report->resolution->deskripsi_pekerjaan }}</p>
                        </div>
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

            <!-- Card 3: Aksi Anda -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Aksi Anda</h3>
                </div>
                <div class="p-6">
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                            <button id="tab-rate" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-indigo-600 border-indigo-500">Setuju & Beri Rating</button>
                            <button id="tab-complain" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-gray-600 border-transparent">Komplain Pekerjaan</button>
                        </nav>
                    </div>

                    <!-- Rating Form Section -->
                    <div id="rate-form-section" class="tab-content mt-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Gunakan formulir ini jika pekerjaan sudah sesuai.</p>
                        <form method="POST" action="{{ route('admin.report.storeRating', $report) }}" class="mt-4">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="rating" :value="__('Rating (1-5 Bintang)')" />
                                    <select id="rating" name="rating" class="mt-1 block w-full md:w-1/2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        <option value="">Pilih Rating</option>
                                        <option value="5">⭐⭐⭐⭐⭐ (Sangat Puas)</option>
                                        <option value="4">⭐⭐⭐⭐ (Puas)</option>
                                        <option value="3">⭐⭐⭐ (Cukup)</option>
                                        <option value="2">⭐⭐ (Kurang)</option>
                                        <option value="1">⭐ (Sangat Kurang)</option>
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="rating_feedback" :value="__('Feedback / Ulasan (Opsional)')" />
                                    <textarea id="rating_feedback" name="rating_feedback" rows="4" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('rating_feedback') }}</textarea>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Batal</a>

                                {{-- ✅ Loading Submit Rating --}}
                                <x-loading-button loading-text="Menyimpan rating...">
                                    {{ __('Kirim Rating') }}
                                </x-loading-button>
                            </div>
                        </form>
                    </div>

                    <!-- Complain Form Section -->
                    <div id="complain-form-section" class="tab-content mt-6 hidden">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Gunakan formulir ini jika pekerjaan tidak sesuai harapan.</p>
                        <form method="POST" action="{{ route('admin.report.storeComplaint', $report) }}" class="mt-4">
                            @csrf
                            <div>
                                <x-input-label for="complaint_note" :value="__('Alasan Komplain (Wajib Diisi)')" />
                                <textarea id="complaint_note" name="complaint_note" rows="5" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500" required minlength="10">{{ old('complaint_note') }}</textarea>
                                <x-input-error :messages="$errors->get('complaint_note')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Batal</a>

                                {{-- ✅ Loading Submit Complain (danger style) --}}
                                <x-loading-button
                                    loading-text="Mengirim komplain..."
                                    class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
                                >
                                    {{ __('Kirim Komplain') }}
                                </x-loading-button>
                            </div>
                        </form>
                    </div>
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
            // Image Modal Logic
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

            // Tab Logic
            const tabRate = document.getElementById('tab-rate');
            const tabComplain = document.getElementById('tab-complain');
            const rateFormSection = document.getElementById('rate-form-section');
            const complainFormSection = document.getElementById('complain-form-section');

            const activeClasses = ['text-indigo-600', 'border-indigo-500'];
            const inactiveClasses = ['text-gray-500', 'border-transparent', 'hover:text-gray-700', 'hover:border-gray-300'];

            function setActiveTab(tab) {
                [tabRate, tabComplain].forEach(t => {
                    t.classList.remove(...activeClasses);
                    t.classList.add(...inactiveClasses);
                });
                tab.classList.add(...activeClasses);
                tab.classList.remove(...inactiveClasses);
            }

            tabRate.addEventListener('click', function () {
                rateFormSection.classList.remove('hidden');
                complainFormSection.classList.add('hidden');
                setActiveTab(tabRate);
            });

            tabComplain.addEventListener('click', function () {
                complainFormSection.classList.remove('hidden');
                rateFormSection.classList.add('hidden');
                setActiveTab(tabComplain);
            });
        });
    </script>
</x-app-layout>
