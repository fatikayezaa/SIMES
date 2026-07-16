<?php
include '../includes/auth_check.php';
include '../config/database.php';

/*
|--------------------------------------------------------------------------
| Validasi Parameter
|--------------------------------------------------------------------------
*/

if (
    !isset($_GET['id_peserta']) ||
    !isset($_GET['id_event']) ||
    !isset($_GET['status'])
) {
    header("Location: ../events/index.php");
    exit;
}

$id_peserta = (int) $_GET['id_peserta'];
$id_event   = (int) $_GET['id_event'];
$status     = trim($_GET['status']);

$id_user = $_SESSION['id_user'];

/*
|--------------------------------------------------------------------------
| Validasi Status
|--------------------------------------------------------------------------
*/

if ($status !== 'hadir' && $status !== 'belum hadir') {
    $_SESSION['error'] = "Status kehadiran tidak valid.";
    header("Location: index.php?id_event=$id_event");
    exit;
}

/*
|--------------------------------------------------------------------------
| Cek Kepemilikan Event
|--------------------------------------------------------------------------
*/

$cek = mysqli_query(
    $conn,
    "SELECT id_event
     FROM events
     WHERE id_event = '$id_event'
     AND id_user = '$id_user'"
);

if (!$cek) {
    die("Terjadi kesalahan database: " . mysqli_error($conn));
}

if (mysqli_num_rows($cek) === 0) {
    $_SESSION['error'] = "Anda tidak memiliki akses ke event tersebut.";
    header("Location: ../events/index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Update Status Kehadiran
|--------------------------------------------------------------------------
*/

$query = "UPDATE participants
          SET status_kehadiran = '$status'
          WHERE id_peserta = '$id_peserta'
          AND id_event = '$id_event'";

$result = mysqli_query($conn, $query);

if ($result) {

    $_SESSION['success'] = "Status kehadiran berhasil diperbarui.";

    header("Location: index.php?id_event=$id_event");
    exit;

} else {

    die("Gagal mengubah status peserta: " . mysqli_error($conn));

}