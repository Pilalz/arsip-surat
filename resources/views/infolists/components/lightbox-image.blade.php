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
            // Generate URL per file
            // Pastikan route 'view.private.image' mengembalikan response()->file($path);
            $fileUrl = route('view.private.image', ['filename' => basename($photoPath)]);

            $extension = strtolower(pathinfo($photoPath, PATHINFO_EXTENSION));
            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
            $isPdf = $extension === 'pdf';
        @endphp

        <div class="relative group">
            {{-- SKENARIO 1: GAMBAR --}}
            @if($isImage)
                <div x-data="{ open: false }">
                    <img 
                        src="{{ $fileUrl }}" 
                        style="height: 100px; width: 100px; object-fit: cover;" 
                        class="rounded-lg shadow-sm border border-gray-200 cursor-pointer hover:opacity-80 transition"
                        @click="$dispatch('open-preview-image', { url: '{{ $fileUrl }}' })"
                        alt="Lampiran"
                    >
                </div>

            {{-- SKENARIO 2: PDF --}}
            @elseif($isPdf)
                <div class="flex flex-col items-center justify-center p-2 border rounded-lg w-[100px] h-[100px] bg-gray-50">
                    <x-heroicon-m-document-text class="w-8 h-8 text-red-500 mb-1"/>
                    <a href="{{ $fileUrl }}" target="_blank" class="text-xs text-center text-primary-600 hover:underline break-all">
                        Open PDF
                    </a>
                </div>

            {{-- SKENARIO 3: LAINNYA --}}
            @else
                <a href="{{ $fileUrl }}" target="_blank" class="flex flex-col items-center justify-center p-2 border rounded-lg w-[100px] h-[100px] hover:bg-gray-50 transition">
                    <x-heroicon-m-document class="w-8 h-8 text-gray-500 mb-1"/>
                    <span class="text-[10px] text-gray-500 uppercase">{{ $extension }}</span>
                </a>
            @endif
        </div>
    @endforeach
</div>