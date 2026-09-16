<x-filament-panels::page>
    <div class="space-y-6 max-w-2xl">

        {{-- ── SECTION 1: Informasi Profil ───────────────────────────────── --}}
        <x-filament::section>
            <x-slot name="heading">Informasi Profil</x-slot>
            <x-slot name="description">Perbarui nama dan alamat email akun Anda.</x-slot>

            <form wire:submit.prevent="saveProfile" class="space-y-4">

                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        wire:model="name"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Nama lengkap Anda"
                    >
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        wire:model="email"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="email@contoh.com"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end pt-2">
                    <x-filament::button type="submit" icon="heroicon-m-check">
                        Simpan Profil
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        {{-- ── SECTION 2: Ganti Password ──────────────────────────────────── --}}
        <x-filament::section>
            <x-slot name="heading">Ganti Password</x-slot>
            <x-slot name="description">Kosongkan jika tidak ingin mengubah password. Minimal 8 karakter.</x-slot>

            <form wire:submit.prevent="savePassword" class="space-y-4">

                {{-- Password Saat Ini --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Password Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="password"
                        wire:model="current_password"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Masukkan password saat ini"
                    >
                    @error('current_password')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Baru --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Password Baru <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="password"
                        wire:model="new_password"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Minimal 8 karakter"
                    >
                    @error('new_password')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi Password Baru --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Konfirmasi Password Baru <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="password"
                        wire:model="new_password_confirmation"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Ulangi password baru"
                    >
                    @error('new_password_confirmation')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end pt-2">
                    <x-filament::button type="submit" color="warning" icon="heroicon-m-key">
                        Perbarui Password
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

    </div>
</x-filament-panels::page>
