<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'koneksi.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) exit('ID barang tidak valid.');
$stmt = mysqli_prepare($koneksi, "SELECT * FROM barang WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$barang = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$barang) exit('Barang tidak ditemukan.');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="card border-0 shadow-sm mx-auto" style="max-width:600px">
            <div class="card-body p-4">
                <h4>Edit Barang</h4>
                <form action="update_barang.php" method="POST"><input type="hidden" name="id" value="<?= $barang['id'] ?>">
                    <div class="mb-3"><label class="form-label">Kode Barang</label><input name="kode_barang" class="form-control" value="<?= htmlspecialchars($barang['kode_barang']) ?>" maxlength="30" required></div>
                    <div class="mb-3"><label class="form-label">Nama Barang</label><input name="nama_barang" class="form-control" value="<?= htmlspecialchars($barang['nama_barang']) ?>" maxlength="100" required></div>
                    <div class="mb-3"><label class="form-label">Harga</label><input type="number" name="harga" class="form-control" value="<?= htmlspecialchars($barang['harga']) ?>" min="0" step="0.01" required></div>
                    <div class="mb-3"><label class="form-label">Stok</label><input type="number" name="stok" class="form-control" value="<?= $barang['stok'] ?>" min="0" required></div>
                    <button class="btn btn-primary">Update</button> <a href="barang.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>