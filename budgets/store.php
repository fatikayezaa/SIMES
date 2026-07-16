<?php
include '../includes/auth_check.php';
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../events/index.php");
    exit;
}

$id_event  = (int) ($_POST['id_event'] ?? 0);
$kebutuhan = trim($_POST['kebutuhan'] ?? '');
$kategori  = trim($_POST['kategori'] ?? '');
$anggaran  = (float) ($_POST['anggaran'] ?? 0);
$realisasi = (float) ($_POST['realisasi'] ?? 0);

$id_user = $_SESSION['id_user'];

if (
    $id_event <= 0 ||
    empty($kebutuhan) ||
    empty($kategori) ||
    $anggaran < 0 ||
    $realisasi < 0
) {
    $_SESSION['error'] = "Data anggaran belum lengkap atau tidak valid.";
    header("Location: create.php?id_event=$id_event");
    exit;
}

$cek = mysqli_query(
    $conn,
    "SELECT id_event FROM events WHERE id_event = '$id_event' AND id_user = '$id_user'"
);

if (!$cek || mysqli_num_rows($cek) === 0) {
    $_SESSION['error'] = "Anda tidak memiliki akses ke event tersebut.";
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

$query = "INSERT INTO budgets (id_event, kebutuhan, kategori, anggaran, realisasi, status) 
          VALUES ('$id_event', '$kebutuhan', '$kategori', '$anggaran', '$realisasi', '$status')";

$result = mysqli_query($conn, $query);

if ($result) {
    $_SESSION['success'] = "Data anggaran berhasil ditambahkan.";
    header("Location: index.php?id_event=$id_event");
    exit;
} else {
    die("Gagal menyimpan anggaran: " . mysqli_error($conn));
}
?>