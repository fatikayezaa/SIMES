<?php
include '../includes/auth_check.php';
include '../config/database.php';
$id_user = $_SESSION['id_user'];
$nama = $_SESSION['nama'];
$id_event = (int)($_GET['id_event'] ?? 0);

$event = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM events WHERE id_event = '$id_event' AND id_user = '$id_user'"));
if (!$event) {
    header("Location: ../beranda.php");
    exit;
}

$result_sidebar = mysqli_query($conn, "SELECT id_event, nama_event FROM events WHERE id_user = '$id_user' ORDER BY id_event DESC");
$report = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM reports WHERE id_event = '$id_event' LIMIT 1"));

$total_peserta = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM participants WHERE id_event = '$id_event'"))['total'];
$total_hadir = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM participants WHERE id_event = '$id_event' AND status_kehadiran = 'hadir'"))['total'];
$total_tidak_hadir = $total_peserta - $total_hadir;

$total_anggaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(anggaran),0) AS total FROM budgets WHERE id_event = '$id_event'"))['total'];
$total_realisasi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(realisasi),0) AS total FROM budgets WHERE id_event = '$id_event'"))['total'];
$sisa_anggaran = $total_anggaran - $total_realisasi;

$total_foto = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM documentations WHERE id_event = '$id_event' AND jenis_file='foto'"))['total'];
$total_video = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM documentations WHERE id_event = '$id_event' AND jenis_file='video'"))['total'];

