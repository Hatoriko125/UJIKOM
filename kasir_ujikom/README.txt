KASIR UJIKOM - PHP NATIVE + MYSQL (XAMPP)

CARA INSTALASI
1. Ekstrak folder kasir_ujikom ke C:\xampp\htdocs\
2. Buka XAMPP, jalankan Apache dan MySQL.
3. Buka http://localhost/phpmyadmin
4. Import file database.sql.
5. Buka http://localhost/kasir_ujikom/

LOGIN AWAL
Username: admin
Password: admin123

FITUR
- Login/logout menggunakan PHP session
- Dashboard ringkas
- CRUD barang
- Pencarian barang berdasarkan kode/nama
- Transaksi satu jenis barang per transaksi
- Pengurangan stok saat transaksi tersimpan
- Riwayat transaksi

CATATAN
- Koneksi default: localhost, user root, password kosong. Ubah koneksi.php jika konfigurasi MySQL berbeda.
- Bootstrap memakai CDN, jadi internet dibutuhkan untuk memuat styling.
- Jika login gagal karena hash password, buat file sementara generate_hash.php berisi:
  <?php echo password_hash('admin123', PASSWORD_DEFAULT); ?>
  Buka file tersebut lewat localhost, salin hash yang tampil, lalu ganti hash pada INSERT users di database.sql dan import ulang database.
- Ini kerangka belajar UJIKOM. Sebelum digunakan lebih jauh, tambahkan validasi dan proteksi CSRF sesuai kebutuhan.
