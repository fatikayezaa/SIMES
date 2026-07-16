<?php
include '../includes/auth_check.php';
include '../config/database.php';

// Validasi parameter
if (!isset($_GET['id'])) {
    header("Location: ../beranda.php");
    exit;
}

$id_event = (int) $_GET['id'];
$id_user  = $_SESSION['id_user'];

/*
|--------------------------------------------------------------------------
| Ambil data event
|--------------------------------------------------------------------------
*/

$query_event = "SELECT *
                FROM events
                WHERE id_event = '$id_event'
                AND id_user = '$id_user'";

$result_event = mysqli_query($conn, $query_event);

if (!$result_event) {
    die("Terjadi kesalahan database: " . mysqli_error($conn));
}

if (mysqli_num_rows($result_event) === 0) {
    die("Event tidak ditemukan.");
}

$event = mysqli_fetch_assoc($result_event);

/*
|--------------------------------------------------------------------------
| Hapus banner jika ada
|--------------------------------------------------------------------------
*/

if (!empty($event['banner']) && file_exists("../" . $event['banner'])) {
    unlink("../" . $event['banner']);
}

/*
|--------------------------------------------------------------------------
| Hapus event
|--------------------------------------------------------------------------
*/

$query = "DELETE FROM events
          WHERE id_event = '$id_event'
          AND id_user = '$id_user'";

if (mysqli_query($conn, $query)) {

    $_SESSION['success'] = "Event berhasil dihapus.";

    header("Location: ../beranda.php");
    exit;

} else {

    die("Gagal menghapus event: " . mysqli_error($conn));

}