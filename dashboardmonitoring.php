<?php
include 'includes/auth_check.php';
include 'config/database.php';

$id_user = $_SESSION['id_user'];
$nama    = $_SESSION['nama'];

if (!isset($_GET['id_event'])) {
    header("Location: beranda.php");
    exit;
}

$id_event = (int) $_GET['id_event'];

/* Data Event Utama */
$query = "SELECT * FROM events WHERE id_event = '$id_event' AND id_user = '$id_user'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    header("Location: beranda.php");
    exit;
}
$event = mysqli_fetch_assoc($result);

/* List Sidebar */
$query_sidebar = "SELECT id_event, nama_event FROM events WHERE id_user = '$id_user' ORDER BY id_event DESC";
$result_sidebar = mysqli_query($conn, $query_sidebar);

/* 1. Hitung Total Peserta */
$q_peserta = mysqli_query($conn, "SELECT COUNT(*) AS total FROM participants WHERE id_event = '$id_event'");
$total_peserta = mysqli_fetch_assoc($q_peserta)['total'];

/* 2. Hitung Total Anggaran & Realisasi */
$q_budget = mysqli_query($conn, "SELECT SUM(anggaran) AS total_ang, SUM(realisasi) AS total_real FROM budgets WHERE id_event = '$id_event'");
$r_budget = mysqli_fetch_assoc($q_budget);
$total_anggaran = (float) ($r_budget['total_ang'] ?? 0);
$total_realisasi = (float) ($r_budget['total_real'] ?? 0);

$sisa_anggaran = $total_anggaran - $total_realisasi;
$persen_anggaran = ($total_anggaran > 0) ? round(($total_realisasi / $total_anggaran) * 100, 1) : 0;

/* 3. Hitung Dokumentasi */
$q_docs = mysqli_query($conn, "SELECT COUNT(*) AS total FROM documentations WHERE id_event = '$id_event'");
$total_docs = mysqli_fetch_assoc($q_docs)['total'];

/* 4. Cek Laporan (Query ringan) */
$q_report = mysqli_query($conn, "SELECT 1 FROM reports WHERE id_event = '$id_event'");
$laporan_ada = mysqli_num_rows($q_report) > 0;

