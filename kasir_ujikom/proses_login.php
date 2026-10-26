<?php
session_start();
require 'koneksi.php';
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = mysqli_prepare($koneksi, "SELECT id, username, password, nama FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama'] = $user['nama'];
    header("Location: dashboard.php");
    exit;
}
$_SESSION['error'] = "Username atau password salah.";
header("Location: login.php");
exit;
