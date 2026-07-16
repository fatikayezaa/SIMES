<?php
include 'includes/auth_check.php';
include 'config/database.php';

$id_user = $_SESSION['id_user'];
$nama    = $_SESSION['nama'];

$search = trim($_GET['search'] ?? '');
$filter = trim($_GET['filter'] ?? 'semua');

if ($filter === 'saya_buat') {
    $query_event = "SELECT * FROM events WHERE id_user='$id_user'";
} elseif ($filter === 'saya_ikuti') {
    $query_event = "SELECT e.* FROM events e 
                    JOIN participants p ON e.id_event = p.id_event 
                    WHERE p.email = (SELECT email FROM users WHERE id_user = '$id_user')
                    AND e.id_user != '$id_user'";
} elseif ($filter === 'selesai') {
    $query_event = "SELECT * FROM events WHERE id_user='$id_user' AND status_event='selesai'";
} else {
    $query_event = "SELECT * FROM events WHERE id_user='$id_user'";
}

if ($search != '') {
    $search = mysqli_real_escape_string($conn, $search);
    $query_event .= " AND nama_event LIKE '%$search%'";
}

$query_event .= " ORDER BY id_event DESC";
$result_event = mysqli_query($conn, $query_event);

if (!$result_event) {
    die("Terjadi kesalahan database: " . mysqli_error($conn));
}

$total_event = mysqli_num_rows($result_event);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMES - Beranda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/beranda.css?v=2">
</head>

<body>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <nav class="navbar-custom">
        <div class="nav-left">
            <button class="menu-btn">
                <i class="bi bi-list"></i>
            </button>
            <img src="assets/img/logo.png" class="logo" alt="SIMES">
        </div>

        <form method="GET" class="search-box">
            <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
            <i class="bi bi-search"></i>
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari event...">
            <button type="submit" class="btn btn-sm btn-primary ms-2">Cari</button>
            <?php if (!empty($search)): ?>
                <a href="beranda.php?filter=<?php echo urlencode($filter); ?>" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
            <?php endif; ?>
        </form>

        <div class="dropdown profile">
            <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="assets/img/profile.jpg" alt="Profile">
                <span class="ms-2"><?php echo htmlspecialchars($nama); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text fw-bold"><?php echo htmlspecialchars($nama); ?></span></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-danger" href="auth/logout.php" onclick="return confirm('Yakin ingin logout?');">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="content">
            <h2>Halo, <?php echo htmlspecialchars($nama); ?>!</h2>
            <p class="subtitle">Berikut adalah event yang kamu ikutin atau kelola.</p>

            <h4 class="event-title">Event Saya</h4>
            <p class="mb-3">Total Event : <strong><?php echo $total_event; ?></strong></p>

            <div class="filter-menu">
                <a href="beranda.php?filter=semua<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"
                    class="btn <?php echo ($filter === 'semua') ? 'active' : ''; ?> text-decoration-none d-flex align-items-center justify-content-center">
                    Semua
                </a>
                <a href="beranda.php?filter=saya_buat<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"
                    class="btn <?php echo ($filter === 'saya_buat') ? 'active' : ''; ?> text-decoration-none d-flex align-items-center justify-content-center">
                    Saya Buat
                </a>
                <a href="beranda.php?filter=saya_ikuti<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"
                    class="btn <?php echo ($filter === 'saya_ikuti') ? 'active' : ''; ?> text-decoration-none d-flex align-items-center justify-content-center">
                    Saya Ikuti
                </a>
                <a href="beranda.php?filter=selesai<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"
                    class="btn <?php echo ($filter === 'selesai') ? 'active' : ''; ?> text-decoration-none d-flex align-items-center justify-content-center">
                    Selesai
                </a>
            </div>

            <div class="row mt-4">
                <?php if ($total_event > 0): ?>
                    <?php while ($event = mysqli_fetch_assoc($result_event)): ?>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="event-card">
                                <img src="<?php echo htmlspecialchars($event['banner']); ?>" class="event-img" alt="<?php echo htmlspecialchars($event['nama_event']); ?>">
                                <div class="event-body">
                                    <h5>
                                        <a href="dashboard.php?id_event=<?php echo $event['id_event']; ?>" class="event-link">
                                            <?php echo htmlspecialchars($event['nama_event']); ?>
                                        </a>
                                    </h5>
                                    <p>
                                        <i class="bi bi-calendar-event"></i>
                                        <?php echo date('d F Y', strtotime($event['tanggal'])); ?>
                                    </p>
                                    <p>
                                        <i class="bi bi-geo-alt"></i>
                                        <?php echo htmlspecialchars($event['lokasi']); ?>
                                    </p>

                                    <p class="event-description" style="font-size: 13px; color: #666; margin-top: 5px; height: 40px; overflow: hidden;">
                                        <?php
                                        $deskripsi = $event['deskripsi'];
                                        echo (strlen($deskripsi) > 70) ? htmlspecialchars(substr($deskripsi, 0, 70)) . '...' : htmlspecialchars($deskripsi);
                                        ?>
                                    </p>
                                </div>
                                <div class="event-footer position-relative">
                                    <span>
                                        <i class="bi bi-person-badge-fill"></i>
                                        <?php echo htmlspecialchars($event['penanggung_jawab']); ?>
                                    </span>
                                    <span>
                                        <i class="bi bi-info-circle-fill"></i>
                                        <?php echo htmlspecialchars($event['status_event']); ?>
                                    </span>
                                    <div class="dropdown">
                                        <i class="bi bi-three-dots" data-bs-toggle="dropdown" style="cursor:pointer;"></i>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="dashboard.php?id_event=<?php echo $event['id_event']; ?>">Dashboard</a></li>
                                            <li><a class="dropdown-item" href="events/edit.php?id=<?php echo $event['id_event']; ?>">Edit</a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li><a class="dropdown-item text-danger" onclick="return confirm('Yakin ingin menghapus event ini?')" href="events/delete.php?id=<?php echo $event['id_event']; ?>">Hapus</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="text-center py-5">
                            <h5>Belum ada event</h5>
                            <p>Kamu tidak memiliki kriteria event pada kategori ini.</p>
                            <a href="events/create.php" class="btn btn-primary">+ Buat Event</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>