@php
    $record = $getRecord();
    $rawPhotos = $record->attachments;
    
    // Normalisasi Data menjadi Array
    $photos = [];
    if (is_array($rawPhotos)) {
        $photos = $rawPhotos;
    } elseif (is_string($rawPhotos) && !empty($rawPhotos)) {
        $photos = [$rawPhotos]; // Support data lama (single string)
    }

    // Jika kosong
    if (empty($photos)) {
        echo '<span class="text-gray-400 italic text-sm">- Tidak ada file -</span>';
        return;
    }
@endphp

<div wire:ignore class="flex flex-wrap gap-4">
    @foreach($photos as $photoPath)
        @php
            $fileUrl = route('view.private.image', ['filename' => basename($photoPath)]);
            $extension = strtolower(pathinfo($photoPath, PATHINFO_EXTENSION));
            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
            $isPdf = $extension === 'pdf';
        @endphp

        <div class="relative group">
            {{-- SKENARIO 1: GAMBAR --}}
            @if($isImage)
                <img 
                    src="{{ $fileUrl }}" 
                    style="height: 100px; width: 100px; object-fit: cover;" 
                    class="rounded-lg shadow-sm border border-gray-200 cursor-pointer hover:opacity-80 transition"
                    @click="$dispatch('open-preview-image', { url: '{{ $fileUrl }}' })"
                    alt="Lampiran"
                >

            {{-- SKENARIO 2: PDF — butuh konfirmasi password --}}
            @elseif($isPdf)
                <div
                    x-data="{
                        showModal: false,
                        password: '',
                        isLoading: false,
                        errorMsg: '',
                        pdfUrl: '{{ $fileUrl }}',
                        openModal() {
                            this.showModal = true;
                            this.password = '';
                            this.errorMsg = '';
                            this.$nextTick(() => this.$refs.pwdInput.focus());
                        },
                        async submitPassword() {
                            if (!this.password) return;
                            this.isLoading = true;
                            this.errorMsg = '';
                            try {
                                const res = await fetch('{{ route('verify.password') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    },
                                    body: JSON.stringify({ password: this.password }),
                                });
                                const data = await res.json();
                                if (data.success) {
                                    this.showModal = false;
                                    window.open(this.pdfUrl, '_blank');
                                } else {
                                    this.errorMsg = data.message || 'Password salah.';
                                }
                            } catch (e) {
                                this.errorMsg = 'Terjadi kesalahan. Coba lagi.';
                            } finally {
                                this.isLoading = false;
                            }
                        }
                    }"
                    class="flex flex-col items-center justify-center p-2 border rounded-lg w-25 h-25 bg-gray-50"
                >
                    <x-heroicon-m-document-text class="w-8 h-8 text-red-500 mb-1"/>
                    <button
                        type="button"
                        @click="openModal()"
                        class="text-xs text-center text-primary-600 hover:underline break-all cursor-pointer"
                    >
                        Open PDF
                    </button>

                    {{-- Modal Password --}}
                    <template x-teleport="body">
                        <div
                            x-show="showModal"
                            x-transition.opacity
                            style="display:none;"
                            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-sm"
                            @keydown.escape.window="showModal = false"
                        >
                            {{-- Overlay klik tutup --}}
                            <div class="absolute inset-0" @click="showModal = false"></div>

                            {{-- Panel Modal --}}
                            <div
                                class="relative z-10 bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-sm mx-4"
                                @click.stop
                            >
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="flex-shrink-0 w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Konfirmasi Password</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Masukkan password Anda untuk membuka PDF</p>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                                    <input
                                        x-ref="pwdInput"
                                        type="password"
                                        x-model="password"
                                        @keydown.enter="submitPassword()"
                                        placeholder="Masukkan password Anda..."
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    >
                                    <p
                                        x-show="errorMsg"
                                        x-text="errorMsg"
                                        class="mt-1.5 text-xs text-red-600 dark:text-red-400"
                                    ></p>
                                </div>

                                <div class="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        @click="showModal = false"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="button"
                                        @click="submitPassword()"
                                        :disabled="isLoading || !password"
                                        class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition flex items-center gap-1.5"
                                    >
                                        <svg x-show="isLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 12 0 12 4z"></path>
                                        </svg>
                                        <span x-text="isLoading ? 'Memverifikasi...' : 'Buka PDF'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

            {{-- SKENARIO 3: LAINNYA --}}
            @else
                <a href="{{ $fileUrl }}" target="_blank" class="flex flex-col items-center justify-center p-2 border rounded-lg w-25 h-25 hover:bg-gray-50 transition">
                    <x-heroicon-m-document class="w-8 h-8 text-gray-500 mb-1"/>
                    <span class="text-[10px] text-gray-500 uppercase">{{ $extension }}</span>
                </a>
            @endif
        </div>
    @endforeach
</div>