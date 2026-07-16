<?php
include '../includes/auth_check.php';
include '../config/database.php';

/*
|--------------------------------------------------------------------------
| Validasi Parameter
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id_event'])) {
    header("Location: ../events/index.php");
    exit;
}

$id_event = (int) $_GET['id_event'];
$id_user  = $_SESSION['id_user'];

/*
|--------------------------------------------------------------------------
| Cek Kepemilikan Event
|--------------------------------------------------------------------------
*/

$cek = mysqli_query(
    $conn,
    "SELECT id_event
     FROM events
     WHERE id_event='$id_event'
     AND id_user='$id_user'"
);

if (!$cek) {
    die("Terjadi kesalahan database: " . mysqli_error($conn));
}

if (mysqli_num_rows($cek) === 0) {

    $_SESSION['error'] = "Anda tidak memiliki akses.";

    header("Location: ../events/index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Pastikan Laporan Ada
|--------------------------------------------------------------------------
*/

$cek_laporan = mysqli_query(
    $conn,
    "SELECT id_laporan
     FROM reports
     WHERE id_event='$id_event'
     LIMIT 1"
);

if (!$cek_laporan) {
    die("Terjadi kesalahan database: " . mysqli_error($conn));
}

if (mysqli_num_rows($cek_laporan) === 0) {

    $_SESSION['error'] = "Laporan tidak ditemukan.";

    header("Location: index.php?id_event=$id_event");
    exit;
}

/*
|--------------------------------------------------------------------------
| Hapus Laporan
|--------------------------------------------------------------------------
*/

$query = "DELETE FROM reports
          WHERE id_event='$id_event'";

$result = mysqli_query($conn, $query);

if ($result) {

    $_SESSION['success'] = "Laporan berhasil dihapus.";

    header("Location: index.php?id_event=$id_event");
    exit;

} else {

    die("Gagal menghapus laporan: " . mysqli_error($conn));

}