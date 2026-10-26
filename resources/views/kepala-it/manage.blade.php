<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manage Laporan: ') }} #{{ $report->id }} - {{ ucfirst($report->kategori) }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Card 1: Detail Laporan -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detail Laporan Awal</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pelapor</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $report->nama_pelapor }} ({{ $report->reporter->email }})
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nomor Telepon</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $report->no_telp ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pengajuan</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ \Carbon\Carbon::parse($report->tanggal_pengajuan)->isoFormat('dddd, D MMMM YYYY, HH:mm') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi Masalah</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $report->deskripsi_pengajuan }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Foto Kondisi Awal</p>
                        @if($report->foto_awal)
                            <img src="{{ asset('storage/' . $report->foto_awal) }}"
                                 alt="Foto Kerusakan Awal"
                                 class="mt-2 rounded-lg max-h-64 border dark:border-gray-700 open-image-modal cursor-pointer" />
                        @else
                            <div class="mt-2 h-40 rounded-lg border border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center text-sm text-gray-500">
                                Tidak ada foto kondisi awal
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 2: Status & Tindakan -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Status & Tindakan</h3>
                </div>
                <div class="p-6">
                    <div class="mb-6">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Saat Ini</p>
                        <div class="mt-1"><x-status-badge :status="$report->status" /></div>
                    </div>

                    {{-- ✅ TAMBAHAN (BARU): setelah update dari controller, tampilkan tombol manual kalau WA diblok --}}
                    @if(session('wa_url'))
                        <div class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-sm text-green-800 dark:text-green-200">
                            WhatsApp siap dikirim.
                            <a href="{{ session('wa_url') }}" target="_blank" rel="noopener" class="underline font-semibold">
                                Klik di sini jika tab WhatsApp tidak terbuka
                            </a>.
                        </div>
                    @endif

                    {{-- Komplain ringkas --}}
                    @if($report->complaint_note)
                        <div class="p-4 mb-6 bg-red-50 dark:bg-red-900/30 rounded-lg border border-red-200 dark:border-red-800">
                            <p class="text-sm font-bold text-red-900 dark:text-red-300">Alasan Komplain dari Pelapor:</p>
                            <p class="mt-1 text-sm text-red-700 dark:text-red-400 whitespace-pre-wrap">{{ $report->complaint_note }}</p>
                            <p class="mt-2 text-sm text-red-700 dark:text-red-400">
                                Komplain ke-{{ $report->complaint_count ?? 0 }}
                                @if(!empty($activeComplaint?->complaint_no))
                                    • Nomor: <span class="font-semibold">{{ $activeComplaint->complaint_no }}</span>
                                @endif
                            </p>
                        </div>
                    @endif

                    {{-- ============================= --}}
                    {{-- MODE: REPORT SEDANG KOMPLAIN --}}
                    {{-- ============================= --}}
                    @if($report->status === 'complain')
                        @if($activeComplaint)
                            <div class="p-4 mb-6 bg-amber-50 dark:bg-amber-900/30 rounded-lg border border-amber-200 dark:border-amber-800">
                                <p class="text-sm font-bold text-amber-900 dark:text-amber-200">
                                    Keputusan Komplain ({{ $activeComplaint->complaint_no }})
                                </p>
                                <p class="mt-1 text-sm text-amber-800 dark:text-amber-300">
                                    Pilih tindakan: <strong>Setujui</strong> berarti laporan dibuka ulang untuk dikerjakan teknisi.
                                    <strong>Tolak</strong> berarti laporan dianggap selesai (kembali <code>completed</code>).
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Form Setujui Komplain --}}
                                <form method="POST"
                                      action="{{ route('kepala.report.complaints.approve', [$report, $activeComplaint]) }}"
                                      class="p-4 rounded-lg border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20">
                                    @csrf
                                    <div class="space-y-3">
                                        <p class="text-sm font-semibold text-green-900 dark:text-green-200">
                                            Setujui Komplain
                                        </p>
                                        <div>
                                            <x-input-label for="decision_note_approve" :value="__('Catatan Keputusan (opsional)')" />
                                            <textarea id="decision_note_approve" name="decision_note" rows="3"
                                                      class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('decision_note') }}</textarea>
                                        </div>
                                        <div class="flex justify-end">
                                            <x-primary-button class="bg-green-600 hover:bg-green-700 focus:ring-green-500">
                                                {{ __('Setujui Komplain') }}
                                            </x-primary-button>
                                        </div>
                                    </div>
                                </form>

                                {{-- Form Tolak Komplain --}}
                                <form method="POST"
                                      action="{{ route('kepala.report.complaints.reject', [$report, $activeComplaint]) }}"
                                      class="p-4 rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20"
                                      onsubmit="return confirm('Yakin menolak komplain? Status laporan akan kembali menjadi selesai (completed).');">
                                    @csrf
                                    <div class="space-y-3">
                                        <p class="text-sm font-semibold text-red-900 dark:text-red-200">
                                            Tolak Komplain
                                        </p>
                                        <div>
                                            <x-input-label for="decision_note_reject" :value="__('Catatan Keputusan (opsional)')" />
                                            <textarea id="decision_note_reject" name="decision_note" rows="3"
                                                      class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('decision_note') }}</textarea>
                                        </div>
                                        <div class="flex justify-end">
                                            <x-primary-button class="bg-red-600 hover:bg-red-700 focus:ring-red-500">
                                                {{ __('Tolak Komplain') }}
                                            </x-primary-button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        @else
                            <div class="p-4 mb-6 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                <p class="text-sm font-semibold text-yellow-900 dark:text-yellow-200">
                                    Status report = complain, tapi tidak ditemukan komplain aktif (submitted).
                                </p>
                                <p class="mt-1 text-sm text-yellow-800 dark:text-yellow-300">
                                    Cek data tabel <code>complaints</code> atau pastikan controller mengirim <code>$activeComplaint</code>.
                                </p>
                            </div>
                        @endif

                    {{-- ===================================== --}}
                    {{-- MODE: REPORT NORMAL (pending/hold) --}}
                    {{-- ===================================== --}}
                    @else
                        @php
                            $canManage = in_array($report->status, ['pending', 'hold']);
                        @endphp

                        @if($canManage)
                            <form method="POST" action="{{ route('kepala.report.update', $report) }}" id="formUpdateReport">
                                @csrf

                                <input type="hidden" name="whatsapp_message" id="whatsapp_message" value="">
                                <input type="hidden" name="send_whatsapp" id="send_whatsapp_hidden" value="0">

                                <div class="space-y-6">
                                    <div>
                                        <x-input-label for="status" :value="__('Ubah Status Laporan')" />
                                        <select id="status" name="status"
                                                class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                required>
                                            <option value="accepted" {{ $report->status == 'accepted' ? 'selected' : '' }}>Accept (Tugaskan)</option>
                                            <option value="hold" {{ $report->status == 'hold' ? 'selected' : '' }}>Hold</option>
                                            <option value="rejected" {{ $report->status == 'rejected' ? 'selected' : '' }}>Reject</option>
                                        </select>
                                    </div>

                                    <div>
                                        <x-input-label for="assigned_technician_id" :value="__('Tugaskan ke Teknisi')" />
                                        <select id="assigned_technician_id" name="assigned_technician_id"
                                                class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Pilih Teknisi</option>
                                            @foreach ($technicians as $teknisi)
                                                <option value="{{ $teknisi->id }}"
                                                        data-phone="{{ $teknisi->no_telp ?? '' }}"
                                                        data-name="{{ $teknisi->name }}"
                                                        {{ $report->assigned_technician_id == $teknisi->id ? 'selected' : '' }}>
                                                    {{ $teknisi->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            Kosongkan jika ingin laporan ini tersedia untuk diklaim oleh teknisi mana pun.
                                        </p>

                                        <div class="mt-4 space-y-3">
                                            <div class="flex flex-col md:flex-row md:items-center gap-3">
                                                <a id="btnWhatsapp"
                                                   href="#"
                                                   target="_blank"
                                                   rel="noopener"
                                                   class="inline-flex items-center justify-center px-4 py-2 rounded-lg font-medium text-sm
                                                          bg-green-600 text-white hover:bg-green-700 transition disabled:opacity-50"
                                                   style="pointer-events:none; opacity:.5;">
                                                    Preview WhatsApp
                                                </a>

                                                <p id="waHelp" class="text-sm text-gray-500 dark:text-gray-400">
                                                    Pilih teknisi terlebih dahulu untuk mengaktifkan tombol WhatsApp.
                                                </p>
                                            </div>

                                            <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30">
                                                <input id="send_whatsapp_checkbox"
                                                       type="checkbox"
                                                       class="mt-1 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-green-600 shadow-sm focus:ring-green-500">
                                                <div class="text-sm">
                                                    <p class="font-medium text-gray-900 dark:text-gray-100">
                                                        Kirim WhatsApp otomatis setelah Update Laporan
                                                    </p>
                                                    <p class="text-gray-600 dark:text-gray-400">
                                                        Jika dicentang, setelah klik <strong>Update Laporan</strong> sistem akan membuka WhatsApp di tab baru.
                                                    </p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <div>
                                        <x-input-label for="status_note" :value="__('Catatan (Alasan Reject/Hold)')" />
                                        <textarea id="status_note" name="status_note" rows="4"
                                                  class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('status_note', $report->status_note) }}</textarea>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">*wajib diisi apabila reject/hold</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-3 mt-8">
                                    <a href="{{ route('kepala.dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Batal</a>
                                    <x-primary-button>{{ __('Update Laporan') }}</x-primary-button>
                                </div>
                            </form>
                        @else
                            <div class="space-y-4">
                                @if ($report->assigned_technician_id)
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Teknisi yang Ditugaskan</p>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $report->technician->name ?? 'N/A' }}</p>
                                    </div>
                                @endif
                                @if ($report->status_note)
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Catatan dari Anda</p>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $report->status_note }}</p>
                                    </div>
                                @endif
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Laporan ini sedang dalam proses atau sudah selesai dan tidak dapat diubah lagi dari halaman ini.
                                </p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Card 3: Hasil Penyelesaian -->
            @if($report->resolution)
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Hasil Penyelesaian Teknisi</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Waktu Mulai Pengerjaan</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $report->start_time ? \Carbon\Carbon::parse($report->start_time)->isoFormat('dddd, D MMMM YYYY, HH:mm') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Waktu Selesai Pengerjaan</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $report->end_time ? \Carbon\Carbon::parse($report->end_time)->isoFormat('dddd, D MMMM YYYY, HH:mm') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Durasi Pengerjaan</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $report->duration_minutes ? $report->duration_minutes . ' Menit' : '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ringkasan Pekerjaan</p>
                        <div class="mt-2 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border dark:border-gray-700 text-sm text-gray-900 dark:text-gray-100">
                            @if ($report->resolution->barang)
                                <p><strong>Barang:</strong> {{ $report->resolution->barang }} (Jumlah: {{ $report->resolution->qty ?? 1 }})</p>
                            @endif
                            <p class="mt-2 whitespace-pre-wrap">{{ $report->resolution->deskripsi_pekerjaan ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Foto Sebelum</p>
                            @if($report->resolution->foto_before)
                                <img src="{{ asset('storage/' . $report->resolution->foto_before) }}" alt="Foto Sebelum"
                                     class="mt-2 rounded-lg border dark:border-gray-700 max-h-96 open-image-modal cursor-pointer" />
                            @else
                                <div class="mt-2 h-48 rounded-lg border border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center text-sm text-gray-500">
                                    Tidak ada foto
                                </div>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Foto Sesudah</p>
                            @if($report->resolution->foto_after)
                                <img src="{{ asset('storage/' . $report->resolution->foto_after) }}" alt="Foto Sesudah"
                                     class="mt-2 rounded-lg border dark:border-gray-700 max-h-96 open-image-modal cursor-pointer" />
                            @else
                                <div class="mt-2 h-48 rounded-lg border border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center text-sm text-gray-500">
                                    Tidak ada foto
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($report->resolution->rating)
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rating dari Pelapor</p>
                            <div class="mt-2 flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $report->resolution->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                                         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">({{ $report->resolution->rating }} dari 5)</span>
                            </div>
                            @if($report->resolution->rating_feedback)
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300 italic">"{{ $report->resolution->rating_feedback }}"</p>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- ✅ Tombol dipindah MASUK card (jadi ikut border) --}}
                <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('kepala.dashboard') }}"
                       class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                        &larr; Kembali ke Dashboard
                    </a>
                </div>
            </div>
            @else
            {{-- ✅ Kalau resolution tidak ada, tetap tampilkan tombol dalam card --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Hasil Penyelesaian Teknisi</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data penyelesaian dari teknisi.</p>
                </div>
                <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('kepala.dashboard') }}"
                       class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                        &larr; Kembali ke Dashboard
                    </a>
                </div>
            </div>
            @endif

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

            const techSelect = document.getElementById('assigned_technician_id');
            const btnWa = document.getElementById('btnWhatsapp');
            const waHelp = document.getElementById('waHelp');

            const cbSendWa = document.getElementById('send_whatsapp_checkbox');
            const hiddenSendWa = document.getElementById('send_whatsapp_hidden');
            const hiddenMsg = document.getElementById('whatsapp_message');

            const statusSelect = document.getElementById('status');

            function sanitizePhone(phone) {
                if (!phone) return '';
                phone = phone.toString().replace(/[^\d+]/g, '');
                if (phone.startsWith('08')) phone = '62' + phone.substring(1);
                if (phone.startsWith('+')) phone = phone.substring(1);
                return phone;
            }

            function buildMessage(techName, phoneRaw) {
                const reportId = `{{ $report->id }}`;
                const kategori = `{{ ucfirst($report->kategori) }}`;
                const pelapor = `{{ $report->nama_pelapor }}`;
                const noPelapor = `{{ $report->no_telp ?? '-' }}`;
                const tanggal = `{{ \Carbon\Carbon::parse($report->tanggal_pengajuan)->isoFormat('D MMMM YYYY, HH:mm') }}`;

                const deskripsi = `{{ preg_replace("/\r|\n/", " ", $report->deskripsi_pengajuan) }}`.trim();

                return (
`🛠️ Penugasan Tugas (Helpdesk)
Laporan #${reportId} (${kategori})
Pelapor: ${pelapor}
No. Pelapor: ${noPelapor}
Tanggal: ${tanggal}

Masalah: ${deskripsi}

Catatan:
- Sistem masih lokal, detail tugas dilihat via Dashboard Teknisi setelah login.
- Teknisi: ${techName}${phoneRaw ? " ("+phoneRaw+")" : ""}`
                );
            }

            function updateWhatsAppUI() {
                if (!techSelect || !btnWa) return;

                const opt = techSelect.options[techSelect.selectedIndex];
                const phoneRaw = opt ? (opt.getAttribute('data-phone') || '') : '';
                const techName = opt ? (opt.getAttribute('data-name') || '') : '';
                const phone = sanitizePhone(phoneRaw);

                const msg = buildMessage(techName, phoneRaw);
                if (hiddenMsg) hiddenMsg.value = msg;

                const isAccepted = statusSelect ? (statusSelect.value === 'accepted') : true;

                if (!phone || !isAccepted || !opt || !opt.value) {
                    btnWa.href = '#';
                    btnWa.style.pointerEvents = 'none';
                    btnWa.style.opacity = '.5';

                    if (!opt || !opt.value) {
                        waHelp.textContent = 'Pilih teknisi terlebih dahulu untuk mengaktifkan tombol WhatsApp.';
                    } else if (!phone) {
                        waHelp.textContent = 'Nomor WhatsApp teknisi belum diisi. Lengkapi no_telp teknisi agar bisa kirim.';
                    } else if (!isAccepted) {
                        waHelp.textContent = 'WhatsApp hanya relevan saat status = Accept (Tugaskan).';
                    } else {
                        waHelp.textContent = 'Tidak bisa membuat link WhatsApp.';
                    }

                    if (cbSendWa && !isAccepted) {
                        cbSendWa.checked = false;
                        if (hiddenSendWa) hiddenSendWa.value = '0';
                    }
                    return;
                }

                const waUrl = `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;

                btnWa.href = waUrl;
                btnWa.style.pointerEvents = 'auto';
                btnWa.style.opacity = '1';
                waHelp.textContent = `Siap kirim ke: ${techName} (${phoneRaw || phone})`;
            }

            if (cbSendWa && hiddenSendWa) {
                cbSendWa.addEventListener('change', function() {
                    hiddenSendWa.value = cbSendWa.checked ? '1' : '0';
                });
            }

            if (techSelect) techSelect.addEventListener('change', updateWhatsAppUI);
            if (statusSelect) statusSelect.addEventListener('change', updateWhatsAppUI);

            updateWhatsAppUI();
        });
    </script>

    @if(session('wa_url') && session('auto_open_wa'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const waUrl = @json(session('wa_url'));
                window.open(waUrl, '_blank', 'noopener,noreferrer');
            });
        </script>
    @endif
</x-app-layout>
