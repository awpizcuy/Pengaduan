@props([
    'type' => 'submit',
    'loadingText' => 'Memproses...',
])

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold
                    bg-indigo-600 text-white hover:bg-indigo-700
                    focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                    disabled:opacity-60 disabled:cursor-not-allowed transition'
    ]) }}
>
    <svg class="loading-spinner hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
    </svg>

    <span class="btn-text">{{ $slot }}</span>
    <span class="btn-loading-text hidden">{{ $loadingText }}</span>
</button>
