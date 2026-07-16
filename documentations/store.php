<?php
include '../includes/auth_check.php';
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../beranda.php");
    exit;
}

$id_event   = (int) ($_POST['id_event'] ?? 0);
$judul      = mysqli_real_escape_string($conn, trim($_POST['judul'] ?? ''));
$jenis_file = mysqli_real_escape_string($conn, trim($_POST['jenis_file'] ?? ''));
$keterangan = mysqli_real_escape_string($conn, trim($_POST['keterangan'] ?? ''));

$id_user = $_SESSION['id_user'];

if ($id_event <= 0 || empty($judul) || empty($jenis_file)) {
    $_SESSION['error'] = "Data dokumentasi belum lengkap.";
    header("Location: index.php?id_event=$id_event");
    exit;
}

// Cek Kepemilikan Event
$cek = mysqli_query($conn, "SELECT id_event FROM events WHERE id_event = '$id_event' AND id_user = '$id_user'");
if (mysqli_num_rows($cek) === 0) {
    $_SESSION['error'] = "Anda tidak memiliki akses.";
    header("Location: ../beranda.php");
    exit;
}

if (!isset($_FILES['file_dokumentasi']) || $_FILES['file_dokumentasi']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = "File dokumentasi gagal diupload.";
    header("Location: index.php?id_event=$id_event");
    exit;
}

// Validasi Ukuran File (5MB untuk foto, 100MB untuk video)
$max_size = ($jenis_file === 'foto') ? 5 * 1024 * 1024 : 100 * 1024 * 1024;
if ($_FILES['file_dokumentasi']['size'] > $max_size) {
    $_SESSION['error'] = "Ukuran file terlalu besar! Maksimal " . ($jenis_file === 'foto' ? "5MB" : "100MB");
    header("Location: index.php?id_event=$id_event&tab=$jenis_file");
    exit;
}

$file_name = $_FILES['file_dokumentasi']['name'];
$tmp_name  = $_FILES['file_dokumentasi']['tmp_name'];
$ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
$allowed_ext = ($jenis_file === 'foto') ? ['jpg', 'jpeg', 'png', 'webp'] : ['mp4', 'mov', 'avi'];

if (!in_array($ext, $allowed_ext)) {
    $_SESSION['error'] = "Format file tidak sesuai.";
    header("Location: index.php?id_event=$id_event&tab=$jenis_file");
    exit;
}

$new_file_name = time() . '_' . uniqid() . '.' . $ext;
$db_file_path  = 'assets/uploads/dokumentasi/' . $new_file_name;

if (!move_uploaded_file($tmp_name, '../' . $db_file_path)) {
    die("Gagal menyimpan file ke folder upload.");
}

$query = "INSERT INTO documentations (id_event, jenis_file, judul, file_path, keterangan)
          VALUES ('$id_event', '$jenis_file', '$judul', '$db_file_path', '$keterangan')";

if (mysqli_query($conn, $query)) {
    $_SESSION['success'] = "Dokumentasi berhasil ditambahkan.";
    header("Location: index.php?id_event=$id_event&tab=$jenis_file");
    exit;
} else {
    die("Gagal menyimpan dokumentasi: " . mysqli_error($conn));
}