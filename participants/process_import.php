<?php
session_start();
include '../includes/auth_check.php';
include '../config/database.php';

// Proteksi akses
if (!isset($_POST['btn_import']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../beranda.php");
    exit;
}

$id_event = (int) $_POST['id_event'];
$id_user  = $_SESSION['id_user'];

// Validasi kepemilikan event
$cek = mysqli_query($conn, "SELECT id_event FROM events WHERE id_event = '$id_event' AND id_user = '$id_user'");
if (mysqli_num_rows($cek) === 0) {
    $_SESSION['error'] = "Akses ditolak.";
    header("Location: ../beranda.php");
    exit;
}

if (isset($_FILES['file_csv']) && $_FILES['file_csv']['error'] == 0) {
    $filename = $_FILES['file_csv']['tmp_name'];
    $file = fopen($filename, "r");
    
    // Lewati baris pertama (header)
    fgetcsv($file);
    
    $sukses_insert = 0;
    $gagal_insert = 0;
    
    while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {
        // Skip jika nama (kolom pertama) kosong
        if (empty($row[0])) continue;
        
        $nama     = mysqli_real_escape_string($conn, trim($row[0]));
        $instansi = mysqli_real_escape_string($conn, trim($row[1] ?? ''));
        $email    = mysqli_real_escape_string($conn, trim($row[2] ?? ''));
        $no_hp    = mysqli_real_escape_string($conn, trim($row[3] ?? ''));
        
        $query = "INSERT INTO participants (id_event, nama, instansi, email, no_hp, status_kehadiran) 
                  VALUES ('$id_event', '$nama', '$instansi', '$email', '$no_hp', 'belum hadir')";
        
        if (mysqli_query($conn, $query)) {
            $sukses_insert++;
        } else {
            $gagal_insert++;
        }
    }
    fclose($file);
    
    if ($sukses_insert > 0) {
        $_SESSION['success'] = "Berhasil mengimport $sukses_insert peserta. " . ($gagal_insert > 0 ? "$gagal_insert gagal." : "");
    } else {
        $_SESSION['error'] = "Gagal mengimport data. Pastikan format CSV sesuai.";
    }
    
    header("Location: index.php?id_event=$id_event");
    exit;
    
} else {
    $_SESSION['error'] = "Gagal mengunggah file.";
    header("Location: index.php?id_event=$id_event");
    exit;
}