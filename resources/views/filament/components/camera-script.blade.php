<script>
// Cek dulu biar ga double load
if (typeof window.cameraField === 'undefined') {
    
    window.cameraField = function(config) {
        return {
            stream: null,
            cameraOn: false,
            photo: config.previewUrl || '', 
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

                    this.stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } }
                    });

                    if (this.$refs.video) {
                        this.$refs.video.srcObject = this.stream;
                    }
                } catch (err) {
                    console.error('Camera error:', err);
                    this.cameraOn = false;
                    this.errorMessage = "Gagal akses kamera: " + err.message;
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

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0);
                
                const dataUrl = canvas.toDataURL("image/jpeg", 0.8);
                this.photo = dataUrl;
                this.$wire.set(config.statePath, dataUrl);
                this.stopCamera();
            },

            removePhoto() {
                this.photo = '';
                this.$wire.set(config.statePath, null);
            }
        }
    }
}
</script>