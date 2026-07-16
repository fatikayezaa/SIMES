<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SIMES - Daftar Akun</title>

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
                <span></span>
            </div>

            <div class="left-content">

                <img src="../assets/img/ilustrasiLogin.png"
                     class="illustration"
                     alt="Ilustrasi">

                <h1>
                    Mulai Kelola Event
                    <br>
                    Kampus Sekarang
                </h1>

                <p>
                    Daftarkan diri Anda untuk menikmati kemudahan manajemen event kampus terintegrasi.
                </p>

            </div>

        </div>

        <!-- ========================= -->
        <!-- RIGHT -->
        <!-- ========================= -->
        <div class="col-lg-7 right-side">

            <div class="login-card">

                <h2>Daftar Akun Baru</h2>

                <p class="subtitle">
                    Silakan isi data diri Anda untuk mendaftar
                </p>

                <!-- Alert Session Backend (Bootstrap Style) -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger py-2">
                        <?php
                        echo htmlspecialchars($_SESSION['error']);
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success py-2">
                        <?php
                        echo htmlspecialchars($_SESSION['success']);
                        unset($_SESSION['success']);
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Form dihubungkan ke proses register backend -->
                <form action="register_process.php" method="POST">
                    
                    <!-- Nama -->
                    <div class="mb-3">

                        <label class="form-label">
                            Nama Lengkap
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-person"></i>
                            </span>

                            <input
                                type="text"
                                class="form-control"
                                name="nama"
                                placeholder="Masukkan Nama Lengkap Anda"
                                required
                            >

                        </div>

                    </div>

                    <!-- Email -->
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
                                name="email"
                                placeholder="Masukkan Email Anda"
                                required
                            >

                        </div>

                    </div>

                    <!-- Password -->
                    <div class="mb-4">

                        <label class="form-label">
                            Password
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>

                            <input
                                type="password"
                                class="form-control"
                                name="password"
                                placeholder="Buat Password Minimal 6 Karakter"
                                required
                            >

                        </div>

                    </div>

                    <!-- Button Submit -->
                    <button
                        type="submit"
                        class="btn btn-login w-100">
                        Daftar
                    </button>

                    <!-- Link Login -->
                    <div class="register">
                        Sudah punya akun?
                        <a href="login.php">
                            Login
                        </a>
                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>