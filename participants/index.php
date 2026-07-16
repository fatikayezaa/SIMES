<?php
include '../includes/auth_check.php';
include '../config/database.php';

if (!isset($_GET['id_event'])) {
    header("Location: ../beranda.php");
    exit;
}

$id_event = (int)$_GET['id_event'];
$id_user  = $_SESSION['id_user'];

/*
|--------------------------------------------------------------------------
| Validasi Event
|--------------------------------------------------------------------------
*/

$event_query = mysqli_query(
    $conn,
    "SELECT *
     FROM events
     WHERE id_event='$id_event'
     AND id_user='$id_user'"
);

if (mysqli_num_rows($event_query) == 0) {
    header("Location: ../beranda.php");
    exit;
}

$event = mysqli_fetch_assoc($event_query);

/*
|--------------------------------------------------------------------------
| Sidebar Event
|--------------------------------------------------------------------------
*/

$result_sidebar = mysqli_query(
    $conn,
    "SELECT id_event,nama_event
     FROM events
     WHERE id_user='$id_user'
     ORDER BY id_event DESC"
);

/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

$search = mysqli_real_escape_string(
    $conn,
    trim($_GET['search'] ?? '')
);

$sql = "
SELECT *
FROM participants
WHERE id_event='$id_event'
";

if ($search != "") {

    $sql .= "
    AND (
        nama LIKE '%$search%'
        OR instansi LIKE '%$search%'
        OR email LIKE '%$search%'
    )
    ";
}

$sql .= " ORDER BY id_peserta DESC";

$participants_result = mysqli_query($conn, $sql);

// ========================
// Statistik
// ========================

$total_peserta = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT id_peserta
         FROM participants
         WHERE id_event='$id_event'"
    )
);

$total_hadir = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT id_peserta
         FROM participants
         WHERE id_event='$id_event'
         AND status_kehadiran='hadir'"
    )
);

$total_belum = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT id_peserta
         FROM participants
         WHERE id_event='$id_event'
         AND status_kehadiran='belum hadir'"
    )
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SIMES - Peserta</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/peserta.css">

</head>

