<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Kepala IT') }}
        </h2>
    </x-slot>

    <div class="space-y-6">

        {{-- ========================================== --}}
        {{-- BAGIAN 1: KARTU STATISTIK (ANGKA)          --}}
        {{-- ========================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                            <p class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400">{{ $pendingReports }}</p>
                        </div>
                        <div class="p-3 bg-red-100 dark:bg-red-900/20 rounded-lg">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rata-rata Waktu Selesai --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-shadow hover:shadow-md">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rata-rata Waktu Selesai</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $averageCompletionTime ?? 'N/A' }}</p>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- BAGIAN 2: VISUALISASI DATA (CHART)         --}}
        {{-- ========================================== --}}

        {{-- Grid Chart: Status & Kategori --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- CARD 1: Doughnut Status --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                            Distribusi Laporan per Status
                        </h3>
                        <select id="statusLegendDropdown" class="text-xs px-2 py-1 rounded-md border border-gray-600 bg-gray-800 text-white focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <option value="">Semua Status</option>
                            @foreach($statusCounts->keys() as $status)
                                <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-2 h-64">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- CARD 2: Bar Kategori --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-4">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-3">
                        Distribusi Laporan per Kategori
                    </h3>
                    <div class="mt-2 h-64">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart Laporan Selesai (Line Chart + Filter Timeline) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mt-6">
            <div class="p-4">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3 mb-3">
                    <div class="space-y-2">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                            Laporan Selesai
                            @if($lineMode === 'daily' && $selectedMonth)
                                (Per Tanggal – {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->translatedFormat('F Y') }})
                            @else
                                (Rekap Per Bulan @if($range === 'year' && $year) – Tahun {{ $year }} @else – 12 Bulan Terakhir @endif)
                            @endif
                        </h3>

                        {{-- Filter Chips untuk Bulan --}}
                        @if($monthlySummary && $monthlySummary->count())
                            @php
                                $baseQuery = request()->query();
                            @endphp
                            <div class="flex flex-wrap gap-2 text-xs">
                                {{-- Chip "Semua Bulan" --}}
                                @php
                                    $allParams = $baseQuery;
                                    unset($allParams['month']);
                                    $allActive = !$selectedMonth;
                                @endphp
                                <a href="{{ route('kepala.dashboard', $allParams) }}"
                                   class="inline-flex items-center px-2.5 py-1 rounded-full border {{ $allActive ? 'bg-indigo-600 text-white border-indigo-500' : 'bg-indigo-600/10 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200 border-indigo-500/30' }}">
                                    Semua Bulan
                                </a>

                                {{-- Chip per bulan --}}
                                @foreach($monthlySummary as $month => $total)
                                    @php
                                        $params = $baseQuery;
                                        $params['month'] = $month;
                                        $isActive = $selectedMonth === $month;
                                    @endphp
                                    <a href="{{ route('kepala.dashboard', $params) }}"
                                       class="inline-flex items-center px-2.5 py-1 rounded-full border {{ $isActive ? 'bg-indigo-600 text-white border-indigo-500' : 'bg-indigo-600/10 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200 border-indigo-500/30' }}">
                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('M Y') }}:
                                        <span class="ml-1 font-semibold">{{ $total }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Filter Dropdown Range (Tahun/12 Bulan) --}}
                    <div class="md:pt-1">
                        <form method="GET" action="{{ route('kepala.dashboard') }}" class="flex flex-wrap items-center gap-2 text-xs">
                            <input type="hidden" name="filter_status" value="{{ request('filter_status') }}">
                            <input type="hidden" name="filter_category" value="{{ request('filter_category') }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                            <input type="hidden" name="month" value="">

                            <select name="range" onchange="this.form.submit()" class="px-2 py-1 rounded-md bg-gray-800 text-white border border-gray-600 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <option value="12_months" {{ $range === '12_months' ? 'selected' : '' }}>12 Bulan Terakhir</option>
                                <option value="year" {{ $range === 'year' ? 'selected' : '' }}>Per Tahun</option>
                            </select>

                            @if($range === 'year')
                                @php $currentYear = now()->year; @endphp
                                <select name="year" onchange="this.form.submit()" class="px-2 py-1 rounded-md bg-gray-800 text-white border border-gray-600 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                                        <option value="{{ $y }}" {{ (int)($year ?? $currentYear) === $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            @endif
                        </form>
                    </div>
                </div>

                <div class="mt-2 h-64">
                    <canvas id="completedChart"></canvas>
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- BAGIAN 3: TABEL LAPORAN (DATA)             --}}
        {{-- ========================================== --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 md:p-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between mb-6 gap-4">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Semua Laporan Masuk</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola dan monitor semua laporan yang masuk.</p>
                    </div>

                    {{-- Tombol Cetak --}}
                    <div class="flex items-center gap-3">
                        <a href="{{ route('kepala.reports.printAllCompleted') }}"
                        class="inline-flex items-center justify-center h-11 px-5 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 transition">
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M6 7V3h12v4H6zm12 2h1a3 3 0 013 3v6h-4v3H6v-3H2v-6a3 3 0 013-3h1v2h12V9zM8 19h8v-5H8v5z"/>
                            </svg>
                            Cetak Semua Laporan Selesai (PDF)
                        </a>

                        <a href="{{ route('kepala.reports.exportAllCompletedExcel') }}"
                        class="inline-flex items-center justify-center h-11 px-5 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
                            Export Excel
                        </a>

                        {{-- tombol Export Excel (aktifkan kalau route-nya sudah dibuat)
                        <a href="{{ route('kepala.dashboard.export', request()->query()) }}"
                           class="ml-2 inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                           Export Excel
                        </a>
                        --}}
                    </div>
                </div>

                @if (session('success'))
                    <x-flash-message type="success">{{ session('success') }}</x-flash-message>
                @endif
                @if (session('error'))
                    <x-flash-message type="error">{{ session('error') }}</x-flash-message>
                @endif

                {{-- Filter Bar Component (Lebih Bersih) --}}
                <x-filter-bar
                    :action="route('kepala.dashboard')"
                    :date-from="request('date_from')"
                    :date-to="request('date_to')"
                    :filter-status="request('filter_status')"
                    :filter-category="request('filter_category')"
                    :search="request('search')"
                    :statuses="['pending', 'accepted', 'on_process', 'completed', 'rated', 'rejected', 'hold', 'complain']"
                    :categories="['kerusakan', 'peminjaman', 'instalasi']"
                />

                {{-- Tabel --}}
                <div class="overflow-x-auto -mx-6 md:mx-0">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pelapor</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Komplain</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($reports as $report)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $report->nama_pelapor }}</div>
                                        @if($report->reporter)
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $report->reporter->email }}</div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                            {{ ucfirst($report->kategori) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($report->tanggal_pengajuan)->format('d M Y') }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-status-badge :status="$report->status" />
                                    </td>

                                    {{-- NEW: Komplain count --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $report->complaint_count ?? 0 }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('kepala.report.manage', $report) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                            Manage
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    {{-- colspan jadi 6 karena kolom bertambah --}}
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        Tidak ada laporan yang sesuai dengan filter atau pencarian Anda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                @if($reports->hasPages())
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- SCRIPTS (CHART.JS CONFIGURATION)           --}}
    {{-- ========================================== --}}
    @push('scripts')
        <script>
            // ====== DATA DARI CONTROLLER ======
            // Pastikan Controller mengirimkan variabel ini:
            // $statusCounts, $lineLabels, $lineData, $lineMax, $categoryCounts
            const statusLabels   = @json($statusCounts->keys());
            const statusData     = @json($statusCounts->values());

            const lineLabels     = @json($lineLabels);
            const lineData       = @json($lineData);
            const lineMax        = Number(@json($lineMax));

            const categoryLabels = @json($categoryCounts->keys());
            const categoryData   = @json($categoryCounts->values());

            const isDarkMode       = document.documentElement.classList.contains('dark');
            const chartBorderColor = isDarkMode ? '#0f172a' : '#ffffff';

            // ====== MAPPING WARNA STATUS ======
            const statusColorMap = {
                pending:    '#f97316', // orange
                accepted:   '#22c55e', // green
                rejected:   '#ef4444', // red
                hold:       '#eab308', // yellow
                completed:  '#1d4ed8', // blue
                rated:      '#331af3', // sky
                on_process: '#0ea5e9', // cyan
                complain:   '#fb923c', // orange-400
            };

            const statusBackgroundColors = statusLabels.map(
                label => statusColorMap[label] || '#64748b'
            );

            // ====== CHART 1: STATUS (Doughnut) ======
            const statusCtx   = document.getElementById('statusChart').getContext('2d');
            const statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                        backgroundColor: statusBackgroundColors,
                        borderColor: chartBorderColor,
                        borderWidth: 2,
                        hoverOffset: 8,
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 16,
                                font: {size: 12},
                                color: '#9ca3af', // gray-400
                            },
                            onClick: (e, legendItem, legend) => {}, // disable toggle
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    return `${label}: ${value} laporan`;
                                }
                            }
                        }
                    }
                }
            });

            // Filter Dropdown Logic for Chart 1
            const statusDropdown = document.getElementById('statusLegendDropdown');
            if (statusDropdown) {
                statusDropdown.addEventListener('change', function () {
                    const selected = this.value;
                    const meta     = statusChart.getDatasetMeta(0);
                    meta.data.forEach((element, index) => {
                        const label = statusChart.data.labels[index];
                        element.hidden = selected && label !== selected;
                    });
                    statusChart.update();
                });
            }

            // ====== CHART 2: COMPLETED (Line) ======
            const completedCtx = document.getElementById('completedChart').getContext('2d');
            const pointCount    = lineLabels.length;
            const showPoints    = pointCount <= 45;
            const suggestedMax  = lineMax > 0 ? lineMax + Math.ceil(lineMax * 0.1) : 5;

            // Plugin garis vertikal hijau
            const activePointLinePlugin = {
                id: 'activePointLinePlugin',
                afterDatasetsDraw(chart, args, pluginOptions) {
                    const activeElements = chart.getActiveElements();
                    if (!activeElements || activeElements.length === 0) return;
                    const {ctx, chartArea: {top, bottom}} = chart;
                    const activePoint = activeElements[0];
                    const x = activePoint.element.x;
                    ctx.save();
                    ctx.beginPath();
                    ctx.moveTo(x, top);
                    ctx.lineTo(x, bottom);
                    ctx.lineWidth   = 1;
                    ctx.strokeStyle = '#22c55e';
                    ctx.stroke();
                    ctx.restore();
                }
            };

            const completedChart = new Chart(completedCtx, {
                type: 'line',
                data: {
                    labels: lineLabels,
                    datasets: [{
                        data: lineData,
                        fill: true,
                        borderWidth: 2,
                        borderColor: 'rgba(56,189,248,1)',
                        backgroundColor: 'rgba(56,189,248,0.15)',
                        pointRadius: showPoints ? 3 : 0,
                        pointHoverRadius: showPoints ? 4 : 0,
                        pointHitRadius: 8,
                        tension: 0.3,
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    layout: { padding: {top: 10, right: 10, bottom: 10, left: 0} },
                    scales: {
                        x: {
                            ticks: {
                                color: '#9ca3af',
                                autoSkip: true,
                                maxTicksLimit: pointCount <= 31 ? 10 : 8,
                            },
                            grid: {display: false},
                        },
                        y: {
                            beginAtZero: true,
                            suggestedMax: suggestedMax,
                            ticks: { precision: 0, color: '#9ca3af' },
                            grid: {
                                color: isDarkMode ? 'rgba(148,163,184,0.2)' : 'rgba(148,163,184,0.3)'
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return `${context.raw} laporan selesai`;
                                }
                            }
                        },
                        decimation: {
                            enabled: pointCount > 120,
                            algorithm: 'lttb',
                            samples: 120,
                        }
                    }
                },
                plugins: [activePointLinePlugin]
            });

            // ====== CHART 3: KATEGORI (Bar) ======
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            const categoryColorMap = {
                kerusakan:  '#f97316',
                peminjaman: '#22c55e',
                instalasi:  '#38bdf8',
            };
            const categoryBackgroundColors = categoryLabels.map(
                label => categoryColorMap[label] || '#64748b'
            );

            // Plugin label di dalam bar
            const categoryBarLabelPlugin = {
                id: 'categoryBarLabelPlugin',
                afterDatasetsDraw(chart, args, options) {
                    const {ctx} = chart;
                    const dataset = chart.data.datasets[0];
                    const meta    = chart.getDatasetMeta(0);
                    ctx.save();
                    meta.data.forEach((bar, index) => {
                        const categoryName = chart.data.labels[index];
                        const value        = dataset.data[index];
                        if (value === 0 || value == null) return;

                        const props   = bar.getProps(['x', 'y', 'base', 'height'], true);
                        const barLeft = Math.min(props.x, props.base);
                        const textX   = barLeft + 8;
                        const textY   = props.y;

                        ctx.fillStyle    = '#ffffff';
                        ctx.font         = 'bold 12px sans-serif';
                        ctx.textAlign    = 'left';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(categoryName, textX, textY);
                    });
                    ctx.restore();
                }
            };

            new Chart(categoryCtx, {
                type: 'bar',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        label: 'Jumlah laporan per kategori',
                        data: categoryData,
                        backgroundColor: categoryBackgroundColors,
                        borderWidth: 0,
                        borderRadius: 6,
                        maxBarThickness: 50,
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: { display: false },
                        y: {
                            grid: {display: false},
                            ticks: {
                                callback: function (value, index) {
                                    const label = this.getLabelForValue(value);
                                    const idx   = categoryLabels.indexOf(label);
                                    if (idx !== -1) return categoryData[idx];
                                    return label;
                                },
                                color: '#9ca3af',
                                font: {weight: 'bold'},
                            },
                        },
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return `${context.label}: ${context.raw} laporan`;
                                }
                            }
                        }
                    }
                },
                plugins: [categoryBarLabelPlugin]
            });
        </script>
    @endpush
</x-app-layout>
