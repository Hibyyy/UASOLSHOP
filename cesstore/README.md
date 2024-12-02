# Online Shop Project

## Deskripsi Proyek
Proyek ini adalah aplikasi online shop sederhana yang memungkinkan pengguna untuk melihat produk, menambahkannya ke keranjang belanja, dan melakukan checkout. Sistem ini mendukung autentikasi pengguna dan menyediakan dashboard admin untuk mengelola produk dan transaksi.

## Fitur Utama
1. **Halaman Pengguna:**
   - Registrasi dan login pengguna.
   - Melihat daftar produk yang tersedia.
   - Menambahkan produk ke keranjang belanja.
   - Melakukan checkout dengan metode pembayaran yang tersedia.

2. **Halaman Admin:**
   - CRUD (Create, Read, Update, Delete) untuk produk.
   - Melihat dan mengelola transaksi.

3. **Proses Checkout:**
   - Menyimpan transaksi dengan status "Menunggu Konfirmasi."
   - Total pembayaran dihitung secara otomatis.

## Teknologi yang Digunakan
- **Frontend:** CSS, Bootstrap
- **Backend:** PHP
- **Database:** MySQL

## Cara Menjalankan Proyek
1. Clone repository ini ke lokal Anda.
2. Konfigurasi database pada file `config.php`:
   ```php
   $host = 'localhost';
   $dbname = 'nama_database';
   $username = 'username_database';
   $password = 'password_database';
   ```
3. Import file `database.sql` ke MySQL untuk membuat tabel yang diperlukan.
4. Jalankan server lokal Anda (seperti XAMPP atau WAMP).
5. Akses aplikasi melalui browser di `http://localhost/online-shop`.

## Struktur Direktori
- **config/**: Konfigurasi database.
- **admin/**: Halaman dan fungsi untuk admin.
- **user/**: Halaman dan fungsi untuk pengguna.
- **css/**: File CSS untuk styling.
- **js/**: File JavaScript untuk fungsi tambahan.

## Kontribusi
Kontribusi sangat diterima! Silakan buat pull request atau buka issue jika Anda menemukan bug atau memiliki ide untuk fitur baru.

## Lisensi
Proyek ini menggunakan lisensi [MIT](LICENSE).
ttd. AHMAD HABIBIE ARROUF

---
**Catatan:** Pastikan PHP dan MySQL sudah terinstal di perangkat Anda sebelum menjalankan proyek ini.

