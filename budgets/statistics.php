<?php
include '../includes/auth_check.php';
include '../config/database.php';

$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'];

if (!isset($_GET['id_event'])) {
    header("Location: ../beranda.php");
    exit;
}
$id_event = (int) $_GET['id_event'];

/* Cek Akses Event */
$event_query = "SELECT * FROM events WHERE id_event = '$id_event' AND id_user = '$id_user'";
$event_result = mysqli_query($conn, $event_query);

if (!$event_result || mysqli_num_rows($event_result) === 0) {
    header("Location: ../beranda.php");
    exit;
}
$event = mysqli_fetch_assoc($event_result);

/* List Sidebar */
$query_sidebar = "SELECT id_event, nama_event FROM events WHERE id_user = '$id_user' ORDER BY id_event DESC";
$result_sidebar = mysqli_query($conn, $query_sidebar);

/* Perhitungan Total Ringkasan Atas */
$total_anggaran_query = mysqli_query($conn, "SELECT COALESCE(SUM(anggaran),0) AS total FROM budgets WHERE id_event='$id_event'");
$total_anggaran = mysqli_fetch_assoc($total_anggaran_query)['total'];

$total_realisasi_query = mysqli_query($conn, "SELECT COALESCE(SUM(realisasi),0) AS total FROM budgets WHERE id_event='$id_event'");
$total_realisasi = mysqli_fetch_assoc($total_realisasi_query)['total'];

$sisa_anggaran = $total_anggaran - $total_realisasi;
$persentase_terpakai = ($total_anggaran > 0) ? round(($total_realisasi / $total_anggaran) * 100, 1) : 0;

/* Ambil Data Kategori untuk Chart.js & Tabel Ringkasan */
$chart_query = "SELECT kategori, SUM(anggaran) as total_plan, SUM(realisasi) as total_used 
                FROM budgets 
                WHERE id_event = '$id_event' 
                GROUP BY kategori";
$chart_result = mysqli_query($conn, $chart_query);

$categories = [];
$plans = [];
$used_funds = [];
$table_rows = [];

