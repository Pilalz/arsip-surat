<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

Route::get('/private-image/{filename}', function ($filename) {
    
    // Tentukan path relatif di dalam disk
    $path = 'surat-photos/' . $filename;

    // 1. Debugging: Cek dulu apakah Storage mengenalinya
    if (!Storage::disk('local')->exists($path)) {
        abort(404, 'File tidak ditemukan di Storage Local.');
    }

    // 2. CARA BARU (LEBIH AMAN): 
    // Jangan pakai response()->file(), tapi pakai Storage::response()
    // Ini otomatis mengurus header, mime-type, dan path yang benar.
    return Storage::disk('local')->response($path);

})->name('view.private.image');