<?php
include '../includes/auth_check.php';
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../beranda.php");
    exit;
}

$id_dokumentasi = (int) ($_POST['id_dokumentasi'] ?? 0);
$id_event       = (int) ($_POST['id_event'] ?? 0);
$judul          = mysqli_real_escape_string($conn, trim($_POST['judul'] ?? ''));
$jenis_file     = mysqli_real_escape_string($conn, trim($_POST['jenis_file'] ?? ''));
$keterangan     = mysqli_real_escape_string($conn, trim($_POST['keterangan'] ?? ''));
$file_lama      = $_POST['file_lama'] ?? '';

$id_user = $_SESSION['id_user'];

// 1. Validasi Input Kosong
if ($id_dokumentasi <= 0 || $id_event <= 0 || empty($judul) || empty($jenis_file)) {
    $_SESSION['error'] = "Data dokumentasi belum lengkap.";
    header("Location: index.php?id_event=$id_event");
    exit;
}

// 2. Cek akses dan Kepemilikan Event
$cek = mysqli_query($conn, "SELECT id_event FROM events WHERE id_event='$id_event' AND id_user='$id_user'");
if (!$cek) {
    die("Database Error: " . mysqli_error($conn));
}
if (mysqli_num_rows($cek) === 0) {
    $_SESSION['error'] = "Anda tidak memiliki akses.";
    header("Location: ../beranda.php");
    exit;
}

$db_file_path = $file_lama;

// 3. Proses Upload Jika Ada File Baru
if (isset($_FILES['file_dokumentasi']) && $_FILES['file_dokumentasi']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['file_dokumentasi']['name'], PATHINFO_EXTENSION));
    $allowed_ext = ($jenis_file === 'foto') ? ['jpg', 'jpeg', 'png', 'webp'] : ['mp4', 'mov', 'avi'];

    if (!in_array($ext, $allowed_ext)) {
        $_SESSION['error'] = "Format file tidak sesuai.";
        header("Location: edit.php?id_dokumentasi=$id_dokumentasi&id_event=$id_event");
        exit;
    }

    $max_size = ($jenis_file === 'foto') ? 5 * 1024 * 1024 : 100 * 1024 * 1024;
    if ($_FILES['file_dokumentasi']['size'] > $max_size) {
        $_SESSION['error'] = "Ukuran file baru terlalu besar!";
        header("Location: edit.php?id_dokumentasi=$id_dokumentasi&id_event=$id_event");
        exit;
    }

    $new_file_name = time() . "_" . uniqid() . "." . $ext;
    $db_file_path  = "assets/uploads/dokumentasi/" . $new_file_name;

    if (move_uploaded_file($_FILES['file_dokumentasi']['tmp_name'], "../" . $db_file_path)) {
        if (file_exists("../" . $file_lama)) {
            if (!unlink("../" . $file_lama)) {
                die("Gagal menghapus file lama.");
            }
        }
    } else {
        die("Gagal upload file baru.");
    }
}

// 4. Update Database
$query = "UPDATE documentations SET 
          judul='$judul', 
          jenis_file='$jenis_file', 
          file_path='$db_file_path', 
          keterangan='$keterangan'
          WHERE id_dokumentasi='$id_dokumentasi' AND id_event='$id_event'";

if (mysqli_query($conn, $query)) {
    $_SESSION['success'] = "Dokumentasi berhasil diperbarui.";
    header("Location: index.php?id_event=$id_event&tab=$jenis_file");
    exit;
} else {
    die("Gagal mengupdate dokumentasi : " . mysqli_error($conn));
}