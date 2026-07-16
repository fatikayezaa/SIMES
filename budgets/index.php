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

/* Kalkulasi Ringkasan Data Riil */
$total_anggaran_query = mysqli_query($conn, "SELECT COALESCE(SUM(anggaran),0) AS total FROM budgets WHERE id_event='$id_event'");
$total_anggaran = mysqli_fetch_assoc($total_anggaran_query)['total'];

$total_realisasi_query = mysqli_query($conn, "SELECT COALESCE(SUM(realisasi),0) AS total FROM budgets WHERE id_event='$id_event'");
$total_realisasi = mysqli_fetch_assoc($total_realisasi_query)['total'];

$sisa_anggaran = $total_anggaran - $total_realisasi;
$persentase_terpakai = ($total_anggaran > 0) ? round(($total_realisasi / $total_anggaran) * 100, 1) : 0;

/* Fitur Pencarian Dipersempit (Kebutuhan, Kategori, Status) */
$search = trim($_GET['search'] ?? '');
$budgets_query = "SELECT * FROM budgets WHERE id_event = '$id_event'";
if ($search != '') {
    $search = mysqli_real_escape_string($conn, $search);
    $budgets_query .= " AND (kebutuhan LIKE '%$search%' 
                        OR kategori LIKE '%$search%' 
                        OR status LIKE '%$search%')";
}
$budgets_query .= " ORDER BY id_anggaran DESC";
$budgets_result = mysqli_query($conn, $budgets_query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMES - Kebutuhan Anggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/anggaran.css?v=<?php echo time(); ?>">
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

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <div class="search">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Cari Event, Pengumuman, atau lainnya...">
            </div>

            <div class="profile">
                <img src="../assets/img/profile.jpg" alt="Profile" style="width: 45px !important; height: 45px !important; border-radius: 50% !important; object-fit: cover !important; display: block !important;">
                <span><?php echo htmlspecialchars($nama_user); ?></span>
            </div>
        </header>

        <!-- CONTENT CONTAINER -->
        <div class="content">
            
            <div class="content-header d-flex justify-content-between align-items-center">
                <div>
                    <h2>Anggaran</h2>
                    <p>Daftar kebutuhan anggaran kegiatan: <strong><?php echo htmlspecialchars($event['nama_event']); ?></strong></p>
                </div>
                <a href="../dashboard.php?id_event=<?php echo $id_event; ?>" class="btn btn-outline-secondary btn-sm rounded-3 w-auto px-3">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <!-- CARD STATISTIK -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon purple">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div>
                            <span class="stat-title">Total Anggaran</span>
                            <h5>Rp <?php echo number_format($total_anggaran, 0, ',', '.'); ?></h5>
                            <small>Total Dana Direncanakan</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div>
                            <span class="stat-title">Total Pengeluaran</span>
                            <h5>Rp <?php echo number_format($total_realisasi, 0, ',', '.'); ?></h5>
                            <small><?php echo $persentase_terpakai; ?>% Terpakai</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon <?php echo ($sisa_anggaran < 0) ? 'bg-danger' : 'green'; ?>">
                            <i class="bi <?php echo ($sisa_anggaran < 0) ? 'bi-exclamation-triangle-fill' : 'bi-safe2-fill'; ?>"></i>
                        </div>
                        <div>
                            <span class="stat-title"><?php echo ($sisa_anggaran < 0) ? 'Over Budget' : 'Sisa Anggaran'; ?></span>
                            <h5 class="<?php echo ($sisa_anggaran < 0) ? 'text-danger' : ''; ?>">
                                <?php 
                                if($sisa_anggaran >= 0){
                                    echo "Rp " . number_format($sisa_anggaran, 0, ',', '.');
                                } else {
                                    echo "-Rp " . number_format(abs($sisa_anggaran), 0, ',', '.');
                                }
                                ?>
                            </h5>
                            <small><?php echo ($sisa_anggaran < 0) ? 'Pembengkakan Biaya' : 'Dana Tersisa'; ?></small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="bi bi-pie-chart-fill"></i>
                        </div>
                        <div>
                            <span class="stat-title">Persentase Penggunaan</span>
                            <h5><?php echo $persentase_terpakai; ?>%</h5>
                            <small>Dari Total Anggaran</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABEL DAFTAR KEBUTUHAN -->
            <div class="budget-card">
                <div class="budget-tabs">
                    <a href="index.php?id_event=<?php echo $id_event; ?>" class="tab active">
                        Kebutuhan Anggaran
                    </a>
                    <a href="statistics.php?id_event=<?php echo $id_event; ?>" class="tab">
                        Statik Anggaran
                    </a>
                </div>

                <div class="action-bar flex-nowrap gap-2">
                    <a href="create.php?id_event=<?php echo $id_event; ?>" class="btn-add d-inline-flex align-items-center text-decoration-none">
                        <i class="bi bi-plus-lg me-2"></i> Tambah Kebutuhan
                    </a>

                    <!-- FORM PENCARIAN SESUAI DESIGN REQUIREMENT -->
                    <form method="GET" class="search-box">
                        <input type="hidden" name="id_event" value="<?php echo $id_event; ?>">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari kebutuhan, kategori, status...">
                        <?php if ($search != ''): ?>
                            <a href="index.php?id_event=<?php echo $id_event; ?>" class="btn-close ms-2" style="font-size: 12px;"></a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Kebutuhan</th>
                                <th class="text-center">Kategori</th>
                                <th class="text-end">Anggaran</th>
                                <th class="text-end">Realisasi</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($budgets_result) > 0): ?>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($budgets_result)): ?>
                                    <tr>
                                        <td class="text-center"><?php echo $no++; ?></td>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($row['kebutuhan']); ?></td>
                                        <td class="text-center">
                                            <span class="category-badge">
                                                <?= htmlspecialchars($row['kategori']) ?>
                                            </span>
                                        </td>
                                        <td class="text-end">Rp <?php echo number_format($row['anggaran'], 0, ',', '.'); ?></td>
                                        <td class="text-end">Rp <?php echo number_format($row['realisasi'], 0, ',', '.'); ?></td>
                                        
                                        <!-- RENDER BADGE BOOTSTRAP DINAMIS NEGATIVE ANTI BUG WARNA -->
                                        <td class="text-center">
                                            <?php 
                                            $check_status = strtolower(trim($row['status']));
                                            switch ($check_status) {
                                                case 'belum terealisasi':
                                                    $badge_class = 'secondary';
                                                    $status_text = 'Belum Terealisasi';
                                                    break;
                                                case 'dalam anggaran':
                                                    $badge_class = 'primary';
                                                    $status_text = 'Dalam Anggaran';
                                                    break;
                                                case 'sesuai':
                                                    $badge_class = 'success';
                                                    $status_text = 'Sesuai';
                                                    break;
                                                case 'melebihi':
                                                    $badge_class = 'danger';
                                                    $status_text = 'Melebihi';
                                                    break;
                                                default:
                                                    $badge_class = 'secondary';
                                                    $status_text = ucwords($row['status']);
                                            }
                                            ?>
                                            <span class="badge bg-<?= $badge_class ?> px-3 py-2 rounded-pill fw-semibold" style="font-size: 12px;">
                                                <?= $status_text ?>
                                            </span>
                                        </td>
                                        
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="edit.php?id_anggaran=<?php echo $row['id_anggaran']; ?>&id_event=<?php echo $id_event; ?>" class="btn btn-sm btn-primary text-white">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="delete.php?id_anggaran=<?php echo $row['id_anggaran']; ?>&id_event=<?php echo $id_event; ?>" class="btn btn-sm btn-danger text-white" onclick="return confirm('Yakin ingin menghapus data anggaran ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum ada kebutuhan anggaran yang terdaftar.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

</body>
</html>