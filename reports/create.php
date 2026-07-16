<?php include '../includes/auth_check.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Laporan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/laporan.css">
</head>
<body>
<div class="wrapper">
    <main class="main-content" style="margin-left:0; width:100%;">
        <div class="content">
            <div class="content-header"><h2>Buat Laporan</h2></div>
            <form action="store.php" method="POST">
                <input type="hidden" name="id_event" value="<?= (int)$_GET['id_event'] ?>">
                <div class="note-card">
                    <label class="form-label">Catatan Kegiatan</label>
                    <textarea name="catatan_kegiatan" class="form-control" rows="8" required></textarea>
                    <div class="action-area mt-4">
                        <button type="submit" class="btn btn-primary btn-save">Simpan Laporan</button>
                        <a href="index.php?id_event=<?= (int)$_GET['id_event'] ?>" class="btn btn-outline-secondary btn-export">Kembali</a>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>