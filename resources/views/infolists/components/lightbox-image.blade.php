@php
    $record = $getRecord();
    
    // Pastikan ada file
    if (!$record->photo) {
        echo '<span class="text-gray-400 italic text-sm">- Tidak ada file -</span>';
        return;
    }

    // 1. Generate URL
    // Jika file hasil scan/upload public, pakai asset storage.
    // Jika file hasil kamera (local), pakai route view private.
    // Sesuaikan logika ini dengan sistem penyimpanan kamu. 
    // Asumsi: Kita pakai route view private agar aman.
    $fileUrl = route('view.private.image', ['filename' => basename($record->photo)]);

    // 2. Cek Ekstensi File
    $extension = strtolower(pathinfo($record->photo, PATHINFO_EXTENSION));
    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
    $isPdf = $extension === 'pdf';
@endphp

<div wire:ignore>
    {{-- SKENARIO 1: GAMBAR (Fitur Lightbox Kemarin) --}}
    @if($isImage)
        <div x-data="{ open: false }" class="flex items-center">
            <img 
                src="{{ $fileUrl }}" 
                style="height: 100px; width: auto; object-fit: cover;" 
                class="rounded-lg shadow-sm border border-gray-200 cursor-pointer hover:opacity-80 transition"
                @click="$dispatch('open-preview-image', { url: '{{ $fileUrl }}' })"
                alt="Lampiran Gambar"
            >
        </div>

    {{-- SKENARIO 2: PDF (Embed Viewer) --}}
    @elseif($isPdf)
        <div class="w-full">
            {{-- Tombol Buka di Tab Baru (Opsional, buat jaga-jaga) --}}
            <a href="{{ $fileUrl }}" target="_blank" class="mb-2 inline-flex items-center gap-1 text-sm text-primary-600 hover:text-primary-500 hover:underline">
                <x-heroicon-m-arrow-top-right-on-square class="w-4 h-4"/>
                Buka PDF Fullscreen
            </a>

            {{-- PDF Viewer (Iframe) --}}
            <iframe 
                src="{{ $fileUrl }}" 
                class="w-full rounded-lg border border-gray-200 shadow-sm"
                style="height: 500px;" 
                frameborder="0"
            >
                Browser Anda tidak mendukung preview PDF. 
                <a href="{{ $fileUrl }}">Download PDF</a>
            </iframe>
        </div>

    {{-- SKENARIO 3: FILE LAIN (DOCX/ZIP, dll) --}}
    @else
        <a href="{{ $fileUrl }}" target="_blank" class="flex items-center gap-2 p-3 border rounded-lg hover:bg-gray-50 transition">
            <x-heroicon-m-document class="w-6 h-6 text-gray-500"/>
            <div class="flex flex-col">
                <span class="text-sm font-bold text-gray-700">File Dokumen</span>
                <span class="text-xs text-gray-500 uppercase">{{ $extension }}</span>
            </div>
        </a>
    @endif
</div>