$dokumentasi = mysqli_query($conn, "
    SELECT file_path, judul, keterangan
    FROM documentations
    WHERE id_event = '$id_event'
    AND jenis_file = 'foto'
    ORDER BY id_dokumentasi DESC
    LIMIT 4
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>SIMES - Laporan Kegiatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/laporan.css">
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

        <main class="main-content">
            <header class="topbar">
                <div class="search"><i class="bi bi-search"></i><input type="text" placeholder="Cari..."></div>
                <div class="profile"><img src="../assets/img/profile.jpg" alt="Profile"><span><?php echo htmlspecialchars($nama); ?></span></div>
            </header>

            <div class="content">
                <div class="content-header">
                    <h2>Laporan Kegiatan</h2>
                    <p>Kelola informasi utama dan laporan hasil kegiatan event</p>
                </div>
                <div class="print-header">

                    <h1>LAPORAN KEGIATAN</h1>


                    <p>Sistem Informasi Manajemen Event Kampus (SIMES)</p>

                    <hr>

                    <table class="table-print-info">

                        <tr>
                            <td>Tanggal Cetak</td>
                            <td>: <?= date('d F Y') ?></td>
                        </tr>

                        <tr>
                            <td>Dicetak Oleh</td>
                            <td>: <?= ucwords(htmlspecialchars($nama)) ?></td>
                        </tr>

                    </table>

                    <hr>

                </div>

                <div class="event-card">
                    <h6>Informasi Event</h6>
                    <div class="row g-4 mt-2">
                        <div class="col-md-4">
                            <div class="info-item">
                                <i class="bi bi-calendar-event"></i>
                                <div><small>Nama Event</small>
                                    <h6><?= htmlspecialchars($event['nama_event']) ?></h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item"><i class="bi bi-geo-alt"></i>
                                <div><small>Lokasi</small>
                                    <h6><?= htmlspecialchars($event['lokasi']) ?></h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item"><i class="bi bi-person"></i>
                                <div><small>PIC</small>
                                    <h6><?= htmlspecialchars($event['penanggung_jawab']) ?></h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item"><i class="bi bi-calendar-week"></i>
                                <div><small>Tanggal</small>
                                    <h6><?= htmlspecialchars($event['tanggal'] ?? '-') ?></h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item"><i class="bi bi-tag"></i>
                                <div><small>Kategori</small>
                                    <h6><?= htmlspecialchars($event['kategori'] ?? 'Umum') ?></h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item"><i class="bi bi-info-circle"></i>
                                <div><small>Status</small>
                                    <h6><?= htmlspecialchars($event['status'] ?? 'Berlangsung') ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-2">
                    <div class="col-lg-3">
                        <div class="summary-card peserta">
                            <div class="summary-header"><i class="bi bi-people"></i><span>Peserta</span></div>
                            <div class="summary-body">
                                <div class="summary-item"><small>Total</small>
                                    <h5><?= $total_peserta ?></h5>
                                </div>
                                <div class="summary-item"><small>Hadir</small>
                                    <h5><?= $total_hadir ?></h5>
                                </div>
                                <div class="summary-item"><small>Absen</small>
                                    <h5><?= $total_tidak_hadir ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="summary-card anggaran">
                            <div class="summary-header"><i class="bi bi-wallet2"></i><span>Anggaran</span></div>
                            <div class="summary-body">
                                <div class="summary-item"><small>Total</small>
                                    <h5>Rp <?= number_format($total_anggaran, 0, ',', '.') ?></h5>
                                </div>
                                <div class="summary-item"><small>Realisasi</small>
                                    <h5>Rp <?= number_format($total_realisasi, 0, ',', '.') ?></h5>
                                </div>
                                <div class="summary-item"><small>Sisa</small>
                                    <h5>Rp <?= number_format($sisa_anggaran, 0, ',', '.') ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="summary-card dokumentasi">
                            <div class="summary-header"><i class="bi bi-camera"></i><span>Dokumentasi</span></div>
                            <div class="summary-body">
                                <div class="summary-item"><small>Foto</small>
                                    <h5><?= $total_foto ?></h5>
                                </div>
                                <div class="summary-item"><small>Video</small>
                                    <h5><?= $total_video ?></h5>
                                </div>
                                <div class="summary-item"><small>Total File</small>
                                    <h5><?= $total_foto + $total_video ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="summary-card kegiatan">
                            <div class="summary-header"><i class="bi bi-clipboard-check"></i><span>Status Laporan</span></div>
                            <div class="summary-body">
                                <div class="summary-item"><small>Status</small><span class="status-event"><?= $report ? 'Sudah Dibuat' : 'Belum Dibuat' ?></span></div>
                                <div class="summary-item"><small>Tanggal Lapor</small>
                                    <h5><?= $report ? date('d M Y', strtotime($report['tanggal_laporan'])) : '-' ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="note-card">
                    <label class="form-label fw-semibold">Catatan Kegiatan</label>
                    <div class="note-content">
                        <?php
                        if ($report && !empty($report['catatan_kegiatan'])) {
                            echo $report['catatan_kegiatan'];
                        } else {
                            echo 'Belum ada catatan.';
                        }
                        ?>
                    </div>
                </div>
                <div class="note-card documentation-section">

                    <label class="form-label fw-semibold">
                        Dokumentasi Kegiatan
                    </label>

                    <div class="row">

                        <?php while ($foto = mysqli_fetch_assoc($dokumentasi)): ?>

                            <div class="col-md-6 mb-4">

                                <div class="documentation-card">

                                    <img
                                        src="../<?= htmlspecialchars($foto['file_path']) ?>"
                                        alt="<?= htmlspecialchars($foto['judul']) ?>"
                                        class="documentation-photo">

                                    <div class="documentation-body">

                                        <h6 class="mt-3 mb-2 fw-bold">
                                            <?= htmlspecialchars($foto['judul']) ?>
                                        </h6>

                                        <p class="text-muted mb-0">
                                            <?= htmlspecialchars($foto['keterangan']) ?>
                                        </p>

                                    </div>

                                </div>

                            </div>

                        <?php endwhile; ?>

                    </div>

                </div>

                <div class="action-area mt-4">
                    <div class="d-flex gap-2">
                        <a href="/simes/dashboard.php?id_event=<?= $id_event ?>" class="btn btn-outline-secondary btn-save"><i class="bi bi-arrow-left"></i> Kembali</a>
                        <?php if ($report): ?>
                            <a href="edit.php?id_event=<?= $id_event ?>" class="btn btn-primary btn-save">Edit</a>
                            <a href="delete.php?id_event=<?= $id_event ?>" class="btn btn-danger btn-save" onclick="return confirm('Hapus?')">Hapus</a>
                        <?php else: ?>
                            <a href="create.php?id_event=<?= $id_event ?>" class="btn btn-primary btn-save">Buat</a>
                        <?php endif; ?>
                    </div>
                    <button class="btn btn-outline-primary btn-export" onclick="window.print()"><i class="bi bi-file-earmark-pdf"></i> Export PDF</button>
                </div>
            </div>
        </main>
    </div>
</body>

</html>