# 📘 Quiz gaje 666 – Panduan Instalasi

Dokumen ini berisi panduan lengkap dan terstruktur untuk melakukan **instalasi, konfigurasi, pengujian, serta penanganan masalah** pada aplikasi **Quiz gaje 666** di server hosting.

---

## 📋 Prasyarat Sistem

Pastikan server Anda memenuhi spesifikasi berikut sebelum instalasi:

- Web server: **Apache** atau **Nginx**
- **PHP versi 7.4 atau lebih baru**
- Database **MySQL / MariaDB**
- Akses ke:
  - Control Panel Hosting (cPanel / DirectAdmin / Plesk), atau
  - FTP Client (misalnya FileZilla)

---

## 🚀 Langkah-Langkah Instalasi

### 1. Persiapan Database (phpMyAdmin)

Langkah awal adalah menyiapkan database sebagai media penyimpanan data aplikasi.

1. Masuk ke **phpMyAdmin** melalui panel hosting Anda.
2. Buat database baru  
   Contoh: `quiz_gaje_db`
3. Klik database yang baru dibuat.
4. Pilih tab **Import**.
5. Klik **Choose File**, lalu pilih file `setup.sql` dari folder proyek.
6. Klik **Go / Import**.
7. Pastikan muncul pesan berhasil dan tabel berikut telah terbentuk:
   - `users`
   - `leaderboard`

---

### 2. Konfigurasi Aplikasi (`config.php`)

Aplikasi memerlukan konfigurasi database agar fitur **Login**, **Registrasi**, dan **Leaderboard** dapat berfungsi.

Buka file `config.php`, lalu sesuaikan bagian berikut:

```php
define('DB_HOST', 'localhost');          // Gunakan 'localhost' terlebih dahulu.
define('DB_USER', 'username_db_anda');   // Username database dari panel hosting
define('DB_PASS', 'password_db_anda');   // Password database
define('DB_NAME', 'nama_database_anda'); // Nama database hasil langkah 1
```

### Catatan Konfigurasi Penting

- **Jangan gunakan user `root`** untuk koneksi database.
- Jika koneksi database gagal, nilai `DB_HOST` mungkin perlu diisi dengan **host atau IP internal** yang disediakan oleh panel database hosting (bukan `localhost`).
- Pastikan **menyimpan perubahan** pada file konfigurasi setelah selesai mengedit.

---

## 3. Mengunggah File ke Server

Setelah konfigurasi database selesai, langkah berikutnya adalah mengunggah file aplikasi ke server.

1. Buka **File Manager** melalui panel hosting atau gunakan **FTP Client** (misalnya FileZilla).
2. Masuk ke folder root website, yang umumnya bernama:
   - `public_html`
   - `www`
   - `/`
3. Unggah file berikut ke folder tersebut:
   - `index.php`
   - `config.php`

> ⚠️ **Penting:**  
> Jangan mengunggah file `setup.sql` ke folder publik.  
> File ini hanya digunakan untuk proses impor database dan dapat menimbulkan risiko keamanan jika dapat diakses secara umum.

---

## 🛠️ Pengujian Aplikasi

Untuk memastikan aplikasi berjalan dengan normal, lakukan pengujian berikut:

1. Buka browser dan akses:
"http://domainanda.com/index.php"

2. Jika halaman utama tampil:
- Klik **Masuk**
- Pilih **Daftar** untuk membuat akun baru
3. Kerjakan salah satu kuis yang tersedia:
- SKKM
- Ilmu Komputer
- Ilmu Gajelas
4. Setelah kuis selesai, pastikan skor Anda muncul di halaman:
- **Hall of Fame (Leaderboard)**

---

## ⚠️ Troubleshooting (Masalah Umum)

### ❌ Database Connection Failed

Kemungkinan penyebab dan solusi:
- Periksa kembali nilai `DB_USER` dan `DB_PASS`.
- Pastikan user database memiliki **hak akses penuh (privileges)**.
- Pada cloud hosting, `DB_HOST` sering kali **bukan `localhost`**, melainkan host atau IP khusus dari panel database hosting.

---

### ❌ Error 404 / File Not Found

- Pastikan file `index.php` berada di folder root website yang benar.
- Periksa apakah terdapat file `.htaccess` yang membatasi atau memblokir akses.

---

### ❌ ERR_SSL_PROTOCOL_ERROR

Masalah ini terjadi ketika browser memaksa koneksi HTTPS sementara SSL belum aktif di server.

**Solusi:**
- Akses website menggunakan `http://` (tanpa huruf `s`)
- Gunakan mode **Incognito / Private**
- Bersihkan cache browser (termasuk cache HSTS)
- Pastikan sertifikat SSL (misalnya **Let’s Encrypt**) sudah diaktifkan melalui panel hosting

---

### ❌ Fitur Daftar / Login Tidak Berfungsi

- Pastikan file `setup.sql` telah berhasil diimpor ke database.
- Periksa keberadaan tabel:
- `users`
- `leaderboard`
- Pastikan versi PHP server mendukung:
- `password_hash()` → **PHP 5.5+**
- Direkomendasikan **PHP 7.4 atau lebih baru**

---
