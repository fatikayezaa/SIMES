<?php
include '../includes/auth_check.php';
include '../config/database.php';

$id_event = (int)($_GET['id_event'] ?? 0);
$id_user  = $_SESSION['id_user'];

// Cek akses event
$cek = mysqli_query($conn, "SELECT id_event FROM events WHERE id_event='$id_event' AND id_user='$id_user'");
if (!$cek || mysqli_num_rows($cek) === 0) {
    header("Location: ../events/index.php"); exit;
}

// Ambil data laporan
$report = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM reports WHERE id_event='$id_event' LIMIT 1"));
if (!$report) {
    header("Location: index.php?id_event=$id_event"); exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIMES - Edit Laporan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/laporan.css">
</head>
<body>
<div class="wrapper">
    <main class="main-content" style="margin-left: 0; width: 100%;">
        <div class="content">
            <div class="content-header">
                <h2>Edit Laporan</h2>
                <p>Ubah catatan hasil kegiatan event</p>
            </div>
            
            <form action="update.php" method="POST">
                <input type="hidden" name="id_laporan" value="<?= $report['id_laporan'] ?>">
                <input type="hidden" name="id_event" value="<?= $id_event ?>">

                <div class="note-card">
                    <label class="form-label fw-semibold">Catatan Kegiatan</label>
                    <textarea name="catatan_kegiatan" class="form-control" rows="8" required><?= htmlspecialchars($report['catatan_kegiatan']) ?></textarea>
                    
                    <div class="action-area mt-4">
                        <button type="submit" class="btn btn-primary btn-save">
                            <i class="bi bi-check-circle-fill"></i> Update Laporan
                        </button>
                        <a href="index.php?id_event=<?= $id_event ?>" class="btn btn-outline-secondary btn-export">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>