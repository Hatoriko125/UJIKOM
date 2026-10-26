<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
require 'koneksi.php';
$total_barang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM barang"))['total'];
$total_transaksi = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM penjualan"))['total'];
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COALESCE(SUM(total), 0) AS total FROM penjualan"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard KasirKu</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<nav class="navbar navbar-dark bg-primary"><div class="container">
<a class="navbar-brand" href="dashboard.php">KasirKu</a>
<span class="text-white"><?= htmlspecialchars($_SESSION['nama']) ?> <a href="logout.php" class="btn btn-light btn-sm ms-2">Logout</a></span>
</div></nav>
<div class="container py-4">
<h3>Dashboard</h3><p class="text-secondary">Selamat datang di aplikasi kasir.</p>
<div class="row g-3 mb-4">
<div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-secondary">Total Barang</p><h2><?= $total_barang ?></h2></div></div></div>
<div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-secondary">Total Transaksi</p><h2><?= $total_transaksi ?></h2></div></div></div>
<div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-secondary">Total Pendapatan</p><h4>Rp<?= number_format((float)$total_pendapatan, 0, ',', '.') ?></h4></div></div></div>
</div>
<div class="card border-0 shadow-sm"><div class="card-body"><h5>Menu Utama</h5>
<a href="barang.php" class="btn btn-primary me-2 mb-2">Data Barang</a>
<a href="transaksi.php" class="btn btn-success mb-2">Transaksi Penjualan</a>
<a href="riwayat.php" class="btn btn-outline-primary mb-2">Riwayat Transaksi</a>
</div></div></div>
</body></html>
