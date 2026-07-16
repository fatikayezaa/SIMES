<?php
session_start();

// Simulasi logika ketika tombol "Kirim" ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['success'] = "Permintaan reset password telah diterima.<br><br>Untuk saat ini fitur reset password melalui email belum tersedia. Silakan hubungi administrator SIMES.";
    header("Location: lupa_password.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Lupa Password - SIMES</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<div class="container-fluid">

    <div class="row vh-100">

        <!-- ========================= -->
        <!-- LEFT -->
        <!-- ========================= -->
        <div class="col-lg-5 left-side">

            <div class="logo">
                <img src="../assets/img/logo.png" alt="Logo">
            </div>

            <div class="left-content">

                <img src="../assets/img/ilustrasiLogin.png"
                     class="illustration"
                     alt="Ilustrasi">

                <h1>Lupa Password</h1>

                <p>
                    Masukkan email akun SIMES Anda.
                </p>

            </div>

        </div>

        <!-- ========================= -->
        <!-- RIGHT -->
        <!-- ========================= -->
        <div class="col-lg-7 right-side">

            <div class="login-card">

                <h2>Lupa Password</h2>

                <p class="subtitle">
                    Masukkan email yang terdaftar.
                </p>

                <!-- Tempat Menampilkan Pesan Pemberitahuan -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success py-3 small shadow-sm mb-4">
                        <?php
                        echo $_SESSION['success']; // Menampilkan teks pemberitahuan
                        unset($_SESSION['success']);
                        ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">

                    <div class="mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-envelope"></i>
                            </span>

                            <input
                                type="email"
                                class="form-control"
                                placeholder="Masukkan Email"
                                required>

                        </div>

                    </div>

                    <button type="submit" class="btn btn-login w-100">
                        Kirim
                    </button>

                </form>

                <div class="register">
                    <a href="login.php">
                        ← Kembali ke Login
                    </a>
                </div>

            </div>

        </div>

    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>