<?php
include '../includes/auth_check.php';
include '../config/database.php';

/*
|--------------------------------------------------------------------------
| Validasi Parameter
|--------------------------------------------------------------------------
*/

if (
    !isset($_GET['id_peserta']) ||
    !isset($_GET['id_event'])
) {
    header("Location: ../events/index.php");
    exit;
}

$id_peserta = (int) $_GET['id_peserta'];
$id_event   = (int) $_GET['id_event'];
$id_user    = $_SESSION['id_user'];

/*
|--------------------------------------------------------------------------
| Cek Kepemilikan Event
|--------------------------------------------------------------------------
*/

$cek = mysqli_query(
    $conn,
    "SELECT id_event
     FROM events
     WHERE id_event='$id_event'
     AND id_user='$id_user'"
);

if (mysqli_num_rows($cek) === 0) {
    $_SESSION['error']="Event tidak ditemukan.";
    header("Location:../events/index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil Data Peserta
|--------------------------------------------------------------------------
*/

$query="SELECT *
        FROM participants
        WHERE id_peserta='$id_peserta'
        AND id_event='$id_event'";

$result=mysqli_query($conn,$query);

if(mysqli_num_rows($result)==0){
    $_SESSION['error']="Peserta tidak ditemukan.";
    header("Location:index.php?id_event=$id_event");
    exit;
}

$peserta=mysqli_fetch_assoc($result);

/*
|--------------------------------------------------------------------------
| Sidebar Event Query (Untuk menampilkan list event di sidebar)
|--------------------------------------------------------------------------
*/
$result_sidebar = mysqli_query(
    $conn,
    "SELECT id_event,nama_event
     FROM events
     WHERE id_user='$id_user'
     ORDER BY id_event DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMES - Edit Peserta</title>
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
                <img src="../assets/img/logo.png" alt="Logo">
            </div>
            <ul class="menu">
                <li>
                    <a href="../beranda.php">
                        <i class="bi bi-house"></i>
                        Beranda
                    </a>
                </li>
                <li class="active">
                    <a href="index.php?id_event=<?= $id_event; ?>">
                        <i class="bi bi-card-checklist"></i>
                        Terdaftar
                    </a>
                </li>
            </ul>

            <div class="event-list">
                <?php while ($sidebar = mysqli_fetch_assoc($result_sidebar)): ?>
                    <a href="index.php?id_event=<?= $sidebar['id_event']; ?>"
                       class="<?= ($sidebar['id_event'] == $id_event) ? 'selected' : ''; ?>">
                        <?= htmlspecialchars($sidebar['nama_event']); ?>
                    </a>
                <?php endwhile; ?>
            </div>

            <button class="btn-event" onclick="window.location='../events/create.php'">
                <i class="bi bi-plus-lg"></i>
                Buat Event
            </button>
        </aside>

        <!-- ================= MAIN ================= -->
        <main class="main-content">

            <!-- TOPBAR -->
            <header class="topbar">
                <div class="search">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Cari Event, Pengumuman, atau lainnya..." disabled>
                </div>
                <div class="profile">
                    <img src="../assets/img/profile.png" alt="Profile">
                    <span><?= htmlspecialchars($_SESSION['nama']); ?></span>
                </div>
            </header>

            <div class="content">

                <!-- HEADER -->
                <div class="content-header mb-4">
                    <div>
                        <h2>Edit Peserta</h2>
                        <p>Perbarui data peserta <strong><?= htmlspecialchars($peserta['nama']); ?></strong></p>
                    </div>
                </div>

                <!-- FORM EDIT PESERTA -->
                <div class="card border-0 shadow-sm" style="border-radius: 18px; background: #FFFFFF;">
                    <div class="card-body p-4 p-md-5">
                        <form action="update.php" method="POST">

                            <input type="hidden" name="id_peserta" value="<?= $peserta['id_peserta']; ?>">
                            <input type="hidden" name="id_event" value="<?= $id_event; ?>">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control px-3 py-2" value="<?= htmlspecialchars($peserta['nama']); ?>" required>
                                </div>
                                <div class="col-md-6 mt-3 mt-md-0">
                                    <label class="form-label fw-medium text-secondary">Instansi</label>
                                    <input type="text" name="instansi" class="form-control px-3 py-2" value="<?= htmlspecialchars($peserta['instansi']); ?>">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary">Email</label>
                                    <input type="email" name="email" class="form-control px-3 py-2" value="<?= htmlspecialchars($peserta['email']); ?>">
                                </div>
                                <div class="col-md-6 mt-3 mt-md-0">
                                    <label class="form-label fw-medium text-secondary">No HP</label>
                                    <input type="text" name="no_hp" class="form-control px-3 py-2" value="<?= htmlspecialchars($peserta['no_hp']); ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium text-secondary">Status Kehadiran</label>
                                <select name="status_kehadiran" class="form-select px-3 py-2">
                                    <option value="belum hadir" <?= $peserta['status_kehadiran'] == 'belum hadir' ? 'selected' : ''; ?>>
                                        Belum Hadir
                                    </option>
                                    <option value="hadir" <?= $peserta['status_kehadiran'] == 'hadir' ? 'selected' : ''; ?>>
                                        Hadir
                                    </option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="index.php?id_event=<?= $id_event; ?>" class="btn btn-light px-4 py-2 border fw-medium">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary px-4 py-2 fw-medium" style="background: #2455FF;">
                                    <i class="bi bi-save me-1"></i> Update Peserta
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </main>

    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>