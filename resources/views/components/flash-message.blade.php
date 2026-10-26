@props([])

<div class="space-y-4">
    {{-- Success (AUTO HIDE) --}}
    @if (session('success'))
        <div
            class="flash-msg rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800 dark:border-green-900/40 dark:bg-green-900/20 dark:text-green-200 flex items-start justify-between gap-4"
            data-autohide="1"
            data-timeout="4500"
        >
            <div class="text-sm leading-relaxed break-words">
                <span class="font-semibold">Berhasil:</span> {{ session('success') }}
            </div>
            <button
                type="button"
                aria-label="Tutup pesan"
                class="flash-close shrink-0 text-green-700/70 hover:text-green-900 dark:text-green-200/70 dark:hover:text-green-100"
            >
                ✕
            </button>
        </div>
    @endif

    {{-- Error (TIDAK AUTO HIDE) --}}
    @if (session('error'))
        <div class="flash-msg rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-200 flex items-start justify-between gap-4">
            <div class="text-sm leading-relaxed break-words">
                <span class="font-semibold">Gagal:</span> {{ session('error') }}
            </div>
            <button
                type="button"
                aria-label="Tutup pesan"
                class="flash-close shrink-0 text-red-700/70 hover:text-red-900 dark:text-red-200/70 dark:hover:text-red-100"
            >
                ✕
            </button>
        </div>
    @endif

    {{-- Warning (AUTO HIDE) --}}
    @if (session('warning'))
        <div
            class="flash-msg rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-900 dark:border-amber-900/40 dark:bg-amber-900/20 dark:text-amber-100 flex items-start justify-between gap-4"
            data-autohide="1"
            data-timeout="6000"
        >
            <div class="text-sm leading-relaxed break-words">
                <span class="font-semibold">Peringatan:</span> {{ session('warning') }}
            </div>
            <button
                type="button"
                aria-label="Tutup pesan"
                class="flash-close shrink-0 text-amber-800/70 hover:text-amber-950 dark:text-amber-100/70 dark:hover:text-amber-50"
            >
                ✕
            </button>
        </div>
    @endif

    {{-- Info (AUTO HIDE) --}}
    @if (session('info'))
        <div
            class="flash-msg rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-blue-900 dark:border-blue-900/40 dark:bg-blue-900/20 dark:text-blue-100 flex items-start justify-between gap-4"
            data-autohide="1"
            data-timeout="5000"
        >
            <div class="text-sm leading-relaxed break-words">
                <span class="font-semibold">Info:</span> {{ session('info') }}
            </div>
            <button
                type="button"
                aria-label="Tutup pesan"
                class="flash-close shrink-0 text-blue-800/70 hover:text-blue-950 dark:text-blue-100/70 dark:hover:text-blue-50"
            >
                ✕
            </button>
        </div>
    @endif

    {{-- Validation Errors (TIDAK AUTO HIDE) --}}
    @if ($errors->any())
        <div class="flash-msg rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-200">
            <div class="flex items-start justify-between gap-4">
                <div class="text-sm leading-relaxed break-words">
                    <div class="font-semibold mb-1">Ada input yang belum valid:</div>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button
                    type="button"
                    aria-label="Tutup pesan"
                    class="flash-close shrink-0 text-red-700/70 hover:text-red-900 dark:text-red-200/70 dark:hover:text-red-100"
                >
                    ✕
                </button>
            </div>
        </div>
    @endif
</div>
