<?php
include '../includes/auth_check.php';
include '../config/database.php';

if (
    !isset($_GET['id_anggaran']) ||
    !isset($_GET['id_event'])
) {
    header("Location: ../events/index.php");
    exit;
}

$id_anggaran = (int) $_GET['id_anggaran'];
$id_event    = (int) $_GET['id_event'];
$id_user     = $_SESSION['id_user'];

$cek = mysqli_query(
    $conn,
    "SELECT id_event FROM events WHERE id_event='$id_event' AND id_user='$id_user'"
);

if (!$cek || mysqli_num_rows($cek) === 0) {
    $_SESSION['error'] = "Event tidak ditemukan.";
    header("Location: ../events/index.php");
    exit;
}

$query = "DELETE FROM budgets WHERE id_anggaran='$id_anggaran' AND id_event='$id_event'";
$result = mysqli_query($conn, $query);

if ($result) {
    $_SESSION['success'] = "Data anggaran berhasil dihapus.";
    header("Location: index.php?id_event=$id_event");
    exit;
} else {
    die("Gagal menghapus data anggaran: " . mysqli_error($conn));
}
?>