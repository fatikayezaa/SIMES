<?php
include '../includes/auth_check.php';
include '../config/database.php';

// Pastikan parameter id ada
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_event = (int) $_GET['id'];
$id_user  = $_SESSION['id_user'];

// Ambil event yang benar-benar dimiliki user yang login
$query = "SELECT events.*, users.nama AS nama_user
          FROM events
          JOIN users ON events.id_user = users.id_user
          WHERE events.id_event = '$id_event'
          AND events.id_user = '$id_user'";

$result = mysqli_query($conn, $query);

// Cek apakah query gagal
if (!$result) {
    die("Terjadi kesalahan database: " . mysqli_error($conn));
}

// Kalau event tidak ditemukan / bukan miliknya
if (mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = "Event tidak ditemukan atau Anda tidak memiliki akses.";
    header("Location: index.php");
    exit;
}

$event = mysqli_fetch_assoc($result);

include '../includes/header.php';
?>

<h2>Detail Event</h2>

<p><strong>Nama Event:</strong> <?php echo htmlspecialchars($event['nama_event']); ?></p>

<p><strong>Kategori:</strong> <?php echo htmlspecialchars($event['kategori_event']); ?></p>

<p><strong>Lokasi:</strong> <?php echo htmlspecialchars($event['lokasi']); ?></p>

<p><strong>Tanggal:</strong> <?php echo htmlspecialchars($event['tanggal']); ?></p>

<p><strong>Waktu:</strong> <?php echo htmlspecialchars($event['waktu']); ?></p>

<p><strong>Penanggung Jawab:</strong> <?php echo htmlspecialchars($event['penanggung_jawab']); ?></p>

<p><strong>Status:</strong> <?php echo htmlspecialchars($event['status_event']); ?></p>

<p><strong>Dibuat oleh:</strong> <?php echo htmlspecialchars($event['nama_user']); ?></p>

<p>
    <strong>Deskripsi:</strong><br>
    <?php echo nl2br(htmlspecialchars($event['deskripsi'])); ?>
</p>

<hr>

<h3>Menu Event</h3>

<ul>

    <li>
        <a href="../participants/index.php?id_event=<?php echo $event['id_event']; ?>">
            Kelola Peserta
        </a>
    </li>

    <li>
        <a href="../budgets/index.php?id_event=<?php echo $event['id_event']; ?>">
            Kelola Anggaran
        </a>
    </li>

    <li>
        <a href="../documentations/index.php?id_event=<?php echo $event['id_event']; ?>">
            Kelola Dokumentasi
        </a>
    </li>

    <li>
        <a href="../reports/index.php?id_event=<?php echo $event['id_event']; ?>">
            Laporan Kegiatan
        </a>
    </li>

</ul>

<br>

<a href="index.php">
    ← Kembali ke Daftar Event
</a>

<?php
include '../includes/footer.php';
?>