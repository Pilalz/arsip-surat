<div 
    x-data="{ 
        open: false, 
        imageUrl: '' 
    }"
    @open-preview-image.window="open = true; imageUrl = $event.detail.url"
    @keydown.escape.window="open = false"
    class="relative z-9999"
>
    <template x-teleport="body">
        <div 
            x-show="open" 
            style="display: none;" 
            class="fixed inset-0 z-9999 flex items-center justify-center bg-transparent/90 backdrop-blur-sm p-4"
        >
            <div @click="open = false" class="absolute inset-0 w-full h-full cursor-pointer"></div>

            <div class="relative z-10 w-full h-full flex items-center justify-center pointer-events-none">
                <img 
                    :src="imageUrl" 
                    class="max-w-full max-h-full rounded-md shadow-2xl object-contain pointer-events-auto"
                >
                
                <button 
                    @click="open = false"
                    type="button" 
                    class="absolute top-4 right-4 text-white hover:text-gray-300 bg-black/50 rounded-full p-2 pointer-events-auto"
                >
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
    </template>
</div>