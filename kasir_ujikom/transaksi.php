<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
require 'koneksi.php';
$barang=mysqli_query($koneksi,"SELECT id,kode_barang,nama_barang,harga,stok FROM barang WHERE stok>0 ORDER BY nama_barang");
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Transaksi Penjualan</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light"><div class="container py-5"><div class="card border-0 shadow-sm mx-auto" style="max-width:650px"><div class="card-body p-4"><h4>Transaksi Penjualan</h4><p class="text-secondary">Pilih satu barang dan masukkan jumlah pembelian.</p>
<form action="simpan_transaksi.php" method="POST">
<div class="mb-3"><label class="form-label">Pilih Barang</label><select name="barang_id" id="barang" class="form-select" required><option value="">-- Pilih Barang --</option>
<?php while($b=mysqli_fetch_assoc($barang)): ?><option value="<?= $b['id'] ?>" data-harga="<?= $b['harga'] ?>" data-stok="<?= $b['stok'] ?>"><?= htmlspecialchars($b['kode_barang'].' - '.$b['nama_barang']) ?> | Rp<?= number_format((float)$b['harga'],0,',','.') ?> | Stok <?= $b['stok'] ?></option><?php endwhile; ?>
</select></div>
<div class="mb-3"><label class="form-label">Jumlah Beli</label><input type="number" name="jumlah" id="jumlah" class="form-control" min="1" value="1" required></div>
<div class="alert alert-primary"><div>Harga satuan: <strong id="harga">Rp0</strong></div><div>Total: <strong id="total">Rp0</strong></div></div>
<div class="mb-3"><label class="form-label">Uang Dibayar</label><input type="number" name="bayar" id="bayar" class="form-control" min="0" step="0.01" required></div>
<button class="btn btn-success">Simpan Transaksi</button> <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
</form></div></div></div>
<script>
const pilihan=document.getElementById('barang'), jumlah=document.getElementById('jumlah');
function rupiah(n){return 'Rp'+n.toLocaleString('id-ID');}
function hitungTotal(){
 const o=pilihan.options[pilihan.selectedIndex], harga=Number(o.dataset.harga||0), stok=Number(o.dataset.stok||0);
 jumlah.max=stok;
 if(stok>0 && Number(jumlah.value)>stok) jumlah.value=stok;
 document.getElementById('harga').textContent=rupiah(harga);
 document.getElementById('total').textContent=rupiah(harga*Number(jumlah.value||0));
}
pilihan.addEventListener('change',hitungTotal); jumlah.addEventListener('input',hitungTotal); hitungTotal();
</script></body></html>
