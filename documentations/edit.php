<?php
include '../includes/auth_check.php';
include '../config/database.php';

if (!isset($_GET['id_dokumentasi']) || !isset($_GET['id_event'])) {
    header("Location: ../beranda.php");
    exit;
}

$id_dokumentasi = (int) $_GET['id_dokumentasi'];
$id_event       = (int) $_GET['id_event'];
$id_user        = $_SESSION['id_user'];

// Cek akses event
$cek = mysqli_query($conn, "SELECT id_event FROM events WHERE id_event='$id_event' AND id_user='$id_user'");
if (mysqli_num_rows($cek) === 0) {
    header("Location: ../beranda.php");
    exit;
}

$query = "SELECT * FROM documentations WHERE id_dokumentasi='$id_dokumentasi' AND id_event='$id_event'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    $_SESSION['error'] = "Dokumentasi tidak ditemukan.";
    header("Location: index.php?id_event=$id_event");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Dokumentasi - SIMES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/simes/assets/css/beranda.css">
    <link rel="stylesheet" href="/simes/assets/css/dokumentasi.css">
</head>
<body>
<div class="wrapper">
    <!-- Sidebar tetap sama -->
    <aside class="sidebar">
        <div class="logo"><img src="/simes/assets/img/logo.png" alt="SIMES"></div>
        <ul class="menu">
            <li><a href="/simes/beranda.php"><i class="bi bi-house"></i> Beranda</a></li>
            <li class="active"><a href="index.php?id_event=<?= $id_event ?>"><i class="bi bi-card-checklist"></i> Dokumentasi</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="content">
            <h2>Edit Dokumentasi</h2>
            <div class="setting-card mt-4 p-4 shadow-sm bg-white rounded">
                <form action="update.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_dokumentasi" value="<?= $data['id_dokumentasi']; ?>">
                    <input type="hidden" name="id_event" value="<?= $id_event; ?>">
                    <input type="hidden" name="file_lama" value="<?= $data['file_path']; ?>">

                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($data['judul']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis File</label>
                        <select name="jenis_file" class="form-select">
                            <option value="foto" <?= $data['jenis_file'] == 'foto' ? 'selected' : ''; ?>>Foto</option>
                            <option value="video" <?= $data['jenis_file'] == 'video' ? 'selected' : ''; ?>>Video</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">File Saat Ini</label>
                        <div class="mb-2">
                            <a href="/simes/<?= $data['file_path']; ?>" target="_blank" class="btn btn-sm btn-info text-white">Lihat File</a>
                        </div>
                        <label class="form-label">Upload File Baru (Opsional)</label>
                        <input type="file" name="file_dokumentasi" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="4"><?= htmlspecialchars($data['keterangan']); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Dokumentasi</button>
                    <a href="index.php?id_event=<?= $id_event; ?>" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>