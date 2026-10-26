@props([
    'action',
    'dateFrom',
    'dateTo',
    'filterStatus',
    'filterCategory',
    'search',
    'statuses' => [],
    'categories' => [],
])

<form method="GET" action="{{ $action }}" class="mb-6">
    <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
        {{-- Left: Filter Criteria --}}
        <div class="flex flex-wrap items-center gap-3">
            {{-- Date Range --}}
            <div class="flex items-center gap-2">
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">s.d.</span>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
            </div>

            {{-- Status Filter --}}
            @if(!empty($statuses))
            <select name="filter_status" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Filter Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ $filterStatus == $status ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>
            @endif

            {{-- Category Filter --}}
            @if(!empty($categories))
            <select name="filter_category" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Filter Kategori</option>
                @foreach($categories as $kategori)
                    <option value="{{ $kategori }}" {{ $filterCategory == $kategori ? 'selected' : '' }}>
                        {{ ucfirst($kategori) }}
                    </option>
                @endforeach
            </select>
            @endif
        </div>

        {{-- Right: Actions (Search, Apply, Reset) --}}
        <div class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" placeholder="Cari..." value="{{ $search }}" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
            <div class="flex items-center gap-2">
                <x-primary-button type="submit">Terapkan</x-primary-button>
                <a href="{{ $action }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                    Reset
                </a>
            </div>
        </div>
    </div>
</form>
