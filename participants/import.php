<?php
include '../includes/auth_check.php';
include '../config/database.php';

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
$event_query = "SELECT id_event, nama_event FROM events WHERE id_event = '$id_event' AND id_user = '$id_user'";
$event_result = mysqli_query($conn, $event_query);

if (mysqli_num_rows($event_result) === 0) {
    $_SESSION['error'] = "Event tidak ditemukan atau Anda tidak memiliki akses.";
    header("Location: ../events/index.php");
    exit;
}

$event = mysqli_fetch_assoc($event_result);

include '../includes/header.php';
?>

<h2>Import Peserta via CSV</h2>
<p><strong>Event:</strong> <?php echo htmlspecialchars($event['nama_event']); ?></p>

<!-- Tampilkan Pesan Error/Sukses jika ada -->
<?php if (isset($_SESSION['error'])): ?>
    <div style="color: red; margin-bottom: 15px;">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div style="background: #f8f9fa; padding: 20px; border: 1px solid #ddd; border-radius: 8px; max-width: 500px;">
    <form action="process_import.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_event" value="<?php echo $id_event; ?>">
        
        <label><strong>Pilih File CSV:</strong></label><br>
        <input type="file" name="file_csv" accept=".csv" required><br><br>
        
        <small style="color: gray; display: block; margin-bottom: 15px;">
            * Pastikan format kolom sesuai dengan template (nama, instansi, email, no_hp).<br>
            * Jika belum punya formatnya, silakan <a href="template.csv" download>Download Template Di Sini</a>.
        </small>
        
        <button type="submit" name="btn_import">
            Mulai Import Data
        </button>
    </form>
</div>

<br>
<a href="index.php?id_event=<?php echo $id_event; ?>">
    ← Kembali ke Daftar Peserta
</a>

<?php
include '../includes/footer.php';
?>