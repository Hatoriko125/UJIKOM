<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'koneksi.php';
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$kode = trim($_POST['kode_barang'] ?? '');
$nama = trim($_POST['nama_barang'] ?? '');
$harga = filter_input(INPUT_POST, 'harga', FILTER_VALIDATE_FLOAT);
$stok = filter_input(INPUT_POST, 'stok', FILTER_VALIDATE_INT);
if (!$id || $id < 1 || $kode === '' || $nama === '' || $harga === false || $harga === null || $harga < 0 || $stok === false || $stok === null || $stok < 0) exit('Data tidak valid.');
$stmt = mysqli_prepare($koneksi, "UPDATE barang SET kode_barang=?, nama_barang=?, harga=?, stok=? WHERE id=?");
mysqli_stmt_bind_param($stmt, "ssdii", $kode, $nama, $harga, $stok, $id);
if (mysqli_stmt_execute($stmt)) {
    header("Location: barang.php");
    exit;
}
if (mysqli_errno($koneksi) === 1062) exit('Kode barang sudah dipakai barang lain.');
exit('Gagal mengubah barang.');
