# Sistem Pengambilan Keputusan (SPK) berbasis Web dengan Laravel 11 dan AdminLTE

Sistem Pengambilan Keputusan (SPK) berbasis web yang mengimplementasikan metode MOORA (Multi-Objective Optimization on the Basis of Ratio Analysis) untuk membantu proses pengambilan keputusan multi-kriteria. Dibangun dengan Laravel 11 dan AdminLTE 3.

## Fitur Utama

-   [Metode MOORA ](https://sif.uin-suska.ac.id/wp-content/uploads/2024/02/Chapter-14-MOORA.pdf) - Implementasi lengkap metode MOORA untuk pengambilan keputusan
-   Manajemen Kriteria dan Subkriteria - Kelola kriteria beserta bobot dan subkriterianya
-   Manajemen Alternatif - Kelola data alternatif-alternatif yang akan dinilai
-   Perhitungan Otomatis - Sistem melakukan perhitungan ranking secara otomatis
-   Tampilan Responsif - Dibangun dengan AdminLTE yang responsif
-   Autentikasi Pengguna - Sistem login dengan level akses yang berbeda

## Persyaratan Sistem

-   [PHP 8.2 atau lebih baru / XAMPP](https://www.apachefriends.org/download.html)
-   [Composer](https://getcomposer.org/)
-   [NodeJS](https://nodejs.org/en)
-   Database (MySQL, PostgreSQL, SQLite, atau SQL Server)

## Instalasi dan Konfigurasi Sistem

Clone Repository

```bash
    git clone https://github.com/AlamRidha/spk-guru.git
    cd spk-guru
```

Install Dependencies

```bash
    composer install
    npm install
```

Konfigurasi Environment

```bash
    cp .env.example .env
```

Buka file .env dan sesuaikan pengaturan database:

```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=spk
    DB_USERNAME=root
    DB_PASSWORD=
```

Generate Key Aplikasi

```bash
    php artisan key:generate
```

Jalankan Migrasi dan Seeder

```bash
    php artisan migrate --seed
```

Login User

```bash
    Admin:
        email: admin@gmail.com
        password: admin123
    Kepsek:
        email: kepsek@gmail.com
        password: kepsek123
    Wakil Kurikulum:
        email: wakur@sekolah.com
        password: wakur123
    Guru:
        email: dodo@gmail.com
        password: dodo123
```

Compile Assets atau untuk development

```bash
    npm run build
    npm run dev
```

Jalankan Aplikasi

```bash
    php artisan serve
```

Aplikasi akan berjalan di http://localhost:8000/login

## Authors

[AlamRidha](https://www.github.com/alamridha)
