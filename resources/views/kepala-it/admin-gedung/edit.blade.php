<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Akun Admin Gedung') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <form method="POST" action="{{ route('kepala.users.update', $user) }}" class="js-loading-form">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama')" />
                        <x-text-input
                            id="name"
                            name="name"
                            type="text"
                            class="mt-2 block w-full"
                            value="{{ old('name', $user->name) }}"
                            required
                        />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input
                            id="email"
                            name="email"
                            type="email"
                            class="mt-2 block w-full"
                            value="{{ old('email', $user->email) }}"
                            required
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="no_telp" :value="__('No Telpon')" />
                        <x-text-input
                            id="no_telp"
                            name="no_telp"
                            type="text"
                            class="mt-2 block w-full"
                            value="{{ old('no_telp', $user->no_telp) }}"
                        />
                        <x-input-error :messages="$errors->get('no_telp')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-6">
                        <a href="{{ route('kepala.users.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                            Batal
                        </a>

                        <button
                            type="submit"
                            data-loading-text="Menyimpan..."
                            class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            {{ __('Simpan Perubahan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
