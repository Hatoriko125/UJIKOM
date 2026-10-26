CREATE DATABASE IF NOT EXISTS db_kasir_ujikom;
USE db_kasir_ujikom;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL
);

-- Password admin dibuat dengan password_hash('admin123', PASSWORD_DEFAULT)
-- Jika hash tidak cocok pada instalasi PHP Anda, buat hash baru dengan password_hash().
INSERT INTO users (username, password, nama) VALUES
('admin',
 '$2y$12$oKXYGLop4uXh0dSuqoaIduomruto20KyDV47Pk.traqifk5pvohEe',
 'Administrator');

CREATE TABLE barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_barang VARCHAR(30) NOT NULL UNIQUE,
    nama_barang VARCHAR(100) NOT NULL,
    harga DECIMAL(12,2) NOT NULL DEFAULT 0,
    stok INT NOT NULL DEFAULT 0
);

INSERT INTO barang (kode_barang, nama_barang, harga, stok) VALUES
('BRG001', 'Buku Tulis', 5000, 20),
('BRG002', 'Pulpen', 3000, 30),
('BRG003', 'Pensil', 2000, 25);

CREATE TABLE penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_id INT NOT NULL,
    total DECIMAL(12,2) NOT NULL,
    bayar DECIMAL(12,2) NOT NULL,
    kembalian DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE detail_penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    penjualan_id INT NOT NULL,
    barang_id INT NOT NULL,
    harga DECIMAL(12,2) NOT NULL,
    jumlah INT NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (penjualan_id) REFERENCES penjualan(id),
    FOREIGN KEY (barang_id) REFERENCES barang(id)
);
