<?php
session_start();
include '../config/database.php';

// Hanya menerima request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

// Ambil data dari form
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validasi input kosong
if (empty($email) || empty($password)) {
    $_SESSION['error'] = "Email dan password wajib diisi.";
    header("Location: login.php");
    exit;
}

// Validasi format email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Format email tidak valid.";
    header("Location: login.php");
    exit;
}

// Cari user berdasarkan email
$query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";

$result = mysqli_query($conn, $query);

if (!$result) {
    $_SESSION['error'] = "Terjadi kesalahan database.";
    header("Location: login.php");
    exit;
}

if (mysqli_num_rows($result) === 1) {

    $user = mysqli_fetch_assoc($result);

    if (password_verify($password, $user['password'])) {

        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['nama']    = $user['nama'];
        $_SESSION['email']   = $user['email'];
        $_SESSION['role']    = $user['role'];

        header("Location: /simes/beranda.php");
        exit;

    } else {

        $_SESSION['error'] = "Password salah.";
        header("Location: login.php");
        exit;
    }

} else {

    $_SESSION['error'] = "Email tidak ditemukan.";
    header("Location: login.php");
    exit;
}