<?php
include '../includes/auth_check.php';
include '../config/database.php';

if (!isset($_GET['id_event'])) {
    header("Location: ../events/index.php");
    exit;
}

$id_event = (int) $_GET['id_event'];
$id_user  = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'];

$cek = mysqli_query($conn, "SELECT id_event, nama_event FROM events WHERE id_event = '$id_event' AND id_user = '$id_user'");
if (!$cek || mysqli_num_rows($cek) === 0) {
    $_SESSION['error'] = "Event tidak ditemukan atau Anda tidak memiliki akses.";
    header("Location: ../events/index.php");
    exit;
}
$event = mysqli_fetch_assoc($cek);
$query_sidebar = "SELECT id_event, nama_event FROM events WHERE id_user = '$id_user' ORDER BY id_event DESC";
$result_sidebar = mysqli_query($conn, $query_sidebar);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMES - Tambah Anggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/anggaran.css?v=<?php echo time(); ?>">
</head>

<body>

<div class="wrapper">
    <aside class="sidebar">
        <div class="logo"><img src="../assets/img/logo.png" alt="SIMES"></div>
        <ul class="menu">
            <li><a href="../beranda.php"><i class="bi bi-house"></i> Beranda</a></li>
            <li class="active"><a href="#"><i class="bi bi-card-checklist"></i> Terdaftar <i class="bi bi-caret-down-fill arrow"></i></a></li>
        </ul>
        <div class="event-list">
            <?php while ($row = mysqli_fetch_assoc($result_sidebar)): ?>
                <a class="<?php echo ($row['id_event'] == $id_event) ? 'selected' : ''; ?>" href="index.php?id_event=<?php echo $row['id_event']; ?>">
                    <?php echo htmlspecialchars($row['nama_event']); ?>
                </a>
            <?php endwhile; ?>
        </div>
        <button class="btn-event" onclick="window.location.href='../events/create.php'"><i class="bi bi-plus-lg"></i> Buat Event</button>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="search"><i class="bi bi-search"></i><input type="text" placeholder="Cari..."></div>
            <div class="profile">
                <img src="../assets/img/profile.jpg" alt="Profile" style="width: 45px !important; height: 45px !important; border-radius: 50% !important; object-fit: cover !important;">
                <span><?php echo htmlspecialchars($nama_user); ?></span>
            </div>
        </header>

        <div class="content">
            <div class="content-header d-flex justify-content-between align-items-center">
                <div>
                    <h2>Tambah Item Anggaran</h2>
                    <p>Event: <strong><?php echo htmlspecialchars($event['nama_event']); ?></strong></p>
                </div>
                <a href="index.php?id_event=<?php echo $id_event; ?>" class="btn btn-outline-secondary btn-sm rounded-3 w-auto px-3">← Kembali</a>
            </div>

            <div class="card border-0 shadow-sm p-4" style="border-radius: 18px; background: #ffffff;">
                <form action="store.php" method="POST" onsubmit="return validateForm(this);">
                    <input type="hidden" name="id_event" value="<?php echo $id_event; ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary">Nama Kebutuhan <span class="text-danger">*</span></label>
                            <input type="text" name="kebutuhan" class="form-control px-3 py-2 rounded-3" placeholder="Contoh: Banner Kegiatan" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary">Kategori <span class="text-danger">*</span></label>
                            <!-- Kategori Dropdown Terkunci -->
                            <select class="form-select px-3 py-2 rounded-3" name="kategori" required>
                                <option value="" disabled selected>-- Pilih Kategori --</option>
                                <option value="Konsumsi">Konsumsi</option>
                                <option value="Logistik">Logistik</option>
                                <option value="Publikasi">Publikasi</option>
                                <option value="Transportasi">Transportasi</option>
                                <option value="Peralatan">Peralatan</option>
                                <option value="Honorarium">Honorarium</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-medium text-secondary">Rencana Anggaran (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="anggaran" class="form-control px-3 py-2 rounded-3" min="0" placeholder="0" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-medium text-secondary">Realisasi Terpakai (Rp)</label>
                            <input type="number" name="realisasi" class="form-control px-3 py-2 rounded-3" min="0" value="0">
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-2">
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-medium" style="background: #2455FF;">Simpan Anggaran</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
function validateForm(form) {
    var anggaran = parseFloat(form.anggaran.value) || 0;
    var realisasi = parseFloat(form.realisasi.value) || 0;
    if (realisasi > anggaran) {
        return confirm("Warning: Realisasi terpakai melebihi anggaran rencana. Tetap simpan data?");
    }
    return true;
}
</script>
</body>
</html>