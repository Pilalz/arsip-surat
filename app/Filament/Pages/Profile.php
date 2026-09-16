<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\EditProfile;

class Profile extends EditProfile
{
    // Filament v4 sudah menyediakan EditProfile dengan built-in:
    // - Field: name, email, password, password confirmation, current password
    // - Validasi current password sebelum ganti password
    // - Notifikasi sukses setelah simpan
    // - Auto-hash password baru
    // Tidak perlu override apapun untuk fungsionalitas dasar.

    // Optional: ubah judul halaman
    public static function getLabel(): string
    {
        return 'My Profile';
    }
}
