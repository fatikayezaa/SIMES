-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 16 Jul 2026 pada 21.35
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simes_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `budgets`
--

DROP TABLE IF EXISTS `budgets`;
CREATE TABLE IF NOT EXISTS `budgets` (
  `id_anggaran` int(11) NOT NULL AUTO_INCREMENT,
  `id_event` int(11) NOT NULL,
  `kebutuhan` varchar(150) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `anggaran` decimal(15,2) NOT NULL DEFAULT 0.00,
  `realisasi` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('belum terealisasi','dalam anggaran','sesuai','melebihi') DEFAULT 'belum terealisasi',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_anggaran`),
  KEY `fk_budgets_event` (`id_event`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `budgets`
--

INSERT INTO `budgets` (`id_anggaran`, `id_event`, `kebutuhan`, `kategori`, `anggaran`, `realisasi`, `status`, `created_at`) VALUES
(15, 9, 'Banner Kegiatan', 'Logistik', 500000.00, 500000.00, 'sesuai', '2026-07-16 04:14:55'),
(19, 9, 'Poster', 'Peralatan', 350000.00, 200000.00, 'dalam anggaran', '2026-07-16 18:46:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `documentations`
--

DROP TABLE IF EXISTS `documentations`;
CREATE TABLE IF NOT EXISTS `documentations` (
  `id_dokumentasi` int(11) NOT NULL AUTO_INCREMENT,
  `id_event` int(11) NOT NULL,
  `jenis_file` enum('foto','video') NOT NULL,
  `judul` varchar(150) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_dokumentasi`),
  KEY `fk_documentations_event` (`id_event`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `documentations`
--

INSERT INTO `documentations` (`id_dokumentasi`, `id_event`, `jenis_file`, `judul`, `file_path`, `keterangan`, `uploaded_at`) VALUES
(8, 9, 'foto', 'Foto Pembukaan 3', 'assets/uploads/dokumentasi/1784211835_6a58e97b8be97.jpg', 'baru', '2026-07-16 13:10:36'),
(10, 9, 'video', 'Video Sambutan', 'assets/uploads/dokumentasi/1784211981_6a58ea0d8f212.mp4', 'bisa ga', '2026-07-16 14:26:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id_event` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `nama_event` varchar(150) NOT NULL,
  `kategori_event` varchar(100) NOT NULL,
  `lokasi` varchar(150) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `penanggung_jawab` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `status_event` enum('draft','akan datang','berlangsung','selesai') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_event`),
  KEY `fk_events_user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `events`
--

INSERT INTO `events` (`id_event`, `id_user`, `nama_event`, `kategori_event`, `lokasi`, `tanggal`, `waktu`, `penanggung_jawab`, `deskripsi`, `banner`, `status_event`, `created_at`) VALUES
(9, 1, 'Seminar Digital', 'Seminar', 'Universitas Nasioanal', '2026-07-16', '13:51:00', 'Himasi', 'Seminar aja', 'assets/uploads/banners/1784229848_6a592fd86780c.png', 'berlangsung', '2026-07-15 06:50:26'),
(10, 1, 'Ngaji', 'Ibadah', 'Auditorium Kampus', '2026-07-16', '11:37:00', 'Himpunan Islam', 'ini adalah mengaji', 'assets/uploads/banners/1784176667_6a58601b2224b.jpg', 'selesai', '2026-07-16 04:37:19'),
(11, 1, 'Mabar', 'Hiburan', 'Kampus Bambu Kuning', '2026-07-16', '18:04:00', 'Fatik', '', 'assets/uploads/banners/1784199778_6a58ba6204d4c.png', 'akan datang', '2026-07-16 11:02:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `participants`
--

DROP TABLE IF EXISTS `participants`;
CREATE TABLE IF NOT EXISTS `participants` (
  `id_peserta` int(11) NOT NULL AUTO_INCREMENT,
  `id_event` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `instansi` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `status_kehadiran` enum('hadir','belum hadir') DEFAULT 'belum hadir',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_peserta`),
  KEY `fk_participants_event` (`id_event`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `participants`
--

INSERT INTO `participants` (`id_peserta`, `id_event`, `nama`, `instansi`, `email`, `no_hp`, `status_kehadiran`, `created_at`) VALUES
(14, 9, 'Fatika', 'Unas', 'fatikayezaa@gmail.com', '0812121212', 'belum hadir', '2026-07-16 14:53:44'),
(23, 9, 'Siti Aminah', 'Politeknik Kampus', 'siti@email.com', '08987654321', 'hadir', '2026-07-16 17:29:55'),
(24, 9, 'Ahmad Fauzi', 'Universitas Negeri', 'ahmad@email.com', '08123456789', 'hadir', '2026-07-16 18:37:02'),
(25, 9, 'Siti Aminah', 'Politeknik Kampus', 'siti@email.com', '08987654321', 'hadir', '2026-07-16 18:37:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reports`
--

DROP TABLE IF EXISTS `reports`;
CREATE TABLE IF NOT EXISTS `reports` (
  `id_laporan` int(11) NOT NULL AUTO_INCREMENT,
  `id_event` int(11) NOT NULL,
  `catatan_kegiatan` text DEFAULT NULL,
  `total_peserta` int(11) DEFAULT 0,
  `total_hadir` int(11) DEFAULT 0,
  `total_anggaran` decimal(15,2) DEFAULT 0.00,
  `total_realisasi` decimal(15,2) DEFAULT 0.00,
  `total_foto` int(11) DEFAULT 0,
  `total_video` int(11) DEFAULT 0,
  `tanggal_laporan` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_laporan`),
  UNIQUE KEY `id_event` (`id_event`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `reports`
--

INSERT INTO `reports` (`id_laporan`, `id_event`, `catatan_kegiatan`, `total_peserta`, `total_hadir`, `total_anggaran`, `total_realisasi`, `total_foto`, `total_video`, `tanggal_laporan`, `created_at`, `updated_at`) VALUES
(6, 9, 'Kegiatan Seminar Digital yang diselenggarakan pada tanggal 16 Juli 2026 telah berjalan dengan lancar sesuai dengan agenda yang telah ditetapkan sebelumnya. Secara keseluruhan, rangkaian acara berhasil mencapai target partisipasi peserta yang hadir, dengan tingkat kehadiran yang cukup tinggi dibandingkan dengan jumlah peserta yang terdaftar pada sistem. Seluruh rangkaian sesi, mulai dari pemaparan materi utama hingga sesi tanya jawab interaktif, mendapatkan antusiasme yang positif dari para peserta yang hadir di lokasi Universitas Nasioanal.  Dari sisi teknis pengelolaan anggaran, seluruh dana yang telah direncanakan telah digunakan untuk mendukung operasional kegiatan, dengan rincian pengeluaran yang telah disesuaikan dengan kebutuhan nyata di lapangan. Terdapat sisa anggaran yang akan dikembalikan ke kas organisasi sesuai dengan prosedur keuangan yang berlaku. Dokumentasi kegiatan berupa foto dan video telah tersimpan dengan baik sebagai bagian dari arsip digital kegiatan.  Meskipun kegiatan berjalan sukses, terdapat beberapa evaluasi teknis terkait manajemen mobilitas peserta di area pintu masuk yang memerlukan perhatian lebih pada kegiatan mendatang. Secara umum, koordinasi antar panitia (PIC) dan pihak internal Universitas Nasioanal berjalan dengan sangat baik dan sangat kooperatif, sehingga tidak ditemukan kendala berarti yang menghambat berjalannya acara. Laporan ini dibuat sebagai bentuk pertanggungjawaban resmi atas penyelenggaraan Seminar Digital agar dapat digunakan sebagai bahan acuan untuk perbaikan kegiatan-kegiatan di masa yang akan datang.', 0, 0, 0.00, 0.00, 0, 0, '2026-07-17', '2026-07-16 19:09:59', '2026-07-16 19:15:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `foto_profil` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nama`, `email`, `password`, `role`, `foto_profil`, `created_at`) VALUES
(1, 'AA', 'aa@gmail.com', '$2y$10$nMjTnmjHiz2fEYEWEFTR5eh6d1ofritbk7NQZwuXfR1V6GDJkP46m', 'panitia', NULL, '2026-07-14 15:19:03'),
(6, 'Fatika', 'fatikayezaa@gmail.com', '$2y$10$z8XpSvvYnmd.x/FKaJE8zO1RACixHIc50szyupHetukXj/WlsNL/.', 'User', NULL, '2026-07-14 19:13:55'),
(7, 'Aurel', 'aurel@gmail.com', '$2y$10$Oq86A5lm4zoEHf5x/aWdKe94WQhrRwXEYMKRqfr03pVCfX3p8eOiS', 'User', NULL, '2026-07-15 05:50:02');

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `fk_budgets_event` FOREIGN KEY (`id_event`) REFERENCES `events` (`id_event`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `documentations`
--
ALTER TABLE `documentations`
  ADD CONSTRAINT `fk_documentations_event` FOREIGN KEY (`id_event`) REFERENCES `events` (`id_event`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_events_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `fk_participants_event` FOREIGN KEY (`id_event`) REFERENCES `events` (`id_event`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_reports_event` FOREIGN KEY (`id_event`) REFERENCES `events` (`id_event`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
