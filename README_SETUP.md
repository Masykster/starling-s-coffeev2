# Panduan Instalasi & Setup - Starling's Coffee

Ikuti langkah-langkah berikut untuk menjalankan aplikasi Starling's Coffee di komputer lokal Anda.

## 📋 Prasyarat
Pastikan Anda telah menginstal aplikasi server lokal:
* **XAMPP** (Rekomendasi) atau WAMP/MAMP/Laragon.
* Web Browser (Chrome, Firefox, atau Edge).

## ⚙️ Langkah Instalasi

### 1. Persiapan File
1.  Download source code proyek ini.
2.  Ekstrak folder proyek.
3.  Ubah nama folder menjadi `starling-coffee`.
4.  Pindahkan folder tersebut ke direktori `htdocs` pada instalasi XAMPP Anda (biasanya di `C:\xampp\htdocs\`).

### 2. Konfigurasi Database
1.  Buka **XAMPP Control Panel** dan nyalakan modul **Apache** dan **MySQL**.
2.  Buka browser dan akses `http://localhost/phpmyadmin`.
3.  Buat database baru dengan nama:
    ```sql
    starling_coffee
    ```
4.  Klik tab **Import**.
5.  Pilih file `starling_coffee.sql` yang ada di dalam folder proyek.
6.  Klik tombol **Import** atau **Go** di bagian bawah.

### 3. Konfigurasi Koneksi (Opsional)
Jika Anda menggunakan password untuk root MySQL atau port yang berbeda, sesuaikan file `config.php`:
1.  Buka file `config.php` di text editor.
2.  Sesuaikan bagian:
    ```php
    $host = 'localhost';
    $user = 'root';      // Default XAMPP: root
    $pass = '';          // Default XAMPP: kosong
    $db   = 'starling_coffee';
    ```

### 4. Menjalankan Aplikasi

**Akses Halaman User (Pelanggan):**
Buka browser dan kunjungi:
http://localhost/starling-coffee/


**Akses Halaman Admin:**
Buka browser dan kunjungi:
http://localhost/starling-coffee/admin/


## 🔐 Akun Default
Gunakan kredensial berikut untuk masuk ke sistem:

### Akun Admin
* **Username:** `admin` atau `admin@starlingcoffee.com`
* **Password:** `admin123`

### Akun User (Contoh)
* **Email:** `user@gmail.com`
* **Password:** `password`
*(Atau Anda bisa melakukan Registrasi akun baru di halaman utama)*

## ⚠️ Troubleshooting
* **Gambar tidak muncul:** Pastikan folder `images/` memiliki izin baca/tulis (permissions).
* **Error Database:** Pastikan nama database di phpMyAdmin sama persis dengan yang ada di `config.php`.
* **Gagal Login:** Pastikan file SQL sudah diimpor dengan benar dan tabel `users` serta `admins` sudah terisi.
