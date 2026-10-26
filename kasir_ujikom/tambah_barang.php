<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="card border-0 shadow-sm mx-auto" style="max-width:600px">
            <div class="card-body p-4">
                <h4>Tambah Barang</h4>
                <form action="simpan_barang.php" method="POST">
                    <div class="mb-3"><label class="form-label">Kode Barang</label><input name="kode_barang" class="form-control" required maxlength="30"></div>
                    <div class="mb-3"><label class="form-label">Nama Barang</label><input name="nama_barang" class="form-control" required maxlength="100"></div>
                    <div class="mb-3"><label class="form-label">Harga</label><input type="number" name="harga" class="form-control" min="0" step="0.01" required></div>
                    <div class="mb-3"><label class="form-label">Stok</label><input type="number" name="stok" class="form-control" min="0" required></div>
                    <button class="btn btn-primary">Simpan</button> <a href="barang.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>