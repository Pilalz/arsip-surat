<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Sistem Manajemen Arsip Surat

Sistem informasi berbasis web untuk mendigitalisasi proses pengelolaan administrasi surat menyurat, meliputi pengarsipan surat masuk, surat keluar, serta sistem disposisi. 

Aplikasi ini dirancang untuk memudahkan pencarian kembali dokumen arsip fisik, pelacakan status surat, dan pembuatan laporan rekapitulasi secara efisien dan terpusat.

## 🚀 Fitur Utama

- **Manajemen Surat Masuk & Keluar:** Pencatatan detail surat (nomor surat, pengirim, tujuan, tanggal, kategori, dan lampiran file).
- **Sistem Disposisi:** Pelacakan instruksi pimpinan dan alur tindak lanjut surat antar departemen.
- **Digitalisasi Dokumen:** Pengunggahan dan penyimpanan file arsip (PDF/Image) dengan sistem penamaan yang terstruktur.
- **Pencarian & Filter:** Pencarian arsip secara cepat berdasarkan nomor surat, rentang waktu, pengirim, maupun kata kunci.
- **Dashboard Statistik:** Visualisasi jumlah surat masuk, keluar, dan disposisi aktif dalam periode tertentu.
- **Manajemen Pengguna:** Hak akses berbasis *Role-Based Access Control* (RBAC) untuk Admin, Pimpinan, dan Staf.

## 🛠️ Teknologi yang Digunakan

- **Backend / Framework:** Laravel
- **Interface / Admin Panel:** Filament PHP / Tailwind CSS
- **Database:** PostgreSQL (Kompatibel dengan SQL Server)
- **File Storage:** Local Disk (Storage Link)

## ⚙️ Prasyarat Sistem

Sebelum menginstal, pastikan lingkungan server/komputer Anda telah memiliki:

- PHP >= 8.1
- Composer
- Node.js & NPM
- PostgreSQL atau DBeaver untuk manajemen database

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
