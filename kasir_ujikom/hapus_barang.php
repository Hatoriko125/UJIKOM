<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'koneksi.php';
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) exit('ID barang tidak valid.');
$stmt = mysqli_prepare($koneksi, "DELETE FROM barang WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
if (mysqli_stmt_execute($stmt)) {
    header("Location: barang.php");
    exit;
}
if (mysqli_errno($koneksi) === 1451) exit('Barang sudah digunakan dalam transaksi sehingga tidak dapat dihapus.');
exit('Gagal menghapus barang.');
?>