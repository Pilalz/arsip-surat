<div 
    x-data="cameraField()" 
    class="space-y-4"
>
    {{-- ERROR MESSAGE --}}
    <template x-if="errorMessage">
        <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            <p x-text="errorMessage"></p>
        </div>
    </template>

    {{-- MAIN LAYOUT: KIRI KAMERA - KANAN PREVIEW --}}
    {{-- 'grid-cols-1 md:grid-cols-2' artinya: di HP 1 kolom, di Laptop 2 kolom --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        {{-- BAGIAN KIRI: KAMERA + BUTTONS --}}
        <div class="space-y-3">
            <div class="border rounded-lg p-2 bg-gray-900">
                {{-- VIDEO STREAM --}}
                {{-- UBAH 1: aspect-video bikin rasio jadi 16:9 (Landscape) --}}
                <video 
                    x-ref="video" 
                    x-show="cameraOn"
                    autoplay 
                    playsinline 
                    class="w-full aspect-video bg-black rounded-lg shadow-lg object-cover"
                ></video>

                {{-- Placeholder saat kamera off --}}
                <div 
                    x-show="!cameraOn" 
                    class="w-full aspect-video bg-gray-800 rounded-lg flex items-center justify-center"
                >
                    <div class="text-gray-500 flex flex-col items-center">
                        <!-- <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg> -->
                        <span class="text-sm">Kamera Mati</span>
                    </div>
                </div>
            </div>

            {{-- BUTTONS --}}
            <div class="flex gap-2">
                {{-- Toggle Kamera --}}
                <x-filament::button 
                    size="sm"
                    color="gray"
                    @click="toggleCamera()"
                    icon="heroicon-o-video-camera"
                    class="flex-1"
                >
                    <span x-text="cameraOn ? 'Matikan Kamera' : 'Nyalakan Kamera'"></span>
                </x-filament::button>

                {{-- Ambil Foto --}}
                <x-filament::button 
                    size="sm" 
                    color="primary" 
                    @click="takePhoto()" 
                    icon="heroicon-o-camera"
                    x-show="cameraOn"
                    class="flex-1"
                >
                    Jepret
                </x-filament::button>
            </div>
        </div>

        {{-- BAGIAN KANAN: PREVIEW HASIL --}}
        <div class="border rounded-lg p-4 bg-white shadow-sm h-full flex flex-col justify-center">
            
            {{-- Jika SUDAH ada foto --}}
            <template x-if="photo">
                <div class="space-y-3 w-full">
                    <p class="text-sm font-bold text-gray-700 text-center">Hasil Foto:</p>
                    
                    {{-- UBAH 2: Preview juga pakai aspect-video biar sama ukurannya --}}
                    <img 
                        :src="photo" 
                        class="w-full aspect-video rounded-lg shadow-md object-contain border border-gray-200 bg-gray-100" 
                    />
                    
                    <button 
                        @click="photo = ''" 
                        type="button"
                        class="w-full px-3 py-2 text-sm text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg border border-red-200 transition flex justify-center items-center gap-2"
                    >
                        <!-- <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> -->
                        Hapus & Ulangi
                    </button>
                </div>
            </template>

            {{-- Jika BELUM ada foto --}}
            <template x-if="!photo">
                <div class="flex flex-col items-center justify-center h-full min-h-[200px] text-gray-400 border-2 border-dashed border-gray-300 rounded-lg">
                    <!-- <svg class="w-16 h-16 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg> -->
                    <p class="text-sm">Preview akan muncul di sini</p>
                </div>
            </template>
        </div>
    </div>

    {{-- CANVAS (HIDDEN) --}}
    <canvas x-ref="canvas" class="hidden"></canvas>

    {{-- HIDDEN INPUT RESULT --}}
    <input type="hidden" name="{{ $getStatePath() }}" x-model="photo">
</div>

<script>
function cameraField() {
    return {
        stream: null,
        cameraOn: false,
        photo: '',
        errorMessage: '',

        async toggleCamera() {
            if (this.cameraOn) {
                this.stopCamera();
            } else {
                await this.startCamera();
            }
        },

        async startCamera() {
            this.errorMessage = '';
            
            try {
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    this.errorMessage = "Browser tidak support kamera";
                    return;
                }

                this.cameraOn = true;
                await this.$nextTick();

                // UBAH 3: Request resolusi HD (Landscape 16:9)
                // Idealnya 1280x720, ini standar webcam modern
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { 
                        facingMode: 'user', // 'environment' kalau mau kamera belakang
                        width: { ideal: 1280 }, 
                        height: { ideal: 720 }
                    }
                });

                if (this.$refs.video) {
                    this.$refs.video.srcObject = this.stream;
                }

            } catch (err) {
                console.error('Camera error:', err);
                this.cameraOn = false;
                this.errorMessage = "Gagal mengakses kamera (Pastikan Izin/HTTPS aktif)";
            }
        },

        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
            if (this.$refs.video) {
                this.$refs.video.srcObject = null;
            }
            this.cameraOn = false;
        },

        takePhoto() {
            if (!this.cameraOn) return;

            const video = this.$refs.video;
            const canvas = this.$refs.canvas;

            // Canvas menyesuaikan ukuran asli video stream
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            const ctx = canvas.getContext('2d');
            
            // Tips: Kalau mau foto mirroring (karena kamera depan), uncomment baris bawah ini:
            // ctx.translate(canvas.width, 0);
            // ctx.scale(-1, 1);

            ctx.drawImage(video, 0, 0);
            
            // Simpan kualitas tinggi (0.9)
            this.photo = canvas.toDataURL("image/jpeg", 0.9);

            // Matikan kamera otomatis setelah jepret (opsional, bisa dihapus kalau mau tetap nyala)
            this.stopCamera();
        }
    }
}
</script>