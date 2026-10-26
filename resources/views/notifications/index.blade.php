<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Semua Notifikasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800/50 dark:backdrop-blur-sm overflow-hidden shadow-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50">
                {{-- Header --}}
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-gray-900 dark:text-gray-100">Notifications</h3>
                    @if(Auth::user()->unreadNotifications->isNotEmpty())
                        <form method="POST" action="{{ route('notifications.markAllRead') }}">
                            @csrf
                            <button type="submit" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline focus:outline-none">
                                Mark all as read
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Daftar Notifikasi --}}
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($notifications as $notification)
                        <a href="{{ $notification->data['url'] ?? route('notifications.read', $notification->id) }}" class="flex items-start gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors duration-150">
                            {{-- Ganti placeholder lingkaran warna dengan icon yang relevan (misal: icon lonceng, icon peringatan, atau icon checklist) agar lebih informatif. --}}
                            <span class="rounded-full h-10 w-10 flex-shrink-0 flex items-center justify-center {{ $notification->read_at ? 'bg-gray-100 dark:bg-gray-700' : 'bg-indigo-100 dark:bg-indigo-900/50' }}">
                                <svg class="h-6 w-6 {{ $notification->read_at ? 'text-gray-500 dark:text-gray-400' : 'text-indigo-600 dark:text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"></path></svg>
                            </span>

                            <div class="flex-grow">
                                <p class="font-semibold text-gray-800 dark:text-gray-100">
                                    {{ $notification->data['message'] ?? 'Notification' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>

                            @if(!$notification->read_at)
                                <span class="h-3 w-3 rounded-full bg-indigo-500 flex-shrink-0 mt-1" title="Unread"></span>
                            @endif
                        </a>
                    @empty
                        <p class="text-center text-gray-500 dark:text-gray-400 p-12">You have no notifications.</p>
                    @endforelse
                </div>

                {{-- Footer (Pagination) --}}
                @if($notifications->hasPages())
                    <div class="p-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
