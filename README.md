# Sistem Rumah Sakit

Sistem Rumah Sakit merupakan aplikasi berbasis web untuk membantu proses pengajuan janji temu dokter dan pengecekan ketersediaan ruangan rumah sakit.

## Fitur Utama

* Pengelolaan data pengguna
* Pengelolaan data dokter
* Pengelolaan spesialisasi dokter
* Pengajuan janji temu dokter
* Pengelolaan status janji temu
* Pengecekan ketersediaan ruangan rumah sakit
* Pembagian akses berdasarkan *role* pengguna

## Teknologi yang Digunakan

* PHP 8.1
* Laravel 10
* PostgreSQL 16
* Composer
* Git & GitHub
* Laragon

## Persyaratan

Pastikan perangkat sudah memiliki:

* PHP 8.1 atau versi yang kompatibel dengan project
* Composer
* PostgreSQL
* Git
* Laragon atau web server lainnya

## 1. Clone Repository

Clone repository dari GitHub:

```bash
git clone https://github.com/Dhikaaadityaa/sistem-rumah-sakit.git
```

Masuk ke folder project:

```bash
cd sistem-rumah-sakit
```

## 2. Install Dependency

Jalankan:

```bash
composer install
```

Perintah tersebut akan menginstall seluruh *dependency* Laravel yang dibutuhkan oleh project.

## 3. Membuat File Environment

Salin file `.env.example` menjadi `.env`.

Pada Windows:

```bash
copy .env.example .env
```

Kemudian buka file `.env`.

## 4. Membuat Database PostgreSQL

Buat database PostgreSQL dengan nama:

```text
rumah_sakit
```

Contoh menggunakan PostgreSQL:

```sql
CREATE DATABASE rumah_sakit;
```

Sesuaikan username dan password PostgreSQL dengan konfigurasi PostgreSQL pada komputer masing-masing.

## 5. Konfigurasi Database

Buka file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=rumah_sakit
DB_USERNAME=postgres
DB_PASSWORD=YOUR_POSTGRES_PASSWORD
```

Ganti:

```text
YOUR_POSTGRES_PASSWORD
```

dengan password PostgreSQL masing-masing.

> Jangan memasukkan password PostgreSQL asli ke dalam repository GitHub.

## 6. Generate Application Key

Jalankan:

```bash
php artisan key:generate
```

## 7. Menjalankan Migration dan Seeder

Untuk membuat seluruh tabel sekaligus mengisi data awal:

```bash
php artisan migrate --seed
```

Migration akan membuat tabel:

* `users`
* `specialties`
* `doctors`
* `appointments`
* `rooms`

Seeder akan memasukkan data awal untuk:

* User
* Spesialisasi
* Dokter
* Ruangan
* Janji temu

Jika ingin menghapus seluruh tabel dan membuat ulang database beserta data awal:

```bash
php artisan migrate:fresh --seed
```

> Perintah `migrate:fresh --seed` akan menghapus seluruh tabel dan data yang ada di database.

## 8. Menjalankan Aplikasi

Jalankan:

```bash
php artisan serve
```

Kemudian buka alamat:

```text
http://127.0.0.1:8000
```

## 9. Akun untuk Pengujian

### Admin

Email:

```text
admin@rumahsakit.test
```

Password:

```text
password
```

### Pasien

Email:

```text
pasien@rumahsakit.test
```

Password:

```text
password
```

Akun tersebut merupakan data *dummy* yang dibuat melalui `UserSeeder`.

## 10. Struktur Database

Relasi utama sistem:

```text
specialties
     │
     │ 1
     │
     └──────< doctors
                 │
                 │ 1
                 │
                 └──────< appointments >────── users
```

Sedangkan tabel `rooms` digunakan untuk menyimpan informasi ketersediaan ruangan rumah sakit.

### Tabel Users

Menyimpan data pengguna sistem, termasuk *role* `admin` dan `patient`.

### Tabel Specialties

Menyimpan data spesialisasi dokter.

### Tabel Doctors

Menyimpan data dokter dan terhubung dengan tabel `specialties`.

### Tabel Appointments

Menyimpan data janji temu pasien dengan dokter.

### Tabel Rooms

Menyimpan informasi ruangan rumah sakit beserta statusnya:

* `available`
* `occupied`
* `maintenance`

## 11. Seeder

Seeder utama berada pada:

```text
database/seeders/DatabaseSeeder.php
```

Seeder yang digunakan:

```text
UserSeeder
SpecialtySeeder
DoctorSeeder
RoomSeeder
AppointmentSeeder
```

Urutan *seeding* disesuaikan dengan hubungan antar tabel agar data yang memiliki *foreign key* dapat dibuat dengan benar.

## 12. GitHub Collaboration

Setiap anggota tim harus melakukan *clone* repository terlebih dahulu:

```bash
git clone https://github.com/Dhikaaadityaa/sistem-rumah-sakit.git
```

Setelah melakukan perubahan:

```bash
git status
git add .
git commit -m "Deskripsi perubahan"
git push
```

Sebelum mulai mengerjakan perubahan baru, disarankan mengambil perubahan terbaru dari repository:

```bash
git pull origin main
```

## Catatan

File `.env` berisi konfigurasi lokal dan informasi sensitif sehingga tidak boleh di-*commit* ke GitHub.

Setiap anggota tim harus membuat file `.env` masing-masing berdasarkan `.env.example`.

Project ini menggunakan database PostgreSQL sehingga setiap anggota tim perlu memiliki PostgreSQL dan membuat database `rumah_sakit` pada komputer masing-masing.
