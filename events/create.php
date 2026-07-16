<?php
include '../includes/auth_check.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Event SIMES</title>
</head>

<body>
    <h2>Tambah Event</h2>

    <form action="store.php" method="POST" enctype="multipart/form-data">
        <label>Nama Event:</label><br>
        <input type="text" name="nama_event" required><br><br>

        <label>Kategori Event:</label><br>
        <input type="text" name="kategori_event" required><br><br>

        <label>Lokasi:</label><br>
        <input type="text" name="lokasi" required><br><br>

        <label>Tanggal:</label><br>
        <input type="date" name="tanggal" required><br><br>

        <label>Waktu:</label><br>
        <input type="time" name="waktu" required><br><br>

        <label>Penanggung Jawab:</label><br>
        <input type="text" name="penanggung_jawab" required><br><br>

        <label>Deskripsi:</label><br>
        <textarea name="deskripsi" rows="5"></textarea><br><br>

        <label>Banner Event:</label><br>
        <input
            type="file"
            name="banner"
            accept="image/*"
            required><br><br>

        <label>Status Event:</label><br>
        <select name="status_event" required>
            <option value="draft">Draft</option>
            <option value="akan datang">Akan Datang</option>
            <option value="berlangsung">Berlangsung</option>
            <option value="selesai">Selesai</option>
        </select><br><br>

        <button type="submit">Simpan Event</button>
    </form>

    <br>
    <a href="../beranda.php">← Kembali ke Beranda</a>
</body>

</html>