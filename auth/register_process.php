<?php
session_start();
include '../config/database.php';

// Hanya menerima request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit;
}

// Ambil data dari form
$nama     = trim($_POST['nama'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$role = 'User';

// Validasi input kosong
if (empty($nama) || empty($email) || empty($password)) {
    $_SESSION['error'] = "Semua field wajib diisi.";
    header("Location: register.php");
    exit;
}

// Validasi format email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Format email tidak valid.";
    header("Location: register.php");
    exit;
}

// Validasi panjang password
if (strlen($password) < 6) {
    $_SESSION['error'] = "Password minimal 6 karakter.";
    header("Location: register.php");
    exit;
}

// Cek apakah email sudah terdaftar
$check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

if (!$check) {
    $_SESSION['error'] = "Terjadi kesalahan database.";
    header("Location: register.php");
    exit;
}

if (mysqli_num_rows($check) > 0) {
    $_SESSION['error'] = "Email sudah terdaftar.";
    header("Location: register.php");
    exit;
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Simpan user
$query = "INSERT INTO users (nama, email, password, role)
          VALUES ('$nama', '$email', '$hashed_password', '$role')";

$result = mysqli_query($conn, $query);

if ($result) {
    $_SESSION['success'] = "Registrasi berhasil. Silakan login.";
    header("Location: /simes/auth/login.php");
    exit;
} else {
    $_SESSION['error'] = "Registrasi gagal: " . mysqli_error($conn);
    header("Location: register.php");
    exit;
}