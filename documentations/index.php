<?php
include '../includes/auth_check.php';
include '../config/database.php';

// Validasi ID Event
if (!isset($_GET['id_event'])) {
    header("Location: ../beranda.php");
    exit;
}
$id_event = (int) $_GET['id_event'];
$id_user  = $_SESSION['id_user'];

// Cek Akses Event
$event = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM events WHERE id_event = '$id_event' AND id_user = '$id_user'"));
if (!$event) {
    header("Location: ../beranda.php");
    exit;
}

// Sidebar
$result_sidebar = mysqli_query($conn, "SELECT id_event, nama_event FROM events WHERE id_user = '$id_user' ORDER BY id_event DESC");

// Dokumentasi Data dengan proteksi tab
$tab = (isset($_GET['tab']) && in_array($_GET['tab'], ['foto', 'video'])) ? $_GET['tab'] : 'foto';
$search = trim($_GET['search'] ?? '');
$docs_query = "SELECT * FROM documentations WHERE id_event = '$id_event' AND jenis_file = '$tab'";
if ($search != '') $docs_query .= " AND judul LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'";
$docs_result = mysqli_query($conn, $docs_query . " ORDER BY id_dokumentasi DESC");

// Statistik
$stat = mysqli_fetch_all(mysqli_query($conn, "SELECT jenis_file, COUNT(*) as total FROM documentations WHERE id_event='$id_event' GROUP BY jenis_file"), MYSQLI_ASSOC);
$total_foto = 0;
$total_video = 0;
foreach ($stat as $s) {
    if ($s['jenis_file'] == 'foto') $total_foto = $s['total'];
    if ($s['jenis_file'] == 'video') $total_video = $s['total'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMES - Dokumentasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/simes/assets/css/beranda.css">
    <link rel="stylesheet" href="/simes/assets/css/dokumentasi.css">
</head>

<body>

    <div class="wrapper">
        <aside class="sidebar">
            <div class="logo">
                <img src="/simes/assets/img/logo.png" alt="SIMES">
            </div>

            <ul class="menu">
                <li>
                    <a href="/simes/beranda.php">
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
                    <a class="<?= ($row['id_event'] == $id_event) ? 'selected' : ''; ?>" href="index.php?id_event=<?= $row['id_event']; ?>">
                        <?= htmlspecialchars($row['nama_event']); ?>
                    </a>
                <?php endwhile; ?>
            </div>

            <button class="btn-event" onclick="window.location.href='/simes/events/create.php'">
                <i class="bi bi-plus-lg"></i> Buat Event
            </button>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="search"><i class="bi bi-search"></i><input type="text" placeholder="Cari..."></div>
                <div class="profile"><img src="/simes/assets/img/profile.jpg" alt="Profile"><span><?= htmlspecialchars($_SESSION['nama']); ?></span></div>
            </header>

            <div class="content">
                <!-- Notifikasi Sukses/Error -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['success']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <div class="content-header">
                    <div>
                        <h2>Dokumentasi : <?= htmlspecialchars($event['nama_event']); ?></h2>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="/simes/dashboard.php?id_event=<?= $id_event ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                        <button class="upload-btn" data-bs-toggle="modal" data-bs-target="#uploadModal"><i class="bi bi-upload"></i> Upload</button>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <div class="stat-card">
                            <div class="stat-icon purple"><i class="bi bi-camera-fill"></i></div>
                            <div><span class="stat-title">Total Foto</span>
                                <h4><?= $total_foto ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="stat-card">
                            <div class="stat-icon green"><i class="bi bi-camera-reels-fill"></i></div>
                            <div><span class="stat-title">Total Video</span>
                                <h4><?= $total_video ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gallery-card">
                    <div class="gallery-header">
                        <div class="tabs">
                            <a href="index.php?id_event=<?= $id_event ?>&tab=foto" class="tab <?= ($tab === 'foto') ? 'active' : '' ?>">Foto</a>
                            <a href="index.php?id_event=<?= $id_event ?>&tab=video" class="tab <?= ($tab === 'video') ? 'active' : '' ?>">Video</a>
                        </div>
                    </div>
                    <div class="gallery-grid">
                        <?php if (mysqli_num_rows($docs_result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($docs_result)): ?>
                                <div class="gallery-item">
                                    <?php if ($row['jenis_file'] === 'foto'): ?>
                                        <a href="/simes/<?= htmlspecialchars($row['file_path']); ?>" target="_blank">
                                            <img src="/simes/<?= htmlspecialchars($row['file_path']); ?>" alt="Foto">
                                        </a>
                                    <?php else: ?>
                                        <a href="/simes/<?= htmlspecialchars($row['file_path']); ?>" target="_blank">
                                            <video style="width:100%; height:180px; object-fit:cover;" muted>
                                                <source src="/simes/<?= htmlspecialchars($row['file_path']); ?>" type="video/mp4">
                                            </video>
                                        </a>
                                    <?php endif; ?>

                                    <div class="gallery-info">
                                        <div>
                                            <h6><?= htmlspecialchars($row['judul']); ?></h6>
                                            <p><?= !empty($row['keterangan']) ? htmlspecialchars($row['keterangan']) : '-'; ?></p>
                                        </div>
                                        <div class="mt-2 d-flex gap-2">
                                            <a href="edit.php?id_dokumentasi=<?= $row['id_dokumentasi']; ?>&id_event=<?= $id_event; ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="delete.php?id_dokumentasi=<?= $row['id_dokumentasi']; ?>&id_event=<?= $id_event; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus dokumentasi ini?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-4 col-12 text-muted">Belum ada file <?= $tab ?>.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="store.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="id_event" value="<?= $id_event ?>">
                        <div class="mb-3"><label>Judul</label><input type="text" name="judul" class="form-control" required></div>
                        <div class="mb-3"><label>Jenis</label><select name="jenis_file" class="form-select">
                                <option value="foto">Foto</option>
                                <option value="video">Video</option>
                            </select></div>
                        <div class="mb-3"><label>File</label><input type="file" name="file_dokumentasi" class="form-control" required></div>
                        <div class="mb-3">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan deskripsi singkat..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-primary">Upload</button></div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>