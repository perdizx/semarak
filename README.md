# SEMARAK

**SEMARAK** (Sistem Monitoring & Raport Karakter Anak) adalah aplikasi web untuk mencatat dan memantau perkembangan anak melalui evaluasi kegiatan harian.

## Fitur

- Dashboard ringkasan evaluasi dengan filter angkatan, status, dan rentang tanggal.
- Pengelolaan data anak dan kegiatan.
- Pencatatan evaluasi kehadiran/perilaku kegiatan.
- Ringkasan perkembangan individu dan pembuatan rapor.
- Pengaturan jadwal kegiatan dan masa pembinaan.

## Teknologi

- PHP 8 atau lebih baru
- MySQL/MariaDB
- HTML, CSS, dan JavaScript
- Tailwind CSS dan Font Awesome melalui CDN

## Menjalankan secara lokal dengan XAMPP

1. Pastikan Apache dan MySQL sudah terpasang, lalu jalankan keduanya dari XAMPP Control Panel.
2. Salin atau clone proyek ini ke folder `htdocs`, misalnya `C:\xampp\htdocs\semarak`.
3. Buat database dan tabel dengan mengimpor file [`db/semarak.sql`](db/semarak.sql) melalui phpMyAdmin:
   - Buka `http://localhost/phpmyadmin`.
   - Pilih menu **Import**, pilih file `db/semarak.sql`, lalu jalankan impor.
4. Periksa konfigurasi database di [`includes/db.php`](includes/db.php). Nilai bawaan untuk XAMPP adalah:
   - Host: `localhost`
   - Port: `3306`
   - Database: `semarak`
   - User: `root`
   - Password: kosong
5. Buka aplikasi di `http://localhost/semarak/`.

> Tailwind CSS dan Font Awesome dimuat dari CDN, jadi koneksi internet diperlukan agar tampilan dan ikon eksternal dapat dimuat.

## Struktur proyek

```text
semarak/
├── anak.php               # Data anak
├── absensi.php            # Input evaluasi
├── index.php              # Dashboard
├── summary.php            # Ringkasan individu
├── report.php             # Rapor
├── pengaturan.php         # Pengaturan aplikasi
├── api.php                # Endpoint data aplikasi
├── includes/              # Koneksi database, backend, header, dan footer
├── db/semarak.sql         # Skema database dan data contoh
└── assets/                # CSS, JavaScript, dan logo
```

## Catatan

- Database `semarak` perlu tersedia sebelum aplikasi dijalankan. File SQL di atas membuat database dan tabel yang dibutuhkan.
- Aplikasi juga menyiapkan tabel dan data awal ketika backend dijalankan, selama koneksi database berhasil.
- Data contoh yang disertakan ditujukan untuk pengembangan lokal. Tinjau dan ganti data tersebut sebelum menggunakan aplikasi untuk data nyata.
