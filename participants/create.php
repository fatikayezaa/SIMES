<?php
include '../includes/auth_check.php';
include '../config/database.php';

if (!isset($_GET['id_event'])) {
    header("Location: ../beranda.php");
    exit;
}

$id_event = (int) $_GET['id_event'];
$id_user  = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'];

$cek = mysqli_query($conn, "SELECT id_event, nama_event FROM events WHERE id_event = '$id_event' AND id_user = '$id_user'");

if (!$cek || mysqli_num_rows($cek) === 0) {
    header("Location: ../beranda.php");
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
    <title>SIMES - Tambah Peserta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/peserta.css?v=3">
</head>

<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="logo">
            <img src="../assets/img/logo.png" alt="Logo">
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

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <div class="search">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Cari Event, Pengumuman, atau lainnya...">
            </div>

            <div class="profile">
                <img src="../assets/img/profile.jpg" alt="Profile">
                <span><?php echo htmlspecialchars($nama_user); ?></span>
            </div>
        </header>

        <!-- FORM CONTENT -->
        <div class="content">
            
            <div class="content-header d-flex justify-content-between align-items-center">
                <div>
                    <h2>Tambah Peserta</h2>
                    <p>Event: <strong><?php echo htmlspecialchars($event['nama_event']); ?></strong></p>
                </div>
                <a href="index.php?id_event=<?php echo $id_event; ?>" class="btn btn-outline-secondary btn-sm rounded-3">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger role="alert">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm p-4" style="border-radius: 18px; background: #ffffff;">
                <form action="store.php" method="POST">
                    <input type="hidden" name="id_event" value="<?php echo $id_event; ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary">Nama Peserta <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control px-3 py-2 rounded-3" placeholder="Masukkan nama lengkap" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary">Instansi</label>
                            <input type="text" name="instansi" class="form-control px-3 py-2 rounded-3" placeholder="Masukkan nama instansi/kampus">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary">Email</label>
                            <input type="email" name="email" class="form-control px-3 py-2 rounded-3" placeholder="contoh@email.com">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary">No HP</label>
                            <input type="text" name="no_hp" class="form-control px-3 py-2 rounded-3" placeholder="08xxxxxxxxxx">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium text-secondary">Status Kehadiran <span class="text-danger">*</span></label>
                        <select name="status_kehadiran" class="form-select px-3 py-2 rounded-3" required>
                            <option value="belum hadir" selected>Belum Hadir</option>
                            <option value="hadir">Hadir</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-2">
                        <button type="reset" class="btn btn-light px-4 py-2 rounded-3 fw-medium">Reset</button>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-medium" style="background: #2455FF;">
                            <i class="bi bi-save me-1"></i> Simpan Peserta
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>