/* 5. Data Chart */
$kategori = [];
$anggaran_data = [];
$realisasi_data = [];
$q_chart = mysqli_query($conn, "SELECT kategori, SUM(anggaran) as total_ang, SUM(realisasi) as total_real FROM budgets WHERE id_event = '$id_event' GROUP BY kategori ORDER BY kategori ASC");
while ($row = mysqli_fetch_assoc($q_chart)) {
    $kategori[] = $row['kategori'];
    $anggaran_data[] = (float) $row['total_ang'];
    $realisasi_data[] = (float) $row['total_real'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>SIMES - Dashboard Monitoring</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboardmonitoring.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <div class="wrapper">
        <aside class="sidebar">
            <div class="logo">
                <img src="assets/img/logo.png" alt="SIMES">
            </div>

            <ul class="menu">
                <li>
                    <a href="beranda.php">
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
                <?php
                mysqli_data_seek($result_sidebar, 0);
                while ($row = mysqli_fetch_assoc($result_sidebar)):
                ?>
                    <a class="<?php echo ($row['id_event'] == $id_event) ? 'selected' : ''; ?>"
                        href="dashboard.php?id_event=<?php echo $row['id_event']; ?>">
                        <?php echo htmlspecialchars($row['nama_event']); ?>
                    </a>
                <?php endwhile; ?>
            </div>

            <button class="btn-event" onclick="window.location.href='events/create.php'">
                <i class="bi bi-plus-lg"></i> Buat Event
            </button>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="search"><i class="bi bi-search"></i><input type="text" placeholder="Cari..."></div>
                <div class="profile"><img src="assets/img/profile.jpg" alt="Profile" style="width:45px;height:45px;border-radius:50%;object-fit:cover;"><span><?php echo htmlspecialchars($nama); ?></span></div>
            </header>

            <div class="content">
                <div class="content-header">
                    <h2>Dashboard Monitoring</h2>
                    <p>Daftar monitoring event</p>
                </div>
                <!--Tombol Kembali -->
                <a href="/simes/dashboard.php?id_event=<?= $id_event; ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="event-info-card">
                <div class="event-title">
                    <div class="event-icon"><i class="bi bi-calendar-event"></i></div>
                    <div>
                        <small>Informasi Event</small>
                        <h5><?php echo htmlspecialchars($event['nama_event']); ?></h5>
                        <?php
                        $s = strtolower(trim($event['status_event']));
                        $sc = match ($s) {
                            'akan datang' => 'status-coming',
                            'berlangsung' => 'status-running',
                            'selesai' => 'status-finished',
                            default => 'status-draft'
                        };
                        ?>
                        <span class="status <?= $sc ?> text-capitalize"><?php echo htmlspecialchars($event['status_event']); ?></span>
                    </div>
                </div>
                <div class="event-detail"><i class="bi bi-calendar3"></i>
                    <div><small>Tanggal</small>
                        <h6><?php echo date('d F Y', strtotime($event['tanggal'])); ?></h6><span><?php echo htmlspecialchars($event['waktu']); ?> WIB</span>
                    </div>
                </div>
                <div class="event-detail"><i class="bi bi-geo-alt"></i>
                    <div><small>Lokasi</small>
                        <h6><?php echo htmlspecialchars($event['lokasi']); ?></h6><span>Area Kegiatan</span>
                    </div>
                </div>
                <div class="event-detail"><i class="bi bi-person"></i>
                    <div><small>PIC</small>
                        <h6><?php echo htmlspecialchars($event['penanggung_jawab']); ?></h6><span>Penanggung Jawab</span>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-2">
                <div class="col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="bi bi-people-fill"></i></div>
                        <div><small>Peserta</small>
                            <h5><?php echo $total_peserta; ?></h5><span>orang terdaftar</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon purple"><i class="bi bi-wallet2"></i></div>
                        <div><small>Total Anggaran</small>
                            <h5>Rp <?php echo number_format($total_anggaran, 0, ',', '.'); ?></h5><span>Dana Direncanakan</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon orange"><i class="bi bi-cash-stack"></i></div>
                        <div><small>Total Pengeluaran</small>
                            <h5>Rp <?php echo number_format($total_realisasi, 0, ',', '.'); ?></h5><span>Dana telah digunakan</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="bi bi-camera-fill"></i></div>
                        <div><small>Dokumentasi</small>
                            <h5><?php echo $total_docs; ?></h5><span>Dokumentasi Tersimpan</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-8">
                    <div class="monitor-card">
                        <div class="card-header-custom">
                            <h5>Perbandingan Anggaran dan Realisasi per Kategori</h5>
                        </div>
                        <div class="chart-box">
                            <?php if (!empty($kategori)): ?>
                                <canvas id="budgetChart"></canvas>
                            <?php else: ?>
                                <div class="text-center py-5 text-muted">Belum ada data anggaran</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="monitor-card">
                        <h5 class="mb-4">Ringkasan Monitoring</h5>

                        <!-- Status Event -->
                        <div class="summary-item">
                            <div><i class="bi bi-square-fill text-success me-2"></i> Status Event</div>
                            <span class="badge bg-success"><?= htmlspecialchars($event['status_event']) ?></span>
                        </div>

                        <!-- Peserta -->
                        <div class="summary-item">
                            <div><i class="bi bi-square-fill text-primary me-2"></i> Peserta</div>
                            <strong><?= $total_peserta ?> Orang</strong>
                        </div>

                        <!-- Anggaran -->
                        <div class="summary-item">
                            <div><i class="bi bi-square-fill text-warning me-2"></i> Anggaran</div>
                            <strong><?= $persen_anggaran ?>%</strong>
                        </div>

                        <!-- BARIS DOKUMENTASI BARU -->
                        <div class="summary-item">
                            <div><i class="bi bi-square-fill text-primary me-2"></i> Dokumentasi</div>
                            <strong><?= $total_docs ?> Foto</strong>
                        </div>

                        <!-- Laporan -->
                        <div class="summary-item">
                            <div><i class="bi bi-square-fill text-danger me-2"></i> Laporan</div>
                            <span class="badge bg-<?= $laporan_ada ? 'success' : 'danger' ?>">
                                <?= $laporan_ada ? 'Sudah Dibuat' : 'Belum Dibuat' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </main>
    </div>

    <script>
        new Chart(document.getElementById('budgetChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($kategori); ?>,
                datasets: [{
                        label: 'Anggaran',
                        data: <?= json_encode($anggaran_data); ?>,
                        backgroundColor: '#7C4DFF',
                        borderRadius: 6,
                        barPercentage: 0.7,
                        categoryPercentage: 0.6
                    },
                    {
                        label: 'Realisasi',
                        data: <?= json_encode($realisasi_data); ?>,
                        backgroundColor: '#2455FF',
                        borderRadius: 6,
                        barPercentage: 0.7,
                        categoryPercentage: 0.6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 10
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => 'Rp ' + Number(v).toLocaleString('id-ID')
                        }
                    }
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>