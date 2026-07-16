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
/*
|--------------------------------------------------------------------------
| Ambil Data Event Aktif
|--------------------------------------------------------------------------
*/

$query = "SELECT *
          FROM events
          WHERE id_event = '$id_event'
          AND id_user = '$id_user'";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Terjadi kesalahan database: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = "Event tidak ditemukan.";
    header("Location: beranda.php");
    exit;
}

$event = mysqli_fetch_assoc($result);

/*
|--------------------------------------------------------------------------
| Ambil Semua Event Milik User
|--------------------------------------------------------------------------
*/

$query_sidebar = "SELECT id_event, nama_event
                  FROM events
                  WHERE id_user = '$id_user'
                  ORDER BY id_event DESC";

$result_sidebar = mysqli_query($conn, $query_sidebar);

if (!$result_sidebar) {
    die("Terjadi kesalahan database: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SIMES - Dashboard Event</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/dashboard.css">

</head>

<body>

    <div class="wrapper">

        <!-- ================= SIDEBAR ================= -->

        <aside class="sidebar">

            <div class="logo">

                <img src="assets/img/logo.png" alt="SIMES">

            </div>

            <ul class="menu">

                <li>

                    <a href="beranda.php">

                        <i class="bi bi-house"></i>

                        Beranda

                    </a>

                </li>

                <li class="active">

                    <a href="#">

                        <i class="bi bi-card-checklist"></i>

                        Terdaftar

                        <i class="bi bi-caret-down-fill arrow"></i>

                    </a>

                </li>

            </ul>

            <div class="event-list">

                <?php while ($row = mysqli_fetch_assoc($result_sidebar)): ?>

                    <a
                        class="<?php echo ($row['id_event'] == $id_event) ? 'selected' : ''; ?>"
                        href="dashboard.php?id_event=<?php echo $row['id_event']; ?>">

                        <?php echo htmlspecialchars($row['nama_event']); ?>

                    </a>

                <?php endwhile; ?>

            </div>
            <button
                class="btn-event"
                onclick="window.location.href='events/create.php'">

                <i class="bi bi-plus-lg"></i>

                Buat Event

            </button>

        </aside>

        <!-- ================= CONTENT ================= -->

        <main class="main-content">

            <!-- TOPBAR -->

            <header class="topbar">

                <div class="search">

                    <i class="bi bi-search"></i>

                    <input
                        type="text"
                        placeholder="Cari Event, Pengumuman, atau lainnya...">

                </div>

                <div class="profile">

                    <img src="assets/img/profile.jpg">

                    <span><?php echo htmlspecialchars($nama); ?></span>

                </div>

            </header>

            <!-- BANNER -->
            <div class="banner">

                <img
                    src="<?php echo htmlspecialchars($event['banner']); ?>"
                    alt="<?php echo htmlspecialchars($event['nama_event']); ?>">

            </div>

            <!-- MENU CARD -->

            <!-- MENU CARD -->

            <div class="menu-card-container">
                <div class="row g-4">

                    <!-- Dashboard Monitoring -->

                    <div class="col-lg-4 col-md-6">

                        <a href="dashboardmonitoring.php?id_event=<?php echo $id_event; ?>" class="menu-link">

                            <div class="menu-card">

                                <div class="icon blue">

                                    <i class="bi bi-grid-fill"></i>

                                </div>

                                <h5>Dashboard Monitoring</h5>

                                <p>
                                    Lihat ringkasan data monitoring
                                    data kegiatan event
                                </p>

                                <div class="arrow-btn">

                                    <i class="bi bi-chevron-right"></i>

                                </div>

                            </div>

                        </a>

                    </div>

                    <!-- Peserta -->

                    <div class="col-lg-4 col-md-6">

                        <a href="participants/index.php?id_event=<?php echo $id_event; ?>" class="menu-link">

                            <div class="menu-card">

                                <div class="icon green">

                                    <i class="bi bi-people-fill"></i>

                                </div>

                                <h5>Peserta</h5>

                                <p>
                                    Lihat ringkasan data monitoring
                                    data kegiatan event
                                </p>

                                <div class="arrow-btn">

                                    <i class="bi bi-chevron-right"></i>

                                </div>

                            </div>

                        </a>

                    </div>

                    <!-- Anggaran -->

                    <div class="col-lg-4 col-md-6">

                        <a href="budgets/index.php?id_event=<?php echo $id_event; ?>" class="menu-link">

                            <div class="menu-card">

                                <div class="icon purple">

                                    <i class="bi bi-wallet2"></i>

                                </div>

                                <h5>Anggaran</h5>

                                <p>
                                    Lihat ringkasan data monitoring
                                    data kegiatan event
                                </p>

                                <div class="arrow-btn">

                                    <i class="bi bi-chevron-right"></i>

                                </div>

                            </div>

                        </a>

                    </div>

                    <!-- Laporan -->

                    <div class="col-lg-4 col-md-6">

                        <a href="reports/index.php?id_event=<?php echo $id_event; ?>" class="menu-link">

                            <div class="menu-card">

                                <div class="icon gray">

                                    <i class="bi bi-file-earmark-text-fill"></i>

                                </div>

                                <h5>Laporan Kegiatan</h5>

                                <p>
                                    Lihat ringkasan data monitoring
                                    data kegiatan event
                                </p>

                                <div class="arrow-btn">

                                    <i class="bi bi-chevron-right"></i>

                                </div>

                            </div>

                        </a>

                    </div>

                    <!-- Dokumentasi -->

                    <div class="col-lg-4 col-md-6">

                        <a href="documentations/index.php?id_event=<?php echo $id_event; ?>" class="menu-link">

                            <div class="menu-card">

                                <div class="icon lightgreen">

                                    <i class="bi bi-camera-fill"></i>

                                </div>

                                <h5>Dokumentasi</h5>

                                <p>
                                    Lihat ringkasan data monitoring
                                    data kegiatan event
                                </p>

                                <div class="arrow-btn">

                                    <i class="bi bi-chevron-right"></i>

                                </div>

                            </div>

                        </a>

                    </div>

                    <!-- Pengaturan -->

                    <div class="col-lg-4 col-md-6">

                        <a href="events/edit.php?id=<?php echo $id_event; ?>" class="menu-link">

                            <div class="menu-card">

                                <div class="icon navy">

                                    <i class="bi bi-gear-fill"></i>

                                </div>

                                <h5>Pengaturan Event</h5>

                                <p>
                                    Lihat ringkasan data monitoring
                                    data kegiatan event
                                </p>

                                <div class="arrow-btn">

                                    <i class="bi bi-chevron-right"></i>

                                </div>

                            </div>

                        </a>

                    </div>

                </div>
            </div>

        </main>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>