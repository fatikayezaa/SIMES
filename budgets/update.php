<?php
include '../includes/auth_check.php';
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../events/index.php");
    exit;
}

$id_anggaran = (int) ($_POST['id_anggaran'] ?? 0);
$id_event    = (int) $_POST['id_event'] ?? 0;
$kebutuhan   = trim($_POST['kebutuhan'] ?? '');
$kategori    = trim($_POST['kategori'] ?? '');
$anggaran    = (float) ($_POST['anggaran'] ?? 0);
$realisasi   = (float) ($_POST['realisasi'] ?? 0);

$id_user = $_SESSION['id_user'];

if (
    $id_anggaran <= 0 ||
    $id_event <= 0 ||
    empty($kebutuhan) ||
    empty($kategori) ||
    $anggaran < 0 ||
    $realisasi < 0
) {
    $_SESSION['error'] = "Data anggaran belum lengkap atau tidak valid.";
    header("Location: edit.php?id_anggaran=$id_anggaran&id_event=$id_event");
    exit;
}

$cek = mysqli_query(
    $conn,
    "SELECT id_event FROM events WHERE id_event = '$id_event' AND id_user = '$id_user'"
);

if (!$cek || mysqli_num_rows($cek) === 0) {
    $_SESSION['error'] = "Akses ditolak.";
    header("Location: ../events/index.php");
    exit;
}

if ($realisasi == 0) {
    $status = 'belum terealisasi';
} elseif ($realisasi < $anggaran) {
    $status = 'dalam anggaran';
} elseif ($realisasi == $anggaran) {
    $status = 'sesuai';
} else {
    $status = 'melebihi';
}

$query = "UPDATE budgets 
          SET kebutuhan = '$kebutuhan', kategori = '$kategori', anggaran = '$anggaran', realisasi = '$realisasi', status = '$status' 
          WHERE id_anggaran = '$id_anggaran' AND id_event = '$id_event'";

$result = mysqli_query($conn, $query);

if ($result) {
    $_SESSION['success'] = "Data anggaran berhasil diperbarui.";
    header("Location: index.php?id_event=$id_event");
    exit;
} else {
    die("Gagal mengupdate data anggaran: " . mysqli_error($conn));
}
?>