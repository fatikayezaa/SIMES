<?php
include '../includes/auth_check.php';
include '../config/database.php';

/*
|--------------------------------------------------------------------------
| Hanya menerima request POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../events/index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil Data Form
|--------------------------------------------------------------------------
*/

$id_laporan = (int) ($_POST['id_laporan'] ?? 0);
$id_event = (int) ($_POST['id_event'] ?? 0);
$catatan_kegiatan = trim($_POST['catatan_kegiatan'] ?? '');

$id_user = $_SESSION['id_user'];

/*
|--------------------------------------------------------------------------
| Validasi Input
|--------------------------------------------------------------------------
*/

if (
    $id_laporan <= 0 ||
    $id_event <= 0 ||
    empty($catatan_kegiatan)
) {
    $_SESSION['error'] = "Data laporan belum lengkap.";
    header("Location: edit.php?id_event=$id_event");
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
| Pastikan Laporan Milik Event Ini
|--------------------------------------------------------------------------
*/

$cek_laporan = mysqli_query(
    $conn,
    "SELECT id_laporan
     FROM reports
     WHERE id_laporan = '$id_laporan'
     AND id_event = '$id_event'"
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
| Update Laporan
|--------------------------------------------------------------------------
*/

$query = "UPDATE reports
          SET catatan_kegiatan = '$catatan_kegiatan'
          WHERE id_laporan = '$id_laporan'
          AND id_event = '$id_event'";

$result = mysqli_query($conn, $query);

if ($result) {

    $_SESSION['success'] = "Laporan berhasil diperbarui.";

    header("Location: index.php?id_event=$id_event");
    exit;

} else {

    die("Gagal mengupdate laporan: " . mysqli_error($conn));

}