<?php
include '../includes/auth_check.php';
include '../config/database.php';

if (!isset($_GET['id_dokumentasi']) || !isset($_GET['id_event'])) {
    header("Location: ../beranda.php");
    exit;
}

$id_dokumentasi = (int) $_GET['id_dokumentasi'];
$id_event       = (int) $_GET['id_event'];
$id_user        = $_SESSION['id_user'];

$cek = mysqli_query(
    $conn,
    "SELECT id_event FROM events WHERE id_event='$id_event' AND id_user='$id_user'"
);

if (mysqli_num_rows($cek) === 0) {
    $_SESSION['error'] = "Anda tidak memiliki akses.";
    header("Location: ../beranda.php");
    exit;
}

$query = "SELECT file_path FROM documentations WHERE id_dokumentasi='$id_dokumentasi' AND id_event='$id_event'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = "Dokumentasi tidak ditemukan.";
    header("Location: index.php?id_event=$id_event");
    exit;
}

$data = mysqli_fetch_assoc($result);
$file = "../" . $data['file_path'];

if (file_exists($file)) {
    unlink($file);
}

$query = "DELETE FROM documentations WHERE id_dokumentasi='$id_dokumentasi' AND id_event='$id_event'";

if (mysqli_query($conn, $query)) {
    $_SESSION['success'] = "Dokumentasi berhasil dihapus.";
    header("Location: index.php?id_event=$id_event");
    exit;
} else {
    die("Gagal menghapus dokumentasi : " . mysqli_error($conn));
}