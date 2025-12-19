@php
    $record = $getRecord();
    $imageUrl = $record->photo 
        ? route('view.private.image', ['filename' => basename($record->photo)]) 
        : null;
@endphp

<div>
    @if($imageUrl)
        <img 
            src="{{ $imageUrl }}" 
            style="height: 100px; width: auto; object-fit: cover;" 
            class="rounded-lg shadow-sm border border-gray-200 cursor-pointer hover:opacity-80 transition"
            @click="$dispatch('open-preview-image', { url: '{{ $imageUrl }}' })"
            alt="Bukti Foto"
        >
    @else
        <span class="text-gray-400 italic text-sm">- Tidak ada foto -</span>
    @endif
</div>