while($c_row = mysqli_fetch_assoc($chart_result)) {
    $categories[] = $c_row['kategori'];
    $plans[] = (float) $c_row['total_plan'];
    $used_funds[] = (float) $c_row['total_used'];
    $table_rows[] = $c_row;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMES - Statik Anggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/anggaran.css?v=2">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="logo">
            <img src="../assets/img/logo.png" alt="SIMES">
        </div>
        <ul class="menu">
            <li><a href="../beranda.php"><i class="bi bi-house"></i> Beranda</a></li>
            <li class="active"><a href="#"><i class="bi bi-card-checklist"></i> Terdaftar <i class="bi bi-caret-down-fill arrow"></i></a></li>
        </ul>
        <div class="event-list">
            <?php mysqli_data_seek($result_sidebar, 0); while ($row = mysqli_fetch_assoc($result_sidebar)): ?>
                <a class="<?php echo ($row['id_event'] == $id_event) ? 'selected' : ''; ?>" href="statistics.php?id_event=<?php echo $row['id_event']; ?>">
                    <?php echo htmlspecialchars($row['nama_event']); ?>
                </a>
            <?php endwhile; ?>
        </div>
        <button class="btn-event" onclick="window.location.href='../events/create.php'"><i class="bi bi-plus-lg"></i> Buat Event</button>
    </aside>

    <!-- MAIN -->
    <main class="main-content">
        <header class="topbar">
            <div class="search"><i class="bi bi-search"></i><input type="text" placeholder="Cari Event..."></div>
            <div class="profile">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nama_user); ?>&background=222A45&color=fff&bold=true" alt="Profile">
                <span><?php echo htmlspecialchars($nama_user); ?></span>
            </div>
        </header>

        <div class="content">
            <div class="content-header d-flex justify-content-between align-items-center">
                <div>
                    <h2>Anggaran</h2>
                    <p>Statistik penggunaan anggaran: <strong><?php echo htmlspecialchars($event['nama_event']); ?></strong></p>
                </div>
                <a href="../dashboard.php?id_event=<?php echo $id_event; ?>" class="btn btn-outline-secondary btn-sm rounded-3 w-auto px-3">
                    <i class="bi bi-arrow-left"></i> Dashboard Event
                </a>
            </div>

            <!-- CARD STATISTIK -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3"><div class="stat-card"><div class="stat-icon purple"><i class="bi bi-wallet2"></i></div><div><span class="stat-title">Total Anggaran</span><h5>Rp <?php echo number_format($total_anggaran, 0, ',', '.'); ?></h5><small>100% Dana</small></div></div></div>
                <div class="col-lg-3"><div class="stat-card"><div class="stat-icon orange"><i class="bi bi-cash-stack"></i></div><div><span class="stat-title">Total Pengeluaran</span><h5>Rp <?php echo number_format($total_realisasi, 0, ',', '.'); ?></h5><small><?php echo $persentase_terpakai; ?>% Terpakai</small></div></div></div>
                <div class="col-lg-3"><div class="stat-card"><div class="stat-icon green"><i class="bi bi-piggy-bank-fill"></i></div><div><span class="stat-title">Sisa Anggaran</span><h5>Rp <?php echo number_format($sisa_anggaran, 0, ',', '.'); ?></h5><small><?php echo (100 - $persentase_terpakai) > 0 ? 100 - $persentase_terpakai : 0; ?>% Tersisa</small></div></div></div>
                <div class="col-lg-3"><div class="stat-card"><div class="stat-icon blue"><i class="bi bi-graph-up-arrow"></i></div><div><span class="stat-title">Persentase</span><h5><?php echo $persentase_terpakai; ?>%</h5><small>Penggunaan Dana</small></div></div></div>
            </div>

            <!-- TABS -->
            <div class="budget-card">
                <div class="budget-tabs">
                    <a href="index.php?id_event=<?php echo $id_event; ?>" class="tab">Kebutuhan Anggaran</a>
                    <a href="statistics.php?id_event=<?php echo $id_event; ?>" class="tab active">Statik Anggaran</a>
                </div>

                <!-- CHART CONTAINER -->
                <div class="chart-container">
                    <canvas id="budgetChart"></canvas>
                </div>

                <!-- TABLE SUMMARY -->
                <div class="table-responsive mt-4">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th class="text-end">Total Anggaran</th>
                                <th class="text-end">Pengeluaran</th>
                                <th class="text-end">Sisa</th>
                                <th class="text-center">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($table_rows) > 0): ?>
                                <?php foreach ($table_rows as $row): 
                                    $row_sisa = $row['total_plan'] - $row['total_used'];
                                    $row_pct = ($row['total_plan'] > 0) ? round(($row['total_used'] / $row['total_plan']) * 100) : 0;
                                ?>
                                    <tr>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($row['kategori']); ?></td>
                                        <td class="text-end">Rp <?php echo number_format($row['total_plan'], 0, ',', '.'); ?></td>
                                        <td class="text-end">Rp <?php echo number_format($row['total_used'], 0, ',', '.'); ?></td>
                                        <td class="text-end">Rp <?php echo number_format($row_sisa, 0, ',', '.'); ?></td>
                                        <td class="text-center fw-bold text-primary"><?php echo $row_pct; ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-muted">Belum ada data grafik untuk ditampilkan.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- INJEKSI DATA KE CHART JS -->
<script>
const ctx = document.getElementById('budgetChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($categories); ?>,
        datasets: [
            {
                label: 'Anggaran Rencana',
                data: <?php echo json_encode($plans); ?>,
                backgroundColor: '#7C4DFF',
                borderRadius: 8
            },
            {
                label: 'Realisasi Terpakai',
                data: <?php echo json_encode($used_funds); ?>,
                backgroundColor: '#2455FF',
                borderRadius: 8
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>