<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if(Auth::user()->role == 'admin_gedung')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.report.history')" :active="request()->routeIs('admin.report.history')">
                            {{ __('Riwayat Laporan') }}
                        </x-nav-link>

                    @elseif(Auth::user()->role == 'kepala_it')
                        <x-nav-link :href="route('kepala.dashboard')" :active="request()->routeIs('kepala.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('kepala.technicians.index')" :active="request()->routeIs('kepala.technicians.*')">
                            {{ __('Kelola Teknisi') }}
                        </x-nav-link>
                        <x-nav-link :href="route('kepala.users.index')" :active="request()->routeIs('kepala.users.*')">
                            {{ __('Kelola Admin Gedung') }}
                        </x-nav-link>

                    @elseif(Auth::user()->role == 'teknisi')
                        <x-nav-link :href="route('teknisi.dashboard')" :active="request()->routeIs('teknisi.dashboard')">
                            {{ __('Dashboard Tugas') }}
                        </x-nav-link>
                        <x-nav-link :href="route('teknisi.task.history')" :active="request()->routeIs('teknisi.task.history')">
                            {{ __('Riwayat Tugas') }}
                        </x-nav-link>

                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Notifications Dropdown -->
                @php
                    // Fetch notifications data
                    $user = Auth::user();
                    $notifications = $user->notifications()->latest()->take(15)->get();
                    $unreadCount = $notifications->where('read_at', null)->count();
                @endphp

                <x-dropdown align="right" width="w-80 sm:w-96" contentClasses="p-0">
                    <x-slot name="trigger">
                        <button class="relative inline-flex items-center p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                            @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 h-5 w-5 min-w-[20px] rounded-full bg-red-500 text-white text-xs font-medium flex items-center justify-center">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl border dark:border-gray-700">
                            {{-- Header --}}
                            <div class="p-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                <h3 class="font-bold text-base text-gray-900 dark:text-gray-100">Notifications</h3>
                                @if($unreadCount > 0)
                                    <form method="POST" action="{{ route('notifications.markAllRead') }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline focus:outline-none">
                                            Mark all as read
                                        </button>
                                    </form>
                                @endif
                            </div>

                            {{-- Notification List --}}
                            <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto scrollbar-hide">
                                @forelse ($notifications as $notification)
                                    <a href="{{ route('notifications.read', $notification->id) }}" class="flex items-start gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors duration-150">
                                        <span class="rounded-full h-8 w-8 flex-shrink-0 flex items-center justify-center {{ $notification->read_at ? 'bg-gray-100 dark:bg-gray-700' : 'bg-indigo-100 dark:bg-indigo-900/50' }}">
                                            {{-- Ganti placeholder lingkaran warna dengan icon yang relevan (misal: icon lonceng, icon peringatan, atau icon checklist) agar lebih informatif. --}}
                                            <svg class="h-5 w-5 {{ $notification->read_at ? 'text-gray-500' : 'text-indigo-600 dark:text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"></path></svg>
                                        </span>
                                        <div class="flex-grow">
                                            <p class="text-sm text-gray-800 dark:text-gray-100 {{ !$notification->read_at ? 'font-semibold' : '' }}">
                                                {{ $notification->data['message'] ?? 'Notification' }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        @if(!$notification->read_at)
                                            <span class="h-2.5 w-2.5 rounded-full bg-indigo-500 flex-shrink-0 mt-1" title="Unread"></span>
                                        @endif
                                    </a>
                                @empty
                                    <p class="text-center text-sm text-gray-500 dark:text-gray-400 p-8">You have no new notifications.</p>
                                @endforelse
                            </div>

                            {{-- Footer --}}
                            <div class="p-2 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 text-center">
                                <a href="{{ route('notifications.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                    View All Notifications
                                </a>
                            </div>
                        </div>
                    </x-slot>
                </x-dropdown>

                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(Auth::user()->role == 'admin_gedung')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.report.history')" :active="request()->routeIs('admin.report.history')">
                    {{ __('Riwayat Laporan') }}
                </x-responsive-nav-link>

            @elseif(Auth::user()->role == 'kepala_it')
                <x-responsive-nav-link :href="route('kepala.dashboard')" :active="request()->routeIs('kepala.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('kepala.technicians.index')" :active="request()->routeIs('kepala.technicians.*')">
                    {{ __('Kelola Teknisi') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('kepala.users.index')" :active="request()->routeIs('kepala.users.*')">
                    {{ __('Kelola User') }}
                </x-responsive-nav-link>

            @elseif(Auth::user()->role == 'teknisi')
                <x-responsive-nav-link :href="route('teknisi.dashboard')" :active="request()->routeIs('teknisi.dashboard')">
                    {{ __('Dashboard Tugas') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('teknisi.task.history')" :active="request()->routeIs('teknisi.task.history')">
                    {{ __('Riwayat Tugas') }}
                </x-responsive-nav-link>

            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
