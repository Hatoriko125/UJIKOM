<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
require 'koneksi.php';
$cari = trim($_GET['cari'] ?? '');
$keyword = "%{$cari}%";
$stmt = mysqli_prepare($koneksi, "SELECT * FROM barang WHERE kode_barang LIKE ? OR nama_barang LIKE ? ORDER BY id DESC");
mysqli_stmt_bind_param($stmt, "ss", $keyword, $keyword);
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html><html lang="id"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Data Barang</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="assets/style.css"></head>
<body><div class="container py-4">
<div class="d-flex justify-content-between align-items-center mb-3"><h3>Data Barang</h3><a href="dashboard.php" class="btn btn-secondary">Dashboard</a></div>
<div class="card border-0 shadow-sm"><div class="card-body">
<div class="d-flex flex-wrap justify-content-between gap-2 mb-3"><a href="tambah_barang.php" class="btn btn-primary">+ Tambah Barang</a>
<form method="GET" class="d-flex gap-2"><input type="text" name="cari" class="form-control" placeholder="Cari kode/nama..." value="<?= htmlspecialchars($cari) ?>"><button class="btn btn-outline-primary">Cari</button><a href="barang.php" class="btn btn-outline-secondary">Reset</a></form></div>
<div class="table-responsive"><table class="table table-bordered table-striped align-middle"><thead class="table-primary"><tr><th>No</th><th>Kode</th><th>Nama Barang</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr></thead><tbody>
<?php $no=1; while ($row=mysqli_fetch_assoc($data)): ?>
<tr><td><?= $no++ ?></td><td><?= htmlspecialchars($row['kode_barang']) ?></td><td><?= htmlspecialchars($row['nama_barang']) ?></td><td>Rp<?= number_format((float)$row['harga'],0,',','.') ?></td><td><?= $row['stok'] ?></td><td class="text-nowrap">
<a class="btn btn-warning btn-sm" href="edit_barang.php?id=<?= $row['id'] ?>">Edit</a>
<form action="hapus_barang.php" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus barang ini?')"><input type="hidden" name="id" value="<?= $row['id'] ?>"><button class="btn btn-danger btn-sm">Hapus</button></form>
</td></tr>
<?php endwhile; if (mysqli_num_rows($data)===0): ?><tr><td colspan="6" class="text-center">Data tidak ditemukan.</td></tr><?php endif; ?>
</tbody></table></div></div></div></div></body></html>
