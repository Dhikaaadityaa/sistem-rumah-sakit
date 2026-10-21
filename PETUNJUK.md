# Sistem Rumah Sakit: lapisan aplikasi (backend) untuk starter kit Laravel + React

Berkas di sini adalah **lapisan aplikasi** (migrasi, model, Policy, middleware, controller,
Form Request, seeder, tes). Ia dipasang di atas proyek baru dari **repositori starter kelas**
(Laravel + React + Inertia, sudah ada login/daftar/keluar dari Fortify).

> Status: seluruh berkas PHP lolos `php -l` (sintaks). Aplikasi **belum dijalankan** karena lingkungan
> penyusun tidak dapat mengunduh paket Composer. Jalankan `php artisan test` setelah dipasang.
> Halaman React (pages/*.jsx) belum ada: itu langkah berikutnya.

## Cara memasang (urut)

1. Salin repositori starter kelas, lalu: `composer install`, `npm install`, salin `.env.example` ke `.env`,
   atur `DB_CONNECTION=pgsql` dan database, lalu `php artisan key:generate`.
2. Salin isi folder ini ke akar proyek (`app/`, `database/`, `routes/`, `tests/`). Jangan menimpa berkas starter kit lain.
3. Gabungkan manual empat potongan di folder `snippets/` (jangan menimpa seluruh berkas):
   `routes/web.php`, `bootstrap/app.php`, `AppServiceProvider`, `HandleInertiaRequests`, serta `User.php`.
4. `php artisan migrate:fresh --seed` lalu `php artisan test`.
5. Akun contoh (kata sandi `password`): `admin@rumahsakit.test` (admin), `pasien@rumahsakit.test`, `budi@rumahsakit.test`, `ani@rumahsakit.test` (pasien).

Hapus dari proyek lama: Laravel 10, Sanctum, `Kernel.php`, dan tabel `personal_access_tokens`.

## Pemenuhan batas lingkup proyek

| Syarat | Wujud |
|---|---|
| 3 modul data berelasi + CRUD + cari | Spesialisasi -> Dokter -> Janji Temu (+ Ruangan, terhubung lewat `appointments.room_id`) |
| 2 peran dengan kewenangan berbeda di server | `admin` dan `patient` (kolom `users.role`) |
| 2 alur tak sepele | (1) pengajuan janji dengan cek jadwal bentrok + alur status pending -> confirmed -> completed; (2) pencarian bertingkat (nama, status, spesialisasi, rentang tanggal) |
| 1 halaman rekap | `/recap`: ringkasan janji per status, per bulan, per spesialisasi, ruangan, dan jadwal hari ini |

## Empat hal penting pada auth (yang dinilai)

**1. Hash (satu arah) dan enkripsi (dua arah)**
- Kata sandi: `Hash::make()` (bcrypt). Satu arah: tidak dapat dikembalikan, hanya dapat dicocokkan ulang (`Hash::check`). Seeder memakainya.
- Keluhan pasien: cast `"notes" => "encrypted"` pada `Appointment`. Dua arah: terenkripsi di basis data, terbuka lewat model dengan `APP_KEY`.
  Konsekuensi: kolom ini tidak bisa dicari dengan `ILIKE`. Jangan hilangkan/ganti `APP_KEY` setelah ada data.
- Aturan pakai: sesuatu yang HANYA perlu dicocokkan (kata sandi) di-hash; sesuatu yang perlu DIBACA lagi (keluhan) dienkripsi.

**2. Blokir percobaan berulang (anti brute force)**
- Starter kit memakai Fortify dengan pembatas `RateLimiter::for("login", ...)`. **Periksa** `app/Providers/FortifyServiceProvider.php`; bentuk bakunya:
  ```php
  RateLimiter::for("login", function (Request $request) {
      $kunci = Str::transliterate(Str::lower($request->input(Fortify::username()))."|".$request->ip());
      return Limit::perMinute(5)->by($kunci);
  });
  ```
  Jika sudah ada, biarkan. Buktikan di demo: salah kata sandi 6 kali -> pesan "terlalu banyak percobaan" (HTTP 429).

**3. Sesi: siapa yang sedang masuk**
- Setelah login, server menyimpan data sesi di sisinya dan mengirim SATU kuki berisi tanda sesi. Peramban hanya mengirim tanda itu kembali pada setiap permintaan; server yang tahu siapa pemiliknya.
- Di server: `$request->user()` / `Auth::user()`. Di React: `usePage().props.auth.user`. Peran tidak pernah dipercaya dari klien.

**4. Middleware, Gate, Policy, dan menu sesuai peran (semuanya di Laravel, bukan React)**
- Middleware `auth`/`verified`: sudah login belum. Middleware `role:admin` (`EnsureRole`): peran cocok atau 403, controller tidak dijalankan.
  Dipasang per kelompok rute di `routes/hospital.php`, didaftarkan di `bootstrap/app.php`.
- Gate (`view-recap`): kewenangan yang tidak menunjuk satu baris data.
- Policy (`AppointmentPolicy` dll.): kewenangan atas SATU data (pemilik, status). Dipanggil lewat `Gate::authorize(...)` di controller dan `authorize()` pada Form Request. Jangan memakai `$this->authorize`.
- Menu sesuai peran: `App\Support\Menu` menyaring menu dengan Gate/Policy yang sama, lalu dibagikan sebagai props `menu`. Tombol per baris memakai props `can`. React hanya menggambar.
- RBAC sederhana: satu kolom `role` + Policy. Cukup untuk dua peran.

## Tanggal
Disimpan `Y-m-d`. Tampilan: `App\Support\Tanggal::indonesia("2026-10-05")` => "5 Oktober 2026"
(pecah dengan `explode("-")`, bulan diganti lewat larik nama bulan). Dipakai oleh accessor `tanggal_indonesia` dan rekap bulanan.

## Pembuktian kewenangan (rekam layar untuk tugas)
`tests/Feature/AuthorizationTest.php` meniru penyerang: pasien membuka `/recap` (403), `DELETE /appointments/{id}` (403),
membaca janji orang lain (403), menyelundupkan `status`/`user_id` (diabaikan), jadwal bentrok (ditolak), keluhan terenkripsi di basis data.
