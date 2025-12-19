<div 
    x-data="cameraField({
        statePath: '{{ $getStatePath() }}',
        initialPhoto: @js($getState()),
        previewUrl: @js($getState() ? route('view.private.image', ['filename' => basename($getState())]) : null)
    })"
    class="space-y-4"
>
    {{-- ERROR MESSAGE --}}
    <template x-if="errorMessage">
        <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            <p x-text="errorMessage"></p>
        </div>
    </template>

    {{-- WADAH UTAMA (Single View) --}}
    <div class="relative w-full border rounded-xl overflow-hidden bg-gray-900 shadow-lg">
        
        {{-- 1. TAMPILAN KAMERA (Video) --}}
        {{-- Muncul jika: Kamera Nyala DAN Belum ada foto --}}
        <video 
            x-ref="video" 
            x-show="cameraOn && !photo"
            autoplay 
            playsinline 
            class="w-full aspect-video object-cover"
        ></video>

        {{-- 2. TAMPILAN HASIL FOTO (Image) --}}
        {{-- Muncul jika: Ada foto (baik baru jepret atau dari database) --}}
        <template x-if="photo">
            <div class="relative w-full aspect-video bg-black">
                <img 
                    :src="photo" 
                    class="w-full h-full object-contain" 
                />
                {{-- Overlay Label "Hasil Foto" --}}
                <div class="absolute top-2 left-2 bg-black/50 text-white px-2 py-1 rounded text-xs">
                    Preview
                </div>
            </div>
        </template>

        {{-- 3. TAMPILAN STANDBY (Placeholder) --}}
        {{-- Muncul jika: Kamera Mati DAN Belum ada foto --}}
        <div 
            x-show="!cameraOn && !photo" 
            class="w-full aspect-video flex flex-col items-center justify-center text-gray-500 bg-gray-800"
        >
            <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <!-- <span class="text-sm">Kamera Siap</span> -->
        </div>

        {{-- 4. CONTROLS BAR (Tombol-tombol di bawah layar) --}}
        <div class="bg-gray-100 p-3 border-t flex justify-center gap-3">
            
            {{-- TOMBOL: NYALAKAN KAMERA (Muncul jika kamera mati & belum ada foto) --}}
            <button 
                type="button"
                x-show="!cameraOn && !photo"
                @click="toggleCamera()"
                class="flex items-center gap-2 px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                Open Camera
            </button>

            {{-- TOMBOL: JEPRET (Muncul jika kamera nyala & belum ada foto) --}}
            <button 
                type="button"
                x-show="cameraOn && !photo"
                @click="takePhoto()"
                class="flex items-center gap-2 px-6 py-2 bg-primary-600 text-white font-bold rounded-full hover:bg-primary-500 transition shadow-lg"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                Take a picture
            </button>

            {{-- TOMBOL: BATALKAN KAMERA (Muncul jika kamera nyala tapi mau cancel) --}}
            <button 
                type="button"
                x-show="cameraOn && !photo"
                @click="stopCamera()"
                class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition"
            >
                Close
            </button>

            {{-- TOMBOL: HAPUS / FOTO ULANG (Muncul jika SUDAH ada foto) --}}
            <button 
                type="button"
                x-show="photo"
                @click="removePhoto(); toggleCamera()" 
                class="flex items-center gap-2 px-4 py-2 bg-white border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Retake
            </button>
        </div>

    </div>

    <canvas x-ref="canvas" class="hidden"></canvas>
</div>