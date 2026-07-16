<?php
include '../includes/auth_check.php';
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: ../beranda.php"); exit; }

$id_event = (int)$_POST['id_event'];
$catatan = trim($_POST['catatan_kegiatan']);
$id_user = $_SESSION['id_user'];

$cek = mysqli_query($conn, "SELECT id_event FROM events WHERE id_event='$id_event' AND id_user='$id_user'");
if (mysqli_num_rows($cek) > 0) {
    $query = "INSERT INTO reports (id_event, catatan_kegiatan, tanggal_laporan) VALUES ('$id_event', '$catatan', NOW())";
    mysqli_query($conn, $query);
}
header("Location: index.php?id_event=$id_event");
exit;