<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Helpdesk - PT. Lemigas</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        @include('layouts.navigation')

        {{-- Page Heading --}}
        @isset($header)
            <header class="bg-white/80 dark:bg-gray-800/70 backdrop-blur supports-[backdrop-filter]:backdrop-blur shadow-sm">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- Flash Messages (Global) --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
            <x-flash-message />
        </div>

        {{-- Page Content --}}
        <main>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    {{-- External scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

    {{-- ✅ Flash close + auto-hide handler (tanpa Alpine) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Close button handler
            document.querySelectorAll('.flash-close').forEach(btn => {
                btn.addEventListener('click', function () {
                    const box = this.closest('.flash-msg');
                    if (box) box.remove();
                });
            });

            // Auto-hide handler (hanya yang punya data-autohide="1")
            document.querySelectorAll('.flash-msg[data-autohide="1"]').forEach(box => {
                const timeoutMs = parseInt(box.getAttribute('data-timeout') || '4500', 10);

                let timer = setTimeout(() => {
                    box.remove();
                }, timeoutMs);

                // Pause on hover
                box.addEventListener('mouseenter', () => {
                    if (timer) clearTimeout(timer);
                });

                // Resume on leave (delay kecil biar user sempat selesai baca)
                box.addEventListener('mouseleave', () => {
                    timer = setTimeout(() => {
                        box.remove();
                    }, 1200);
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ✅ Prevent double submit + show loading state
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function () {
                    // disable semua button submit
                    const submits = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                    submits.forEach(btn => {
                        btn.disabled = true;

                        // kalau pakai komponen loading-button
                        const spinner = btn.querySelector?.('.loading-spinner');
                        const text = btn.querySelector?.('.btn-text');
                        const loadingText = btn.querySelector?.('.btn-loading-text');

                        if (spinner && text && loadingText) {
                            spinner.classList.remove('hidden');
                            text.classList.add('hidden');
                            loadingText.classList.remove('hidden');
                        }
                    });
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
