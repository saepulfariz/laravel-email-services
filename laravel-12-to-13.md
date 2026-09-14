# Panduan Upgrade Laravel 12 ke Laravel 13

Proses upgrade dari Laravel 12 ke Laravel 13 dirancang agar berjalan lancar dengan perubahan yang minimal. Dokumentasi resmi Laravel memperkirakan waktu upgrade ini hanya sekitar 10 menit.

Berikut adalah panduan langkah demi langkah untuk melakukan upgrade:

## 1. Periksa Persyaratan Sistem

Laravel 13 membutuhkan versi PHP minimal PHP 8.3. Pastikan server dan lingkungan lokal Anda sudah menggunakan versi ini.

## 2. Perbarui `composer.json`

Buka file `composer.json` di proyek Anda dan perbarui versi dari dependensi utama Laravel ke versi terbaru:

```json
"require": {
    "laravel/framework": "^13.0",
    "laravel/tinker": "^3.0"
},
"require-dev": {
    "phpunit/phpunit": "^12.0",
    "pestphp/pest": "^4.0", // Jika Anda menggunakan Pest
    "laravel/boost": "^2.0" // Jika Anda menggunakan alat AI Laravel Boost
}
```

> **Catatan:** Pastikan juga untuk memeriksa kompatibilitas paket pihak ketiga (seperti Spatie, Livewire, dll.) dan naikkan versinya jika mereka sudah merilis versi yang mendukung Laravel 13.

## 3. Jalankan Pembaruan Composer

Setelah menyimpan `composer.json`, jalankan perintah berikut di terminal Anda untuk memperbarui seluruh paket:

```bash
composer update -W
```

atau

```bash
composer require laravel/socialite laravel/sanctum darkaonline/l5-swagger spatie/laravel-permission maatwebsite/excel
```

```bash
composer require --dev fakerphp/faker laravel/pail laravel/pint laravel/sail mockery/mockery nunomaduro/collision
```

_(Bendera `-W` atau `--with-all-dependencies` memastikan Composer ikut memperbarui semua paket bawaan Symfony yang dibutuhkan oleh Laravel 13)._

## 4. Perhatikan Perubahan Penting (Breaking Changes)

Meskipun pembaruan ini tergolong minor, ada beberapa hal teknis yang mungkin perlu Anda sesuaikan secara manual berdasarkan Upgrade Guide resmi:

- **Perubahan Prefix Cache & Session**: Laravel 13 mengubah format pembuatan nama otomatis untuk cache, database, dan session dari yang sebelumnya menggunakan garis bawah (`_`) menjadi tanda hubung (`-`). Jika Anda mengandalkan format bawaan ini, nama cookie sesi Anda akan berubah sehingga pengguna mungkin akan logout. Anda sangat disarankan untuk menetapkan `CACHE_PREFIX`, `REDIS_PREFIX`, dan `SESSION_COOKIE` secara manual di file `.env` produksi sebelum deploy.
- **PreventRequestForgery**: Middleware perlindungan CSRF (sebelumnya `VerifyCsrfToken` atau `ValidateCsrfToken`) kini telah diformalkan menjadi `PreventRequestForgery`. Jika Anda mengecualikan rute tertentu di pengujian atau rute menggunakan kelas lama, perbarui rujukannya.
- **Instansiasi Model di boot()**: Di Laravel 13, Anda tidak lagi diizinkan untuk menginisiasi instansiasi model baru dari dalam metode `boot()` atau trait `boot*`.
- **Cache Objek PHP (Serializable Classes)**: Jika aplikasi Anda menyimpan objek (class) PHP secara langsung ke dalam cache, Anda kini perlu mendaftarkannya secara eksplisit di array `serializable_classes` di konfigurasi cache Anda.

## 5. Alternatif Otomatis (Opsional)

Jika aplikasi Anda sangat besar, Anda bisa menggunakan layanan berbayar seperti [Laravel Shift](https://laravelshift.com) atau alat AI resmi seperti Laravel Boost (`/upgrade-laravel-v13`) untuk mengotomatiskan perubahan sintaks dan gaya kode.

## 6. Sumber Referensi Resmi

- **Panduan Upgrade Resmi (Upgrade Guide)**: [laravel.com/framework/docs/upgrade](https://laravel.com/docs/13.x/upgrade)
- **Catatan Rilis Laravel 13 (Release Notes)**: [laravel.com/framework/docs/releases](https://laravel.com/docs/13.x/releases)

---

# Implementasi pada Proyek `laravel-email-services`

Berikut adalah rincian langkah-langkah yang telah dilakukan secara spesifik pada proyek ini untuk bermigrasi ke Laravel 13:

### 1. Pengecekan PHP

- Menggunakan perintah `php -v`, dipastikan sistem telah menjalankan **PHP 8.3.29**, sehingga telah memenuhi syarat minimum Laravel 13.

### 2. Penyesuaian `composer.json` & Pihak Ketiga

- Memperbarui versi paket inti: `php` ke `^8.3`, `laravel/framework` ke `^13.0`, `laravel/tinker` ke `^3.0`, dan `phpunit/phpunit` ke `^12.0`.
- Karena ada beberapa paket yang belum mendukung atau menyebabkan konflik versi spesifik (`darkaonline/l5-swagger` versi `10.1`, `nunomaduro/collision`, dll), dilakukan penyesuaian dependensi pihak ketiga dan _dev_ menjadi `*`. Hal ini memungkinkan Composer mencari versi rilis terbaru yang mendukung Laravel 13 secara otomatis.
- Berhasil menjalankan `composer update -W` dan mengunduh seluruh versi terbaru dari paket-paket di dalam proyek (seperti `l5-swagger` ke versi `11.1.0`, `sanctum`, `socialite`, `excel`, dan `spatie/laravel-permission`).

### 3. Penyesuaian Breaking Changes Laravel 13

- **Prefix (.env.example)**: Ditambahkan variabel eksplisit `CACHE_PREFIX=laravel_cache`, `REDIS_PREFIX=laravel_database_`, dan `SESSION_COOKIE=laravel_session` pada file `.env.example`. (Perlu juga ditambahkan di `.env` lokal atau server produksi).
- **Sanctum CSRF (config/sanctum.php)**: Mengubah rujukan `Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class` menjadi `Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class` sesuai pembaharuan dari Laravel 13.
- **Model boot() (EmailLog.php)**: Dilakukan pengecekan pada file `app/Models/EmailLog.php` dan dipastikan kode `boot()` sudah aman karena hanya meregistrasikan _event listener_ (`static::creating`), bukan menginisiasi objek model baru.
