# Sistem Informasi Rumah Sakit

Aplikasi web untuk mengelola data spesialisasi, dokter, janji temu pasien, kamar rumah sakit, dan rekap data.

## Teknologi

- Laravel
- React
- Inertia.js
- TypeScript
- SQLite
- Vite

## Persyaratan

- PHP yang kompatibel dengan versi Laravel proyek
- Composer
- Node.js dan npm
- Git

## Instalasi

1. Clone repositori:

   ```bash
   git clone https://github.com/Dhikaaadityaa/sistem-rumah-sakit.git
   cd sistem-rumah-sakit
   ```

2. Instal dependensi PHP:

   ```bash
   composer install
   ```

3. Siapkan file konfigurasi:

   ```bash
   copy .env.example .env
   ```

4. Pastikan konfigurasi database pada `.env` menggunakan SQLite:

   ```dotenv
   DB_CONNECTION=sqlite
   ```

5. Buat file database SQLite jika belum tersedia:

   ```bash
   php -r "if (!file_exists('database/database.sqlite')) { touch('database/database.sqlite'); }"
   ```

6. Buat application key dan jalankan migrasi:

   ```bash
   php artisan key:generate
   php artisan migrate
   ```

7. Instal dependensi JavaScript dan bangun aset:

   ```bash
   npm install
   npm run build
   ```

8. Jalankan aplikasi:

   ```bash
   php artisan serve
   ```

   Buka alamat yang ditampilkan oleh Laravel pada terminal.

## Pengembangan frontend

Untuk menjalankan Vite dalam mode pengembangan, gunakan:

```bash
npm run dev
```

## Catatan

- Jangan mengunggah file `.env`, database lokal, `vendor`, atau `node_modules` ke repositori.
- Sesuaikan konfigurasi lingkungan sebelum menjalankan aplikasi.
