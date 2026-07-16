<?php
include '../includes/auth_check.php';
include '../config/database.php';

// Validasi parameter (Menggunakan 'id' agar sesuai link dari dashboard)
if (!isset($_GET['id'])) {
    header("Location: ../beranda.php");
    exit;
}

$id_event = (int) $_GET['id'];
$id_user  = $_SESSION['id_user'];

// Ambil event milik user yang login
$query = "SELECT * FROM events WHERE id_event = '$id_event' AND id_user = '$id_user'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Terjadi kesalahan database: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = "Event tidak ditemukan.";
    header("Location: ../beranda.php");
    exit;
}

$event = mysqli_fetch_assoc($result);

// Ambil daftar event untuk sidebar
$query_sidebar = "SELECT id_event, nama_event FROM events WHERE id_user = '$id_user' ORDER BY id_event DESC LIMIT 5";
$result_sidebar = mysqli_query($conn, $query_sidebar);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMES - Pengaturan Event</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/pengaturan.css">
</head>

<body>

    <div class="wrapper">
        <!-- ================= SIDEBAR ================= -->
        <aside class="sidebar">
            <div class="logo">
                <img src="../assets/img/logo.png" alt="SIMES">
            </div>

            <ul class="menu">
                <li>
                    <a href="../beranda.php">
                        <i class="bi bi-house"></i> Beranda
                    </a>
                </li>
                <li class="active">
                    <a href="#">
                        <i class="bi bi-card-checklist"></i> Terdaftar <i class="bi bi-caret-down-fill arrow"></i>
                    </a>
                </li>
            </ul>

            <div class="event-list">
                <?php while ($row = mysqli_fetch_assoc($result_sidebar)): ?>
                    <a class="<?php echo ($row['id_event'] == $id_event) ? 'selected' : ''; ?>"
                        href="index.php?id_event=<?php echo $row['id_event']; ?>">
                        <?php echo htmlspecialchars($row['nama_event']); ?>
                    </a>
                <?php endwhile; ?>
            </div>

            <button class="btn-event" onclick="window.location.href='../events/create.php'">
                <i class="bi bi-plus-lg"></i> Buat Event
            </button>
        </aside>

        <!-- ================= MAIN ================= -->
        <main class="main-content">
            <!-- ================= TOPBAR ================= -->
            <header class="topbar">
                <div class="search">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Cari Event, Pengumuman, atau lainnya..." disabled>
                </div>
                <div class="profile">
                    <img src="../assets/img/profile.jpg" alt="Profile">
                    <span><?= htmlspecialchars($_SESSION['nama']); ?></span>
                </div>
            </header>

            <!-- ================= CONTENT ================= -->
            <div class="content">
                <!-- ================= HEADER ================= -->
                <div class="content-header d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h2>Pengaturan Event</h2>
                        <p>Kelola informasi utama dan pengaturan umum event</p>
                    </div>
                    <!-- Tombol Kembali -->
                    <a href="dashboard.php?id_event=<?= $id_event ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <!-- ================= FORM ================= -->
                <form action="update.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_event" value="<?= $event['id_event']; ?>">

                    <div class="row g-4 align-items-stretch">
                        <!-- FORM KIRI -->
                        <div class="col-lg-8 d-flex">
                            <div class="setting-card w-100">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Event</label>
                                        <input type="text" name="nama_event" class="form-control" value="<?= htmlspecialchars($event['nama_event']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Kategori Event</label>
                                        <input type="text" name="kategori_event" class="form-control" value="<?= htmlspecialchars($event['kategori_event']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Event</label>
                                        <input type="date" name="tanggal" class="form-control" value="<?= $event['tanggal']; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Waktu Event</label>
                                        <input type="time" name="waktu" class="form-control" value="<?= $event['waktu']; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Lokasi Event</label>
                                        <input type="text" name="lokasi" class="form-control" value="<?= htmlspecialchars($event['lokasi']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Penanggung Jawab</label>
                                        <input type="text" name="penanggung_jawab" class="form-control" value="<?= htmlspecialchars($event['penanggung_jawab']); ?>" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Status Event</label>
                                        <select name="status_event" class="form-select">
                                            <?php
                                            $statuses = ['draft' => 'Draft', 'akan datang' => 'Akan Datang', 'berlangsung' => 'Berlangsung', 'selesai' => 'Selesai'];
                                            foreach ($statuses as $val => $label):
                                            ?>
                                                <option value="<?= $val ?>" <?= ($event['status_event'] == $val) ? 'selected' : '' ?>>
                                                    <?= $label ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- POSTER KANAN -->
                        <div class="col-lg-4 d-flex">
                            <div class="setting-card w-100">
                                <h6 class="mb-3">Poster Event</h6>
                                <?php if (!empty($event['banner'])): ?>
                                    <img src="../<?= htmlspecialchars($event['banner']) ?>" class="poster-preview mb-3" alt="Poster">
                                <?php else: ?>
                                    <div class="poster-empty mb-3">Banner belum tersedia</div>
                                <?php endif; ?>
                                <label class="upload-box">
                                    <input type="file" name="banner" accept=".jpg,.jpeg,.png,.webp" hidden>
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <span>Upload Gambar Baru</span>
                                    <small>PNG, JPG maksimal 2 MB</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- ================= DESKRIPSI EVENT ================= -->
                    <div class="setting-card mt-4">
                        <label class="form-label">Deskripsi Event</label>
                        <textarea name="deskripsi" class="form-control description" rows="6" placeholder="Masukkan deskripsi event..."><?= htmlspecialchars($event['deskripsi']); ?></textarea>
                    </div>

                    <!-- ================= BUTTON ================= -->
                    <div class="action-button">
                        <button type="submit" class="btn-save">
                            <i class="bi bi-check-circle-fill"></i> Simpan Perubahan
                        </button>
                        <button type="button" class="btn-cancel" onclick="window.location.reload();">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    const inputBanner = document.querySelector('input[name="banner"]');
    const posterPreview = document.querySelector('.poster-preview');

    inputBanner.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
               
                posterPreview.src = e.target.result;
                posterPreview.style.display = 'block'; 
            }
            
            reader.readAsDataURL(this.files[0]);
        }
    });
</script>

</body>

</html>