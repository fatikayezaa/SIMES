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

$id_event         = (int) ($_POST['id_event'] ?? 0);
$nama             = trim($_POST['nama'] ?? '');
$instansi         = trim($_POST['instansi'] ?? '');
$email            = trim($_POST['email'] ?? '');
$no_hp            = trim($_POST['no_hp'] ?? '');
$status_kehadiran = trim($_POST['status_kehadiran'] ?? '');

$id_user = $_SESSION['id_user'];

/*
|--------------------------------------------------------------------------
| Validasi Input
|--------------------------------------------------------------------------
*/

if ($id_event <= 0 || empty($nama) || empty($status_kehadiran)) {
    $_SESSION['error'] = "Field wajib belum lengkap.";
    header("Location: create.php?id_event=$id_event");
    exit;
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Format email tidak valid.";
    header("Location: create.php?id_event=$id_event");
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
| Simpan Peserta
|--------------------------------------------------------------------------
*/

$query = "INSERT INTO participants
          (id_event, nama, instansi, email, no_hp, status_kehadiran)
          VALUES
          ('$id_event', '$nama', '$instansi', '$email', '$no_hp', '$status_kehadiran')";

$result = mysqli_query($conn, $query);

if ($result) {

    $_SESSION['success'] = "Peserta berhasil ditambahkan.";

    header("Location: index.php?id_event=$id_event");
    exit;

} else {

    die("Gagal menyimpan peserta: " . mysqli_error($conn));

}