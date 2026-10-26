<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
require 'koneksi.php';
$kode=trim($_POST['kode_barang']??''); $nama=trim($_POST['nama_barang']??'');
$harga=filter_input(INPUT_POST,'harga',FILTER_VALIDATE_FLOAT); $stok=filter_input(INPUT_POST,'stok',FILTER_VALIDATE_INT);
if ($kode==='' || $nama==='' || $harga===false || $harga===null || $harga<0 || $stok===false || $stok===null || $stok<0) exit('Data tidak valid. <a href="tambah_barang.php">Kembali</a>');
$stmt=mysqli_prepare($koneksi,"INSERT INTO barang (kode_barang,nama_barang,harga,stok) VALUES (?,?,?,?)");
mysqli_stmt_bind_param($stmt,"ssdi",$kode,$nama,$harga,$stok);
if (mysqli_stmt_execute($stmt)) { header("Location: barang.php"); exit; }
if (mysqli_errno($koneksi)===1062) exit('Kode barang sudah digunakan. <a href="tambah_barang.php">Kembali</a>');
exit('Gagal menyimpan barang.');
?>
