@php
    // Logika pemilihan kelas dipindahkan ke sini
    $classes = '';
    switch ($status) {
        case 'pending':
            $classes = '#f97316 bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-200';
            break;
        case 'accepted':
            $classes = '#22c55e bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200';
            break;
        case 'on_process':
            $classes = '#0ea5e9 bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200';
            break;
        case 'hold':
            $classes = '#eab308 bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200';
            break;
        case 'rejected':
            $classes = '#ef4444 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200';
            break;
        case 'completed':
            $classes = '#1d4ed8 bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200';
            break;
        case 'rated':
            $classes = '#331af3 bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-200';
            break;
        case 'complain':
            $classes = '#fb923c bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-200';
            break;
        default:
            $classes = 'bg-gray-100 text-gray-800 dark:bg-gray-900/40 dark:text-gray-200';
    }
@endphp

<span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $classes }}">
    {{ $text }}
</span>
