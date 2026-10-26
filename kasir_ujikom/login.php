<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login KasirKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-sm border-0 mx-auto login-card">
            <div class="card-body p-4">
                <h3 class="text-center mb-2">KasirKu</h3>
                <p class="text-center text-secondary">Silakan login untuk melanjutkan</p>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form action="proses_login.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100">Login</button>
                </form>
                <p class="text-center text-secondary small mt-3 mb-0">UJIKOM - PHP Native &amp; MySQL</p>
            </div>
        </div>
    </div>
</body>

</html>