<body>

    <div class="wrapper">

        <!-- ================= SIDEBAR ================= -->

        <aside class="sidebar">
            <div class="logo">
                <img src="/simes/assets/img/logo.png" alt="SIMES">
            </div>

            <ul class="menu">
                <!-- Cek apakah variabel $page isinya 'beranda' -->
                <li class="<?= (isset($page) && $page == 'beranda') ? 'active' : '' ?>">
                    <a href="/simes/beranda.php">
                        <i class="bi bi-house"></i> Beranda
                    </a>
                </li>

                <!-- Cek apakah variabel $page isinya 'terdaftar' -->
                <li class="<?= (isset($page) && $page == 'terdaftar') ? 'active' : '' ?>">
                    <a href="#">
                        <i class="bi bi-card-checklist"></i> Terdaftar <i class="bi bi-caret-down-fill arrow"></i>
                    </a>
                </li>
            </ul>

            <!-- Cek apakah ada data sidebar event untuk ditampilkan -->
            <?php if (isset($result_sidebar) && isset($id_event)): ?>
                <div class="event-list">
                    <?php while ($row = mysqli_fetch_assoc($result_sidebar)): ?>
                        <a class="<?= ($row['id_event'] == $id_event) ? 'selected' : ''; ?>"
                            href="/simes/participants/index.php?id_event=<?= $row['id_event']; ?>">
                            <?= htmlspecialchars($row['nama_event']); ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

            <button class="btn-event" onclick="window.location.href='/simes/events/create.php'">
                <i class="bi bi-plus-lg"></i> Buat Event
            </button>
        </aside>

        <!-- ================= MAIN ================= -->

        <main class="main-content">

            <!-- TOPBAR -->

            <header class="topbar">

                <div class="search">

                    <i class="bi bi-search"></i>

                    <input
                        type="text"
                        placeholder="Cari Event, Pengumuman, atau lainnya..."
                        disabled>

                </div>

                <div class="profile">

                    <img src="../assets/img/profile.jpg" alt="Profile">

                    <span>

                        <?= htmlspecialchars($_SESSION['nama']); ?>

                    </span>

                </div>

            </header>

            <div class="content">

                <?php if (isset($_SESSION['success'])): ?>

                    <div class="alert alert-success">

                        <?= $_SESSION['success'];
                        unset($_SESSION['success']); ?>

                    </div>

                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>

                    <div class="alert alert-danger">

                        <?= $_SESSION['error'];
                        unset($_SESSION['error']); ?>

                    </div>

                <?php endif; ?>

                <!-- HEADER -->
                <div class="content-header d-flex justify-content-between align-items-start">
                    <div>
                        <h2>Peserta</h2>
                        <p>
                            Kelola data peserta dan status kehadiran kegiatan
                            <strong><?= htmlspecialchars($event['nama_event']); ?></strong>
                        </p>
                    </div>
                   
                    <a href="../dashboard.php?id_event=<?= $id_event; ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="row g-4 mb-4">

                    <!-- Total -->

                    <div class="col-lg-4">

                        <div class="stat-card">

                            <div class="stat-icon green">

                                <i class="bi bi-people-fill"></i>

                            </div>

                            <div>

                                <span class="stat-title">

                                    Total Peserta

                                </span>

                                <h4>

                                    <?= $total_peserta; ?>

                                    <small>Orang</small>

                                </h4>

                            </div>

                        </div>

                    </div>

                    <!-- Hadir -->

                    <div class="col-lg-4">

                        <div class="stat-card">

                            <div class="stat-icon purple">

                                <i class="bi bi-person-check-fill"></i>

                            </div>

                            <div>

                                <span class="stat-title">

                                    Hadir

                                </span>

                                <h4>

                                    <?= $total_hadir; ?>

                                    <small>Orang</small>

                                </h4>

                            </div>

                        </div>

                    </div>

                    <!-- Belum Hadir -->

                    <div class="col-lg-4">

                        <div class="stat-card">

                            <div class="stat-icon red">

                                <i class="bi bi-person-x-fill"></i>

                            </div>

                            <div>

                                <span class="stat-title">

                                    Belum Hadir

                                </span>

                                <h4>

                                    <?= $total_belum; ?>

                                    <small>Orang</small>

                                </h4>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="action-bar">

                    <div class="d-flex gap-3"> <!-- Tambahkan class ini -->

                        <a href="create.php?id_event=<?= $id_event; ?>" class="btn-import">
                            <i class="bi bi-plus-lg"></i>
                            Tambah Peserta
                        </a>

                        <button
                            class="btn-import"
                            data-bs-toggle="modal"
                            data-bs-target="#importModal">

                            <i class="bi bi-upload"></i>
                            Import Data

                        </button>

                    </div>

                    <form method="GET" class="search-box">

                        <input type="hidden" name="id_event" value="<?= $id_event; ?>">

                        <i class="bi bi-search"></i>

                        <input
                            type="text"
                            name="search"
                            value="<?= htmlspecialchars($search); ?>"
                            placeholder="Cari peserta...">

                    </form>

                </div>

                <div class="table-card">

                    <table class="table align-middle">

                        <thead>

                            <tr>

                                <th>No</th>

                                <th>Nama Peserta</th>

                                <th>Instansi</th>

                                <th>Email</th>

                                <th>Status Kehadiran</th>

                                <th width="120">Aksi</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php if (mysqli_num_rows($participants_result) > 0): ?>

                                <?php $no = 1; ?>

                                <?php while ($row = mysqli_fetch_assoc($participants_result)): ?>

                                    <tr>

                                        <td><?= $no++; ?></td>

                                        <td><?= htmlspecialchars($row['nama']); ?></td>

                                        <td><?= htmlspecialchars($row['instansi']); ?></td>

                                        <td><?= htmlspecialchars($row['email']); ?></td>

                                        <td>
                                            <select class="form-select form-select-sm badge-select <?= $row['status_kehadiran'] == 'hadir' ? 'hadir' : 'belum' ?>"
                                                onchange="window.location.href='update_status.php?id_peserta=<?= $row['id_peserta']; ?>&id_event=<?= $id_event; ?>&status=' + this.value">

                                                <option value="belum hadir" <?= $row['status_kehadiran'] == 'belum hadir' ? 'selected' : ''; ?>>🔴 Belum Hadir</option>
                                                <option value="hadir" <?= $row['status_kehadiran'] == 'hadir' ? 'selected' : ''; ?>>🟢 Hadir</option>

                                            </select>
                                        </td>
                                        <td width="130">

                                            <a
                                                href="edit.php?id_peserta=<?= $row['id_peserta']; ?>&id_event=<?= $id_event; ?>"
                                                class="btn btn-sm btn-warning"
                                                title="Edit">

                                                <i class="bi bi-pencil"></i>

                                            </a>

                                            <button class="btn btn-sm btn-danger" onclick="hapusPeserta(<?= $row['id_peserta']; ?>, <?= $id_event; ?>, this)" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>

                                    <td colspan="6" class="text-center py-5">

                                        Belum ada peserta.

                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

            <!-- ================= MODAL IMPORT ================= -->
            <div class="modal fade" id="importModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Import Data Peserta</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="process_import.php" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <input type="hidden" name="id_event" value="<?= $id_event; ?>">

                                <!-- KETERANGAN FORMAT -->
                                <div class="alert alert-info" style="font-size: 0.85rem;">
                                    <strong>Panduan Format CSV:</strong><br>
                                    Pastikan file CSV memiliki urutan kolom berikut (tanpa judul baris/header):<br>
                                    <code>Nama Peserta, Instansi, Email</code>
                                    <br><br>
                                    <a href="../assets/templates/template.csv" class="text-decoration-none">
                                        <i class="bi bi-download"></i> Download Template CSV
                                    </a>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Upload File CSV</label>
                                    <input type="file" name="file_csv" class="form-control" accept=".csv" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" name="btn_import" class="btn btn-primary">Import</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Bootstrap -->

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

            <script>
                function hapusPeserta(idPeserta, idEvent, elemenTombol) {
                    if (confirm('Yakin ingin menghapus peserta ini?')) {

                        // Memanggil delete.php di latar belakang
                        fetch(`delete.php?id_peserta=${idPeserta}&id_event=${idEvent}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {

                                    let baris = elemenTombol.closest('tr');

                                    // Efek animasi menghilang (fade out)
                                    baris.style.transition = "all 0.5s";
                                    baris.style.opacity = "0";

                                    setTimeout(() => {
                                        baris.remove();
                                    }, 500);
                                } else {
                                    alert('Gagal menghapus: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Terjadi kesalahan pada server.');
                            });
                    }
                }
            </script>

</body>

</html>