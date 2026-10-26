# UJIKOM

- riwayat : 

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'koneksi.php';
$data = mysqli_query($koneksi, "SELECT p.id,p.tanggal,p.total,p.bayar,p.kembalian,u.nama FROM penjualan p JOIN users u ON p.user_id=u.id ORDER BY p.id DESC");
$berhasil = filter_input(INPUT_GET, 'berhasil', FILTER_VALIDATE_INT);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Riwayat Transaksi</h3>
            <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
        </div>
        <?php if ($berhasil): ?>
            <div class="alert alert-success">Transaksi #<?= $berhasil ?> berhasil disimpan.</div>
        <?php endif; ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kasir</th>
                                <th>Total</th>
                                <th>Bayar</th>
                                <th>Kembalian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($r = mysqli_fetch_assoc($data)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($r['tanggal']) ?></td>
                                    <td><?= htmlspecialchars($r['nama']) ?></td>
                                    <td>Rp<?= number_format((float)$r['total'], 0, ',', '.') ?></td>
                                    <td>Rp<?= number_format((float)$r['bayar'], 0, ',', '.') ?></td>
                                    <td>Rp<?= number_format((float)$r['kembalian'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if (mysqli_num_rows($data) === 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada transaksi.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <a href="transaksi.php" class="btn btn-success">Transaksi Baru</a>
            </div>
        </div>
    </div>
</body>

</html>

- simpan_transaksi : 

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'koneksi.php';
$barang_id = filter_input(INPUT_POST, 'barang_id', FILTER_VALIDATE_INT);
$jumlah = filter_input(INPUT_POST, 'jumlah', FILTER_VALIDATE_INT);
$bayar = filter_input(INPUT_POST, 'bayar', FILTER_VALIDATE_FLOAT);
$user_id = (int)$_SESSION['user_id'];
if (!$barang_id || $barang_id < 1 || !$jumlah || $jumlah < 1 || $bayar === false || $bayar === null || $bayar < 0) exit('Input transaksi tidak valid.');

mysqli_begin_transaction($koneksi);
try {
    $stmt = mysqli_prepare($koneksi, "SELECT harga,stok FROM barang WHERE id=? FOR UPDATE");
    mysqli_stmt_bind_param($stmt, "i", $barang_id);
    mysqli_stmt_execute($stmt);
    $barang = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$barang) throw new Exception("Barang tidak ditemukan.");
    if ($jumlah > (int)$barang['stok']) throw new Exception("Stok barang tidak mencukupi.");
    $harga = (float)$barang['harga'];
    $total = round($harga * $jumlah, 2);
    if ($bayar < $total) throw new Exception("Uang pembayaran kurang.");
    $kembalian = round($bayar - $total, 2);

    $stmt = mysqli_prepare($koneksi, "INSERT INTO penjualan (user_id,total,bayar,kembalian) VALUES (?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "iddd", $user_id, $total, $bayar, $kembalian);
    mysqli_stmt_execute($stmt);
    $penjualan_id = mysqli_insert_id($koneksi);

    $subtotal = $total;
    $stmt = mysqli_prepare($koneksi, "INSERT INTO detail_penjualan (penjualan_id,barang_id,harga,jumlah,subtotal) VALUES (?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "iidid", $penjualan_id, $barang_id, $harga, $jumlah, $subtotal);
    mysqli_stmt_execute($stmt);

    $stmt = mysqli_prepare($koneksi, "UPDATE barang SET stok=stok-? WHERE id=? AND stok>=?");
    mysqli_stmt_bind_param($stmt, "iii", $jumlah, $barang_id, $jumlah);
    mysqli_stmt_execute($stmt);
    if (mysqli_stmt_affected_rows($stmt) !== 1) throw new Exception("Gagal memperbarui stok.");
    mysqli_commit($koneksi);
    header("Location: riwayat.php?berhasil=" . $penjualan_id);
    exit;
} catch (Throwable $e) {
    mysqli_rollback($koneksi);
    http_response_code(400);
    echo '<!DOCTYPE html><html lang="id"><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Transaksi Gagal</title><body style="font-family:Arial;padding:24px"><h3>Transaksi gagal</h3><p>' . htmlspecialchars($e->getMessage()) . '</p><a href="transaksi.php">Kembali ke transaksi</a></body></html>';
}

- simpan_barang : 
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'koneksi.php';
$kode = trim($_POST['kode_barang'] ?? '');
$nama = trim($_POST['nama_barang'] ?? '');
$harga = filter_input(INPUT_POST, 'harga', FILTER_VALIDATE_FLOAT);
$stok = filter_input(INPUT_POST, 'stok', FILTER_VALIDATE_INT);
if ($kode === '' || $nama === '' || $harga === false || $harga === null || $harga < 0 || $stok === false || $stok === null || $stok < 0) exit('Data tidak valid. <a href="tambah_barang.php">Kembali</a>');
$stmt = mysqli_prepare($koneksi, "INSERT INTO barang (kode_barang,nama_barang,harga,stok) VALUES (?,?,?,?)");
mysqli_stmt_bind_param($stmt, "ssdi", $kode, $nama, $harga, $stok);
if (mysqli_stmt_execute($stmt)) {
    header("Location: barang.php");
    exit;
}
if (mysqli_errno($koneksi) === 1062) exit('Kode barang sudah digunakan. <a href="tambah_barang.php">Kembali</a>');
exit('Gagal menyimpan barang.');

simpan_transaksi : 
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'koneksi.php';
$barang_id = filter_input(INPUT_POST, 'barang_id', FILTER_VALIDATE_INT);
$jumlah = filter_input(INPUT_POST, 'jumlah', FILTER_VALIDATE_INT);
$bayar = filter_input(INPUT_POST, 'bayar', FILTER_VALIDATE_FLOAT);
$user_id = (int)$_SESSION['user_id'];
if (!$barang_id || $barang_id < 1 || !$jumlah || $jumlah < 1 || $bayar === false || $bayar === null || $bayar < 0) exit('Input transaksi tidak valid.');

mysqli_begin_transaction($koneksi);
try {
    $stmt = mysqli_prepare($koneksi, "SELECT harga,stok FROM barang WHERE id=? FOR UPDATE");
    mysqli_stmt_bind_param($stmt, "i", $barang_id);
    mysqli_stmt_execute($stmt);
    $barang = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$barang) throw new Exception("Barang tidak ditemukan.");
    if ($jumlah > (int)$barang['stok']) throw new Exception("Stok barang tidak mencukupi.");
    $harga = (float)$barang['harga'];
    $total = round($harga * $jumlah, 2);
    if ($bayar < $total) throw new Exception("Uang pembayaran kurang.");
    $kembalian = round($bayar - $total, 2);

    $stmt = mysqli_prepare($koneksi, "INSERT INTO penjualan (user_id,total,bayar,kembalian) VALUES (?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "iddd", $user_id, $total, $bayar, $kembalian);
    mysqli_stmt_execute($stmt);
    $penjualan_id = mysqli_insert_id($koneksi);

    $subtotal = $total;
    $stmt = mysqli_prepare($koneksi, "INSERT INTO detail_penjualan (penjualan_id,barang_id,harga,jumlah,subtotal) VALUES (?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "iidid", $penjualan_id, $barang_id, $harga, $jumlah, $subtotal);
    mysqli_stmt_execute($stmt);

    $stmt = mysqli_prepare($koneksi, "UPDATE barang SET stok=stok-? WHERE id=? AND stok>=?");
    mysqli_stmt_bind_param($stmt, "iii", $jumlah, $barang_id, $jumlah);
    mysqli_stmt_execute($stmt);
    if (mysqli_stmt_affected_rows($stmt) !== 1) throw new Exception("Gagal memperbarui stok.");
    mysqli_commit($koneksi);
    header("Location: riwayat.php?berhasil=" . $penjualan_id);
    exit;
} catch (Throwable $e) {
    mysqli_rollback($koneksi);
    http_response_code(400);
    echo '<!DOCTYPE html><html lang="id"><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Transaksi Gagal</title><body style="font-family:Arial;padding:24px"><h3>Transaksi gagal</h3><p>' . htmlspecialchars($e->getMessage()) . '</p><a href="transaksi.php">Kembali ke transaksi</a></body></html>';
}

- transaksi : 
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'koneksi.php';
$barang = mysqli_query($koneksi, "SELECT id,kode_barang,nama_barang,harga,stok FROM barang WHERE stok>0 ORDER BY nama_barang");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transaksi Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="card border-0 shadow-sm mx-auto" style="max-width:650px">
            <div class="card-body p-4">
                <h4>Transaksi Penjualan</h4>
                <p class="text-secondary">Pilih satu barang dan masukkan jumlah pembelian.</p>
                <form action="simpan_transaksi.php" method="POST">
                    <div class="mb-3"><label class="form-label">Pilih Barang</label><select name="barang_id" id="barang" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php while ($b = mysqli_fetch_assoc($barang)): ?><option value="<?= $b['id'] ?>" data-harga="<?= $b['harga'] ?>" data-stok="<?= $b['stok'] ?>"><?= htmlspecialchars($b['kode_barang'] . ' - ' . $b['nama_barang']) ?> | Rp<?= number_format((float)$b['harga'], 0, ',', '.') ?> | Stok <?= $b['stok'] ?></option><?php endwhile; ?>
                        </select></div>
                    <div class="mb-3"><label class="form-label">Jumlah Beli</label><input type="number" name="jumlah" id="jumlah" class="form-control" min="1" value="1" required></div>
                    <div class="alert alert-primary">
                        <div>Harga satuan: <strong id="harga">Rp0</strong></div>
                        <div>Total: <strong id="total">Rp0</strong></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Uang Dibayar</label><input type="number" name="bayar" id="bayar" class="form-control" min="0" step="0.01" required></div>
                    <button class="btn btn-success">Simpan Transaksi</button> <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
    <script>
        const pilihan = document.getElementById('barang'),
            jumlah = document.getElementById('jumlah');

        function rupiah(n) {
            return 'Rp' + n.toLocaleString('id-ID');
        }

        function hitungTotal() {
            const o = pilihan.options[pilihan.selectedIndex],
                harga = Number(o.dataset.harga || 0),
                stok = Number(o.dataset.stok || 0);
            jumlah.max = stok;
            if (stok > 0 && Number(jumlah.value) > stok) jumlah.value = stok;
            document.getElementById('harga').textContent = rupiah(harga);
            document.getElementById('total').textContent = rupiah(harga * Number(jumlah.value || 0));
        }
        pilihan.addEventListener('change', hitungTotal);
        jumlah.addEventListener('input', hitungTotal);
        hitungTotal();
    </script>
</body>

</html>

- Barang :
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'koneksi.php';
$cari = trim($_GET['cari'] ?? '');
$keyword = "%{$cari}%";
$stmt = mysqli_prepare($koneksi, "SELECT * FROM barang WHERE kode_barang LIKE ? OR nama_barang LIKE ? ORDER BY id DESC");
mysqli_stmt_bind_param($stmt, "ss", $keyword, $keyword);
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Data Barang</h3><a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between gap-2 mb-3"><a href="tambah_barang.php" class="btn btn-primary">+ Tambah Barang</a>
                    <form method="GET" class="d-flex gap-2"><input type="text" name="cari" class="form-control" placeholder="Cari kode/nama..." value="<?= htmlspecialchars($cari) ?>"><button class="btn btn-outline-primary">Cari</button><a href="barang.php" class="btn btn-outline-secondary">Reset</a></form>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($row = mysqli_fetch_assoc($data)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['kode_barang']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                    <td>Rp<?= number_format((float)$row['harga'], 0, ',', '.') ?></td>
                                    <td><?= $row['stok'] ?></td>
                                    <td class="text-nowrap">
                                        <a class="btn btn-warning btn-sm" href="edit_barang.php?id=<?= $row['id'] ?>">Edit</a>
                                        <form action="hapus_barang.php" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus barang ini?')"><input type="hidden" name="id" value="<?= $row['id'] ?>"><button class="btn btn-danger btn-sm">Hapus</button></form>
                                    </td>
                                </tr>
                            <?php endwhile;
                            if (mysqli_num_rows($data) === 0): ?><tr>
                                    <td colspan="6" class="text-center">Data tidak ditemukan.</td>
                                </tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
