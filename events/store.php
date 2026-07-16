<?php
include '../includes/auth_check.php';
include '../config/database.php';

$id_user = $_SESSION['id_user'];

$nama_event        = trim($_POST['nama_event']);
$kategori_event    = trim($_POST['kategori_event']);
$lokasi            = trim($_POST['lokasi']);
$tanggal           = trim($_POST['tanggal']);
$waktu             = trim($_POST['waktu']);
$penanggung_jawab  = trim($_POST['penanggung_jawab']);
$deskripsi         = trim($_POST['deskripsi']);
$status_event      = trim($_POST['status_event']);
$banner = $_FILES['banner'];

$nama_file      = $banner['name'];
$tmp_file       = $banner['tmp_name'];
$error_file     = $banner['error'];

$folder_upload = "../assets/uploads/banners/";

if (!is_dir($folder_upload)) {
    mkdir($folder_upload, 0777, true);
}

if (
    empty($nama_event) || empty($kategori_event) || empty($lokasi) ||
    empty($tanggal) || empty($waktu) || empty($penanggung_jawab) || empty($status_event)
) {
    die("Semua field wajib diisi.");
}

if ($error_file !== 0) {
    die("Banner event wajib diupload.");
}

$ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

$allowed = ['jpg', 'jpeg', 'png', 'webp'];

if (!in_array($ekstensi, $allowed)) {
    die("Format gambar harus JPG, JPEG, PNG, atau WEBP.");
}

$nama_baru = time() . "_" . uniqid() . "." . $ekstensi;

// Lokasi upload file di server
$target_file = $folder_upload . $nama_baru;

// FIX: Lokasi relative path bersih dari root project untuk disimpan ke database
$path_banner = "assets/uploads/banners/" . $nama_baru;

if (!move_uploaded_file($tmp_file, $target_file)) {
    die("Gagal mengupload banner.");
}

$query = "INSERT INTO events
(
id_user,
nama_event,
kategori_event,
lokasi,
tanggal,
waktu,
penanggung_jawab,
deskripsi,
banner,
status_event
)
VALUES
(
'$id_user',
'$nama_event',
'$kategori_event',
'$lokasi',
'$tanggal',
'$waktu',
'$penanggung_jawab',
'$deskripsi',
'$path_banner',
'$status_event'
)";

if (mysqli_query($conn, $query)) {
    $_SESSION['success'] = "Event berhasil dibuat.";
    header("Location: ../beranda.php");
    exit;
} else {
    die("Gagal menyimpan event: " . mysqli_error($conn));
}