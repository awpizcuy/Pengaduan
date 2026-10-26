<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Selesaikan Pekerjaan: ') }} #{{ $report->id }} - {{ ucfirst($report->kategori) }}
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
                         @if($report->status == 'complain' && $report->complaint_note)
                            <div>
                                <p class="text-sm font-medium text-red-500 dark:text-red-400">Alasan Komplain</p>
                                <p class="mt-1 text-sm text-red-700 dark:text-red-300 whitespace-pre-wrap">{{ $report->complaint_note }}</p>
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Foto Kondisi Awal</p>
                        @if($report->foto_awal)
                            <img src="{{ asset('storage/' . $report->foto_awal) }}" alt="Foto Kerusakan Awal" class="mt-2 rounded-lg max-h-64 border dark:border-gray-700 open-image-modal cursor-pointer" />
                        @else
                            <div class="mt-2 h-40 rounded-lg border border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center text-sm text-gray-500">Tidak ada foto kerusakan awal</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 2: Formulir Penyelesaian -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Formulir Laporan Penyelesaian</h3>
                </div>
                <div class="p-6">
                    @if ($errors->any())
                        <div class="mb-4">
                            <x-flash-message type="error">Mohon periksa kembali data yang Anda isi pada formulir.</x-flash-message>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('teknisi.task.storeCompletion', $report) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div>
                            <x-input-label for="barang" :value="__('Barang (yang diganti/dipinjam/diinstal)')" />
                            <x-text-input id="barang" class="mt-1 block w-full" type="text" name="barang" :value="old('barang')" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kosongkan jika tidak ada.</p>
                            <x-input-error :messages="$errors->get('barang')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="qty" :value="__('Jumlah (Qty)')" />
                            <x-text-input id="qty" class="mt-1 block w-full" type="number" name="qty" :value="old('qty', 1)" min="0" />
                            <x-input-error :messages="$errors->get('qty')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="deskripsi_pekerjaan" :value="__('Deskripsi Pekerjaan yang Dilakukan')" />
                            <textarea id="deskripsi_pekerjaan" name="deskripsi_pekerjaan" rows="5" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('deskripsi_pekerjaan') }}</textarea>
                            <x-input-error :messages="$errors->get('deskripsi_pekerjaan')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="foto_before" :value="__('Foto Sebelum Dikerjakan')" />
                                <input id="foto_before" name="foto_before" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/20 dark:file:text-indigo-300 file:cursor-pointer border border-gray-300 dark:border-gray-600 rounded-lg" required>
                                <x-input-error :messages="$errors->get('foto_before')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="foto_after" :value="__('Foto Setelah Dikerjakan')" />
                                <input id="foto_after" name="foto_after" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/20 dark:file:text-indigo-300 file:cursor-pointer border border-gray-300 dark:border-gray-600 rounded-lg" required>
                                <x-input-error :messages="$errors->get('foto_after')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('teknisi.dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Batal</a>
                            <x-primary-button>
                                {{ __('Selesai & Kirim Laporan') }}
                            </x-primary-button>
                        </div>
                    </form>
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
