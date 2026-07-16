<?php
include '../includes/auth_check.php';
include '../config/database.php';

$id_user = $_SESSION['id_user'];

$id_event = (int) $_POST['id_event'];

$nama_event       = trim($_POST['nama_event']);
$kategori_event   = trim($_POST['kategori_event']);
$lokasi           = trim($_POST['lokasi']);
$tanggal          = trim($_POST['tanggal']);
$waktu            = trim($_POST['waktu']);
$penanggung_jawab = trim($_POST['penanggung_jawab']);
$deskripsi        = trim($_POST['deskripsi']);
$status_event     = trim($_POST['status_event']);

/*
|--------------------------------------------------------------------------
| Ambil data event lama
|--------------------------------------------------------------------------
*/

$query_event = "SELECT * FROM events
                WHERE id_event='$id_event'
                AND id_user='$id_user'";

$result_event = mysqli_query($conn, $query_event);

if (mysqli_num_rows($result_event) == 0) {
    die("Event tidak ditemukan.");
}

$event = mysqli_fetch_assoc($result_event);

$path_banner = $event['banner'];

/*
|--------------------------------------------------------------------------
| Jika user upload banner baru
|--------------------------------------------------------------------------
*/

if (isset($_FILES['banner']) && $_FILES['banner']['error'] == 0) {

    $nama_file = $_FILES['banner']['name'];
    $tmp_file  = $_FILES['banner']['tmp_name'];

    $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ekstensi, $allowed)) {
        die("Format banner harus JPG, JPEG, PNG, atau WEBP.");
    }

    $nama_baru = time() . "_" . uniqid() . "." . $ekstensi;

    // FIX: Arahkan ke folder assets/uploads/banners/ milik server
    $target_file = "../assets/uploads/banners/" . $nama_baru;

    // FIX: Tulis relative path bersih untuk database
    $path_banner = "assets/uploads/banners/" . $nama_baru;

    if (!move_uploaded_file($tmp_file, $target_file)) {
        die("Gagal upload banner.");
    }

    // Hapus banner lama
    if (!empty($event['banner']) && file_exists("../" . $event['banner'])) {
        unlink("../" . $event['banner']);
    }
}

/*
|--------------------------------------------------------------------------
| Update Database
|--------------------------------------------------------------------------
*/

$query = "UPDATE events SET

nama_event='$nama_event',
kategori_event='$kategori_event',
lokasi='$lokasi',
tanggal='$tanggal',
waktu='$waktu',
penanggung_jawab='$penanggung_jawab',
deskripsi='$deskripsi',
banner='$path_banner',
status_event='$status_event'

WHERE id_event='$id_event'
AND id_user='$id_user'";

if (mysqli_query($conn, $query)) {
    $_SESSION['success'] = "Event berhasil diperbarui.";
    header("Location: ../beranda.php");
    exit;
} else {
    die("Gagal update event : " . mysqli_error($conn));
}