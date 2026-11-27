# Campus Voice

**Campus Voice** adalah sistem informasi aspirasi kampus berbasis web yang dikembangkan menggunakan **Laravel 10**, **PHP**, dan **MySQL**. Aplikasi ini menyediakan satu linimasa terpusat bagi mahasiswa dan pegawai/tendik untuk menyampaikan aspirasi, kritik, saran, maupun keluhan terkait lingkungan kampus, memantau tanggapan serta bukti tindak lanjut dari pihak pegawai/tendik, dan memungkinkan admin mengawasi serta memoderasi konten sesuai aturan yang berlaku.

**Repository:** https://github.com/analisis335-ai/PROJECT-CAMPUS-VOICE-RPL

---

## Daftar Isi

- [Gambaran Umum Aplikasi](#gambaran-umum-aplikasi)
- [Peran Pengguna & Hak Akses](#peran-pengguna--hak-akses)
- [Fitur Utama](#fitur-utama)
- [Teknologi](#teknologi)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi dan Menjalankan Aplikasi](#instalasi-dan-menjalankan-aplikasi)
- [Pengujian Otomatis](#pengujian-otomatis)
- [Akun Demo](#akun-demo)
- [Validasi & Penyimpanan Unggahan](#validasi--penyimpanan-unggahan)
- [Struktur Proyek](#struktur-proyek)
- [Tim Pengembang](#tim-pengembang)

---

## Gambaran Umum Aplikasi

Campus Voice membagi alur interaksi menjadi dua gerbang utama:

1. **Portal Pengguna (`/`)** — Halaman awal (`awal.blade.php`) bagi **Mahasiswa** dan **Pegawai / Tendik** untuk mendaftar (`/daftar`) atau masuk (`/masuk`) menggunakan NIM/NIP dan password. Setelah masuk, pengguna diarahkan ke **Dashboard (`/dasbor`)** berupa linimasa aspirasi yang dilengkapi pencarian kata kunci, fitur buat postingan (`/buat-postingan`), like, komentar, kotak masuk (`/kotak-masuk`), profil akun (`/akun`), dan pengaturan sandi (`/pengaturan`).
2. **Portal Admin (`/admin`)** — Halaman khusus pengelola sistem (`admin/awal.blade.php`) untuk masuk (`/admin/masuk`) menggunakan **Nama Admin** dan password. Di **Dashboard Admin (`/admin/dasbor`)**, admin dapat meninjau seluruh postingan dan komentar aktif, memeriksa detail identitas serta file bukti akun pengguna melalui modal profil, mengirim peringatan, melakukan hapus paksa, memblokir akun, serta memantau **Kotak Masuk Admin (`/admin/kotak_masuk`)** untuk pemulihan konten dan pembukaan blokir.

---

## Peran Pengguna & Hak Akses

Pembatasan akses diatur melalui middleware `CekPeran` (`peran`) di `app/Http/Middleware/CekPeran.php`. Pengguna berstatus `Diblokir` akan otomatis dikeluarkan dari sesi dan ditolak saat mengakses halaman terproteksi.

### 1. Mahasiswa (`peran: Mahasiswa`)
- Mendaftar menggunakan nama lengkap, peran `Mahasiswa`, NIM, password (min. 6 karakter), dan unggahan bukti KTM (`JPG/PNG/PDF`, maks. 2 MB).
- Masuk menggunakan NIM dan password.
- Melihat linimasa postingan aktif dan mencari postingan berdasarkan deskripsi atau nama penulis.
- Membuat postingan teks beserta lampiran gambar opsional (`JPG/JPEG/PNG/WEBP`, maks. 2 MB). *(Catatan: isi postingan tidak dapat diedit setelah dikirim).*
- Memberi atau membatalkan **Suka (Like)** serta menambahkan **Komentar** pada postingan aktif.
- Melihat **Kotak Masuk** (pesan peringatan dari admin, riwayat postingan aktif, dan riwayat komentar aktif) serta menghapus postingan atau komentar miliknya sendiri.
- Melihat halaman **Data Akun** (beserta tautan bukti KTM terunggah dan total peringatan) serta mengubah password di **Pengaturan**.

### 2. Pegawai / Tenaga Kependidikan (`peran: Pegawai`)
- Memiliki seluruh hak akses pengguna umum (mendaftar dengan NIP & bukti kepegawaian, masuk dengan NIP, melihat & mencari linimasa, membuat postingan, like, komentar, kotak masuk, menghapus konten sendiri, dan ubah password).
- **Hak khusus Pegawai:** Mengunggah **Bukti Penyelesaian** (`POST /postingan/{id}/bukti`) berupa tanggapan teks dan lampiran file (`JPG/JPEG/PNG/PDF`, maks. 2 MB) pada postingan aspirasi sebagai dokumentasi tindak lanjut.

### 3. Admin (`peran: Admin`)
- Masuk melalui `/admin/masuk` menggunakan **Nama Admin** (*case-insensitive*) dan password.
- Melihat dan mencari postingan aktif di `/admin/dasbor`, serta berinteraksi (memberi like dan komentar).
- Melihat detail akun pengguna (nama, NIM/NIP, peran, status, total peringatan, dan file bukti akun privat) langsung dari nama penulis pada postingan atau komentar.
- Mengirim **Peringatan** ke kotak masuk pemilik postingan atau komentar.
- Melakukan **Hapus Paksa** pada postingan atau komentar setelah melewati masa tunggu **minimal 1 jam** sejak peringatan dikirim dan konten masih aktif.
- Melakukan **Blokir Akun** terhadap pengguna non-admin yang telah menerima **minimal 3 peringatan**.
- Memantau **Kotak Masuk Admin** (`/admin/kotak_masuk`) yang terdiri dari 3 tab:
  - *Peringatan Terkirim* (disertai tombol tindak lanjut jika sudah ≥ 1 jam).
  - *Konten Dihapus* (dapat **dipulihkan** kembali ke linimasa selama belum melewati batas waktu **< 1 hari** sejak dihapus).
  - *Akun Terblokir* (dapat **dibuka kembali blokirnya** agar pengguna dapat login lagi).
- Mengubah password admin di `/admin/pengaturan`.

---

## Fitur Utama

| Fitur | Route / Endpoint Utama | Keterangan |
|---|---|---|
| **Halaman Awal & Portal Admin** | `/`, `/admin` | Landing page terpisah untuk pengguna kampus dan admin pengelola. |
| **Registrasi & Verifikasi Identitas** | `/daftar` | Pendaftaran Mahasiswa/Pegawai dengan unggahan bukti identitas ke folder privat (`private/bukti_akun`). |
| **Autentikasi Berbasis Peran** | `/masuk`, `/admin/masuk`, `/keluar` | Login pengguna via NIM/NIP dan login admin via Nama Admin, dilindungi middleware `CekPeran`. |
| **Linimasa & Pencarian Aspirasi** | `/dasbor`, `/admin/dasbor` | Menampilkan postingan aktif terbaru dengan filter pencarian `?q=` pada isi postingan dan nama penulis. |
| **Buat Postingan** | `/buat-postingan` | Publikasi aspirasi teks dengan lampiran gambar opsional ke disk `public`. |
| **Interaksi Sosial** | `/postingan/{id}/suka`, `/postingan/{id}/komentar` | Toggle like (1 kali per pengguna per postingan) dan tambah komentar. |
| **Tindak Lanjut & Bukti Penyelesaian** | `/postingan/{id}/bukti` | Khusus peran `Pegawai` untuk melampirkan keterangan dan file bukti penyelesaian pada postingan. |
| **Kotak Masuk Pengguna** | `/kotak-masuk` | Menampilkan peringatan dari admin serta riwayat postingan/komentar aktif beserta tombol hapus milik sendiri. |
| **Moderasi & Pemulihan Admin** | `/admin/peringatan/...`, `/admin/hapus/...`, `/admin/blokir/...`, `/admin/pulihkan/...`, `/admin/buka-blokir/...` | Alur moderasi bertahap: peringatan -> jeda 1 jam untuk hapus paksa -> blokir akun (≥ 3 peringatan) -> pemulihan konten (< 1 hari). |
| **Data Akun & Pengaturan Sandi** | `/akun`, `/bukti-akun/{id}`, `/pengaturan`, `/pengaturan/sandi` | Informasi profil, akses aman file bukti privat (hanya pemilik atau Admin), dan ubah password. |

---

## Teknologi

| Bagian | Teknologi yang Digunakan |
|---|---|
| **Bahasa Pemrograman** | PHP `^8.1` |
| **Framework Backend** | Laravel `^10.10` (dengan Laravel Sanctum `^3.3`) |
| **Antarmuka (Frontend)** | Blade Templates, HTML5, CSS kustom (`public/aset/css/gaya.css`), JavaScript vanilla, Google Fonts (*Plus Jakarta Sans*) |
| **Database** | MySQL (6 tabel: `pengguna`, `postingan`, `komentar`, `suka`, `bukti_penyelesaian`, `peringatan`) |
| **Pengujian & Code Style** | PHPUnit `^10.1`, Laravel Pint `^1.0` |
| **Asset Bundler (Opsional)** | Vite `^5.0.0` (tersedia di `package.json`, namun seluruh view saat ini memuat langsung `public/aset/css/gaya.css`) |

---

## Persyaratan Sistem

- **PHP** >= 8.1 (dengan ekstensi standar Laravel aktif: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, dll.)
- **Composer**
- **MySQL** (atau MariaDB yang kompatibel, misalnya melalui **Laragon** atau **XAMPP** di Windows)
- **Node.js & npm** *(opsional, hanya dibutuhkan jika ingin menggunakan atau memodifikasi pipeline Vite pada `resources/css` dan `resources/js`)*

---

## Instalasi dan Menjalankan Aplikasi

1. **Clone repository dan masuk ke direktori proyek:**
   ```bash
   git clone https://github.com/analisis335-ai/PROJECT-CAMPUS-VOICE-RPL.git
   cd PROJECT-CAMPUS-VOICE-RPL
   ```

2. **Pasang dependensi PHP:**
   ```bash
   composer install
   ```

3. **Salin file konfigurasi lingkungan dan generate key aplikasi:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi database MySQL di file `.env`:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=campus_voice
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   Pastikan database `campus_voice` sudah dibuat di MySQL Anda.

5. **Jalankan migrasi tabel dan seeder data demo:**
   ```bash
   php artisan migrate --seed
   ```

6. **Buat symbolic link storage agar gambar postingan dan bukti penyelesaian dapat diakses:**
   ```bash
   php artisan storage:link
   ```

7. **Jalankan server pengembangan lokal:**
   ```bash
   php artisan serve
   ```
   Akses aplikasi melalui browser di `http://127.0.0.1:8000` (atau menggunakan virtual host Laragon jika tersedia).

---

## Pengujian Otomatis

Proyek ini dilengkapi feature test pada `tests/Feature/CampusVoiceTest.php` yang menguji:
- Akses halaman publik (`/`, `/daftar`, `/masuk`, `/admin`, `/admin/masuk`).
- Alur mahasiswa & pegawai (login, buat postingan, like, komentar, dan unggah bukti penyelesaian).
- Alur moderasi admin (hapus paksa setelah 1 jam, blokir akun dengan 3 peringatan, pemulihan konten `< 1 hari`, dan buka blokir).

Pastikan database telah ter-migrasi dan ter-seed (karena `CampusVoiceTest` menggunakan trait `DatabaseTransactions`), lalu jalankan:
```bash
php artisan test
```

---

## Akun Demo

Seeder (`database/seeders/DatabaseSeeder.php`) menyediakan akun contoh berikut untuk pengujian lokal:

| Peran | Nama | NIM / NIP | Password | Catatan Login |
|---|---|---|---|---|
| **Mahasiswa** | `Budi Santoso` | `230101001` | `123456` | Login di `/masuk` menggunakan NIM |
| **Pegawai** | `Siti Aminah` | `1989041201` | `123456` | Login di `/masuk` menggunakan NIP |
| **Mahasiswa** | `Rizky Pratama` | `230101002` | `123456` | Akun uji dengan 3 peringatan & konten siap moderasi |
| **Admin** | `Admin Kampus` | `ADMIN01` | `admin123` | Login di `/admin/masuk` menggunakan **Nama Admin** (`Admin Kampus`) |

> **Peringatan Keamanan:** Kredensial di atas hanya untuk demonstrasi lokal. Ganti atau hapus akun seeder sebelum aplikasi dijalankan di lingkungan produksi/publik.

---

## Validasi & Penyimpanan Unggahan

| Jenis Unggahan | Format Diizinkan | Ukuran Maks. | Lokasi Penyimpanan |
|---|---|---|---|
| **Bukti Identitas Akun** (KTM / Kepegawaian) | `JPG`, `JPEG`, `PNG`, `PDF` | 2 MB | `storage/app/private/bukti_akun` *(privat, hanya dapat dibuka oleh pemilik akun atau Admin via `/bukti-akun/{id}`)* |
| **Gambar Postingan** | `JPG`, `JPEG`, `PNG`, `WEBP` | 2 MB | `storage/app/public/unggahan_postingan` |
| **Bukti Penyelesaian** (Pegawai/Tendik) | `JPG`, `JPEG`, `PNG`, `PDF` | 2 MB | `storage/app/public/bukti_penyelesaian` |

- Panjang password saat pendaftaran maupun pembaruan password minimal **6 karakter**.

---

## Struktur Proyek

```text
PROJECT-CAMPUS-VOICE-RPL/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php         # Moderasi, peringatan, hapus paksa, pulihkan, blokir/buka blokir
│   │   │   ├── AutentikasiController.php   # Registrasi, login pengguna & admin, logout
│   │   │   └── DasborController.php        # Linimasa, pencarian, postingan, like, komentar, bukti, akun, sandi
│   │   └── Middleware/
│   │       └── CekPeran.php                # Pembatasan akses peran & pencegahan akun terblokir
│   └── Models/
│       ├── BuktiPenyelesaian.php
│       ├── Komentar.php
│       ├── Pengguna.php
│       ├── Peringatan.php
│       ├── Postingan.php
│       └── Suka.php
├── database/
│   ├── migrations/
│   │   └── 2026_01_01_000001_buat_tabel_campus_voice.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── public/
│   └── aset/
│       └── css/
│           └── gaya.css                    # Stylesheet utama aplikasi
├── resources/
│   └── views/
│       ├── admin/                          # Tampilan portal & moderasi admin
│       ├── komponen/                       # Komponen kartu_postingan.blade.php
│       ├── tata_letak/                     # Layout utama.blade.php & polos.blade.php
│       └── *.blade.php                     # Halaman awal, daftar, masuk, dasbor, buat postingan, akun, kotak masuk, pengaturan
├── routes/
│   └── web.php                             # Definisi seluruh route aplikasi
├── storage/                                # Penyimpanan file privat, publik, dan log aplikasi
├── tests/
│   └── Feature/
│       └── CampusVoiceTest.php             # Feature test alur utama & moderasi
├── MAINTENANCE.md                          # Dokumentasi rencana maintenance (Markdown)
├── MAINTENANCE.docx                        # Dokumentasi rencana maintenance (Word)
├── README.md                               # Dokumentasi utama proyek
├── composer.json
├── package.json
└── phpunit.xml
```

---

## Tim Pengembang

Project ini dikembangkan untuk kebutuhan pembelajaran mata kuliah **Rekayasa Perangkat Lunak (RPL)** oleh:

| Nama | Peran | Tanggung Jawab |
|---|---|---|
| Muhammad Adhit Asis | Scrum Master | Fitur Admin, kerangka aplikasi |
| Kenzo Wiliam | Developer 1 | Akun, halaman awal, dan postingan |
| Naufal Abdillah | Developer 2 | Pendaftaran, dashboard, dan kotak masuk |
| Deris Apriliansyah | Developer 3 | Login, pengaturan, dan halaman awal/welcome |
| Fadhil Akbar Burhanuddin | Product Owner | Dokumentasi project (README) |

---

***Campus Voice — Sampaikan aspirasi, lihat tindak lanjutnya.***
