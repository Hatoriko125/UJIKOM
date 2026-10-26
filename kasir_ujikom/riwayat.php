<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
require 'koneksi.php';
$data=mysqli_query($koneksi,"SELECT p.id,p.tanggal,p.total,p.bayar,p.kembalian,u.nama FROM penjualan p JOIN users u ON p.user_id=u.id ORDER BY p.id DESC");
$berhasil=filter_input(INPUT_GET,'berhasil',FILTER_VALIDATE_INT);
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Riwayat Transaksi</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body><div class="container py-4">
<div class="d-flex justify-content-between align-items-center mb-3"><h3>Riwayat Transaksi</h3><a href="dashboard.php" class="btn btn-secondary">Dashboard</a></div>
<?php if($berhasil): ?><div class="alert alert-success">Transaksi #<?= $berhasil ?> berhasil disimpan.</div><?php endif; ?>
<div class="card border-0 shadow-sm"><div class="card-body"><div class="table-responsive"><table class="table table-bordered table-striped"><thead class="table-primary"><tr><th>No</th><th>Tanggal</th><th>Kasir</th><th>Total</th><th>Bayar</th><th>Kembalian</th></tr></thead><tbody>
<?php $no=1; while($r=mysqli_fetch_assoc($data)): ?><tr><td><?= $no++ ?></td><td><?= htmlspecialchars($r['tanggal']) ?></td><td><?= htmlspecialchars($r['nama']) ?></td><td>Rp<?= number_format((float)$r['total'],0,',','.') ?></td><td>Rp<?= number_format((float)$r['bayar'],0,',','.') ?></td><td>Rp<?= number_format((float)$r['kembalian'],0,',','.') ?></td></tr><?php endwhile; ?>
<?php if(mysqli_num_rows($data)===0): ?><tr><td colspan="6" class="text-center">Belum ada transaksi.</td></tr><?php endif; ?>
</tbody></table></div><a href="transaksi.php" class="btn btn-success">Transaksi Baru</a></div></div></div></body></html>
