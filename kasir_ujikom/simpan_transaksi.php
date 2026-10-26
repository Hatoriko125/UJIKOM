<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
require 'koneksi.php';
$barang_id=filter_input(INPUT_POST,'barang_id',FILTER_VALIDATE_INT);
$jumlah=filter_input(INPUT_POST,'jumlah',FILTER_VALIDATE_INT);
$bayar=filter_input(INPUT_POST,'bayar',FILTER_VALIDATE_FLOAT);
$user_id=(int)$_SESSION['user_id'];
if (!$barang_id || $barang_id<1 || !$jumlah || $jumlah<1 || $bayar===false || $bayar===null || $bayar<0) exit('Input transaksi tidak valid.');

mysqli_begin_transaction($koneksi);
try {
    $stmt=mysqli_prepare($koneksi,"SELECT harga,stok FROM barang WHERE id=? FOR UPDATE");
    mysqli_stmt_bind_param($stmt,"i",$barang_id); mysqli_stmt_execute($stmt);
    $barang=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$barang) throw new Exception("Barang tidak ditemukan.");
    if ($jumlah>(int)$barang['stok']) throw new Exception("Stok barang tidak mencukupi.");
    $harga=(float)$barang['harga']; $total=round($harga*$jumlah,2);
    if ($bayar<$total) throw new Exception("Uang pembayaran kurang.");
    $kembalian=round($bayar-$total,2);

    $stmt=mysqli_prepare($koneksi,"INSERT INTO penjualan (user_id,total,bayar,kembalian) VALUES (?,?,?,?)");
    mysqli_stmt_bind_param($stmt,"iddd",$user_id,$total,$bayar,$kembalian);
    mysqli_stmt_execute($stmt); $penjualan_id=mysqli_insert_id($koneksi);

    $subtotal=$total;
    $stmt=mysqli_prepare($koneksi,"INSERT INTO detail_penjualan (penjualan_id,barang_id,harga,jumlah,subtotal) VALUES (?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt,"iidid",$penjualan_id,$barang_id,$harga,$jumlah,$subtotal);
    mysqli_stmt_execute($stmt);

    $stmt=mysqli_prepare($koneksi,"UPDATE barang SET stok=stok-? WHERE id=? AND stok>=?");
    mysqli_stmt_bind_param($stmt,"iii",$jumlah,$barang_id,$jumlah); mysqli_stmt_execute($stmt);
    if (mysqli_stmt_affected_rows($stmt)!==1) throw new Exception("Gagal memperbarui stok.");
    mysqli_commit($koneksi);
    header("Location: riwayat.php?berhasil=".$penjualan_id);
    exit;
} catch (Throwable $e) {
    mysqli_rollback($koneksi);
    http_response_code(400);
    echo '<!DOCTYPE html><html lang="id"><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Transaksi Gagal</title><body style="font-family:Arial;padding:24px"><h3>Transaksi gagal</h3><p>'.htmlspecialchars($e->getMessage()).'</p><a href="transaksi.php">Kembali ke transaksi</a></body></html>';
}
?>
