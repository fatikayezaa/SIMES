<?php
include '../includes/auth_check.php';
include '../config/database.php';

/*
|--------------------------------------------------------------------------
| Validasi ID Event
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id_event'])) {
    header("Location: ../beranda.php");
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
     WHERE id_event = '$id_event'
     AND id_user = '$id_user'"
);

if (!$cek) {
    die("Terjadi kesalahan database: " . mysqli_error($conn));
}

if (mysqli_num_rows($cek) === 0) {
    $_SESSION['error'] = "Event tidak ditemukan atau Anda tidak memiliki akses.";
    header("Location: ../beranda.php");
    exit;
}

include '../includes/header.php';
?>

<h2>Upload Dokumentasi</h2>

<form action="store.php" method="POST" enctype="multipart/form-data">

    <input type="hidden" name="id_event" value="<?php echo $id_event; ?>">

    <label>Judul Dokumentasi:</label><br>
    <input type="text" name="judul" required>
    <br><br>

    <label>Jenis File:</label><br>
    <select name="jenis_file" required>
        <option value="foto">Foto</option>
        <option value="video">Video</option>
    </select>
    <br><br>

    <label>Upload File:</label><br>
    <input type="file" name="file_dokumentasi" required>
    <br><br>

    <label>Keterangan:</label><br>
    <textarea name="keterangan" rows="4" cols="50"></textarea>
    <br><br>

    <button type="submit">
        Simpan Dokumentasi
    </button>

</form>

<br>
<a href="index.php?id_event=<?php echo $id_event; ?>">
    ← Kembali ke Daftar Dokumentasi
</a>

<?php
include '../includes/footer.php';
?>