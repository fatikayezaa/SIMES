<?php
include '../includes/auth_check.php';
include '../config/database.php';

header('Content-Type: application/json');

if(!isset($_GET['id_peserta']) || !isset($_GET['id_event'])){
    echo json_encode(['status' => 'error', 'message' => 'Parameter tidak lengkap']);
    exit;
}

$id_peserta = (int)$_GET['id_peserta'];
$id_event = (int)$_GET['id_event'];
$id_user = $_SESSION['id_user'];

// Cek akses
$cek = mysqli_query($conn, "SELECT id_event FROM events WHERE id_event='$id_event' AND id_user='$id_user'");
if(mysqli_num_rows($cek) == 0){
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
    exit;
}

// Eksekusi hapus
$query = "DELETE FROM participants WHERE id_peserta='$id_peserta' AND id_event='$id_event'";

if(mysqli_query($conn, $query)){
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
}
?>