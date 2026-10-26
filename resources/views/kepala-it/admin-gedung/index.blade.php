<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kelola Data Admin Gedung') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 md:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Daftar Admin Gedung</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola akun admin gedung yang dibuat oleh Kepala IT</p>
                    </div>

                    <a href="{{ route('kepala.users.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Akun
                    </a>
                </div>

                @if(session('created_user_password'))
                    <div class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                        <p class="font-medium text-yellow-900 dark:text-yellow-300">Password Baru</p>

                        <div class="mt-2 text-sm text-yellow-800 dark:text-yellow-200 flex flex-wrap items-center gap-3">
                            <div>
                                Akun untuk <strong>{{ session('created_user_email') }}</strong> dibuat. Password sementara:
                            </div>

                            <code id="temp-password" class="px-2 py-1 bg-white dark:bg-gray-800 rounded">
                                {{ session('created_user_password') }}
                            </code>

                            <button id="copy-temp-password" type="button"
                                class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                <span class="inline-flex items-center" data-copy-icon>
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 16h8M8 12h8M8 8h8M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                <span data-copy-label>Salin</span>
                            </button>
                        </div>

                        <p class="mt-1 text-xs text-gray-500">
                            Pastikan untuk mencatat password ini karena hanya ditampilkan sekali.
                        </p>
                    </div>
                @endif

                <div class="mb-6">
                    <form method="GET" action="{{ route('kepala.users.index') }}" data-loading-form>
                        <div class="flex gap-3">
                            <x-text-input type="text" name="search" placeholder="Cari nama atau email..."
                                          class="flex-1 rounded-lg" :value="$searchTerm ?? ''" />

                            <button type="submit"
                                    data-loading-btn
                                    data-loading-text="Mencari..."
                                    class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition disabled:opacity-60 disabled:cursor-not-allowed">
                                <span data-btn-label>Cari</span>
                                <svg data-btn-spinner class="hidden ml-2 h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto -mx-6 md:mx-0">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">No Telp</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->no_telp ?? '-' }}</div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('kepala.users.edit', $user) }}"
                                               class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                                Edit
                                            </a>

                                            <form action="{{ route('kepala.users.destroy', $user) }}"
                                                  method="POST"
                                                  class="inline-block"
                                                  data-loading-form
                                                  onsubmit="return confirm('Hapus akun ini?');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        data-loading-btn
                                                        data-loading-text="Menghapus..."
                                                        class="inline-flex items-center px-3 py-1.5 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition disabled:opacity-60 disabled:cursor-not-allowed">
                                                    <span data-btn-label>Hapus</span>
                                                    <svg data-btn-spinner class="hidden ml-2 h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada data Admin Gedung.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const btn = document.getElementById('copy-temp-password');
                const code = document.getElementById('temp-password');
                if (!btn || !code) return;

                function setCopiedState() {
                    const label = btn.querySelector('[data-copy-label]');
                    if (label) label.textContent = 'Tersalin';
                    btn.classList.add('opacity-90');

                    setTimeout(function () {
                        if (label) label.textContent = 'Salin';
                        btn.classList.remove('opacity-90');
                    }, 1500);
                }

                function fallbackCopy(text) {
                    const textarea = document.createElement('textarea');
                    textarea.value = text;
                    document.body.appendChild(textarea);
                    textarea.select();
                    try { document.execCommand('copy'); setCopiedState(); } catch (e) {}
                    textarea.remove();
                }

                btn.addEventListener('click', function () {
                    const text = (code.textContent || '').trim();
                    if (!text) return;

                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(text).then(setCopiedState, function () {
                            fallbackCopy(text);
                        });
                    } else {
                        fallbackCopy(text);
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
