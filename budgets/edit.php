<?php
include '../includes/auth_check.php';
include '../config/database.php';

if (!isset($_GET['id_anggaran']) || !isset($_GET['id_event'])) {
    header("Location: ../beranda.php");
    exit;
}

$id_anggaran = (int) $_GET['id_anggaran'];
$id_event    = (int) $_GET['id_event'];
$id_user     = $_SESSION['id_user'];
$nama_user   = $_SESSION['nama'];

$cek = mysqli_query($conn, "SELECT id_event, nama_event FROM events WHERE id_event='$id_event' AND id_user='$id_user'");
if (!$cek || mysqli_num_rows($cek) === 0) {
    header("Location: ../beranda.php");
    exit;
}
$event = mysqli_fetch_assoc($cek);

$query = "SELECT * FROM budgets WHERE id_anggaran='$id_anggaran' AND id_event='$id_event' LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    header("Location: index.php?id_event=$id_event");
    exit;
}
$budget = mysqli_fetch_assoc($result);
$query_sidebar = "SELECT id_event, nama_event FROM events WHERE id_user = '$id_user' ORDER BY id_event DESC";
$result_sidebar = mysqli_query($conn, $query_sidebar);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMES - Edit Anggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/anggaran.css?v=2">
</head>

<body>

<div class="wrapper">
    <aside class="sidebar">
        <div class="logo"><img src="../assets/img/logo.png" alt="SIMES"></div>
        <ul class="menu">
            <li><a href="../beranda.php"><i class="bi bi-house"></i> Beranda</a></li>
            <li class="active"><a href="#"><i class="bi bi-card-checklist"></i> Terdaftar <i class="bi bi-caret-down-fill arrow"></i></a></li>
        </ul>
        <div class="event-list">
            <?php while ($row = mysqli_fetch_assoc($result_sidebar)): ?>
                <a class="<?php echo ($row['id_event'] == $id_event) ? 'selected' : ''; ?>" href="index.php?id_event=<?php echo $row['id_event']; ?>">
                    <?php echo htmlspecialchars($row['nama_event']); ?>
                </a>
            <?php endwhile; ?>
        </div>
        <button class="btn-event" onclick="window.location.href='../events/create.php'"><i class="bi bi-plus-lg"></i> Buat Event</button>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="search"><i class="bi bi-search"></i><input type="text" placeholder="Cari..."></div>
            <div class="profile">
                <img src="../assets/img/profile.jpg" alt="Profile" style="width: 45px !important; height: 45px !important; border-radius: 50% !important; object-fit: cover !important; display: block !important;">
                <span><?php echo htmlspecialchars($nama_user); ?></span>
            </div>
        </header>

        <div class="content">
            <div class="content-header d-flex justify-content-between align-items-center">
                <div>
                    <h2>Edit Anggaran</h2>
                    <p>Event: <strong><?php echo htmlspecialchars($event['nama_event']); ?></strong></p>
                </div>
                <a href="index.php?id_event=<?php echo $id_event; ?>" class="btn btn-outline-secondary btn-sm rounded-3 w-auto px-3"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>

            <div class="card border-0 shadow-sm p-4" style="border-radius: 18px; background: #ffffff;">
                <form action="update.php" method="POST" onsubmit="return confirmValidate(this);">
                    <input type="hidden" name="id_anggaran" value="<?php echo $budget['id_anggaran']; ?>">
                    <input type="hidden" name="id_event" value="<?php echo $id_event; ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary">Nama Kebutuhan <span class="text-danger">*</span></label>
                            <input type="text" name="kebutuhan" class="form-control px-3 py-2 rounded-3" value="<?php echo htmlspecialchars($budget['kebutuhan']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary">Kategori <span class="text-danger">*</span></label>
                            <!-- Kategori Terkunci Dropdown saat Edit -->
                            <select class="form-select px-3 py-2 rounded-3" name="kategori" required>
                                <?php
                                $categories = ["Konsumsi", "Logistik", "Publikasi", "Transportasi", "Peralatan", "Honorarium", "Lainnya"];
                                foreach($categories as $cat) {
                                    $selected = (strtolower($budget['kategori']) == strtolower($cat)) ? 'selected' : '';
                                    echo "<option value='$cat' $selected>$cat</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-medium text-secondary">Rencana Anggaran (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="anggaran" class="form-control px-3 py-2 rounded-3" min="0" value="<?php echo $budget['anggaran']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-medium text-secondary">Realisasi Terpakai (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="realisasi" class="form-control px-3 py-2 rounded-3" min="0" value="<?php echo $budget['realisasi']; ?>" required>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-2">
                        <a href="index.php?id_event=<?php echo $id_event; ?>" class="btn btn-light px-4 py-2 rounded-3 fw-medium text-dark text-decoration-none">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-medium" style="background: #2455FF;"><i class="bi bi-check-circle me-1"></i> Update Anggaran</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
function confirmValidate(form) {
    var anggaran = parseFloat(form.anggaran.value) || 0;
    var realisasi = parseFloat(form.realisasi.value) || 0;
    if (realisasi > anggaran) {
        return confirm("Warning: Realisasi terpakai melebihi anggaran rencana. Tetap perbarui data?");
    }
    return true;
}
</script>
</body>
</html>