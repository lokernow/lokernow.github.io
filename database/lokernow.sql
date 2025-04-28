-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2025 at 05:36 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lokernow`
--

-- --------------------------------------------------------

--
-- Table structure for table `company_profiles`
--

CREATE TABLE `company_profiles` (
  `id` int(11) NOT NULL,
  `id_perusahaan` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nomor_telepon` varchar(20) DEFAULT NULL,
  `nama_perusahaan` varchar(100) NOT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `kultur` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `galeri_perusahaan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lamaran`
--

CREATE TABLE `lamaran` (
  `id` int(11) NOT NULL,
  `pelamar_id` int(11) NOT NULL,
  `lowongan_id` int(11) NOT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `surat_lamaran` text DEFAULT NULL,
  `status` enum('dikirim','diproses','diterima','ditolak') DEFAULT 'dikirim',
  `tanggal_lamaran` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lowongan_kerja`
--

CREATE TABLE `lowongan_kerja` (
  `id` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nama_perusahaan` varchar(150) DEFAULT NULL,
  `judul_pekerjaan` varchar(150) DEFAULT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `wilayah` varchar(100) DEFAULT NULL,
  `tipe_pekerjaan` enum('Part Time','Full Time') DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_perusahaan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pelamar`
--

CREATE TABLE `pelamar` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pendidikan_pelamar`
--

CREATE TABLE `pendidikan_pelamar` (
  `id` int(11) NOT NULL,
  `pelamar_id` int(11) NOT NULL,
  `tingkat_pendidikan` varchar(50) NOT NULL,
  `nama_institusi` varchar(100) NOT NULL,
  `bidang_studi` varchar(100) DEFAULT NULL,
  `tahun_mulai` varchar(7) DEFAULT NULL,
  `tahun_selesai` varchar(7) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengalaman_pelamar`
--

CREATE TABLE `pengalaman_pelamar` (
  `id` int(11) NOT NULL,
  `pelamar_id` int(11) NOT NULL,
  `nama_pekerjaan` varchar(100) NOT NULL,
  `nama_perusahaan` varchar(100) NOT NULL,
  `tanggal_mulai` varchar(7) DEFAULT NULL,
  `tanggal_selesai` varchar(7) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `perusahaan`
--

CREATE TABLE `perusahaan` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profile_pelamar`
--

CREATE TABLE `profile_pelamar` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nomor_telepon` varchar(20) DEFAULT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `deskripsi_personal` text DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `simpan_lamaran`
--

CREATE TABLE `simpan_lamaran` (
  `id` int(11) NOT NULL,
  `pelamar_id` int(11) NOT NULL,
  `lowongan_id` int(11) NOT NULL,
  `tanggal_simpan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `verification_tokens`
--

CREATE TABLE `verification_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `token` varchar(100) NOT NULL,
  `expiry_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `company_profiles`
--
ALTER TABLE `company_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_perusahaan` (`id_perusahaan`);

--
-- Indexes for table `lamaran`
--
ALTER TABLE `lamaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelamar_id` (`pelamar_id`),
  ADD KEY `lowongan_id` (`lowongan_id`);

--
-- Indexes for table `lowongan_kerja`
--
ALTER TABLE `lowongan_kerja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lokasi` (`lokasi`);

--
-- Indexes for table `pelamar`
--
ALTER TABLE `pelamar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `pendidikan_pelamar`
--
ALTER TABLE `pendidikan_pelamar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelamar_id` (`pelamar_id`);

--
-- Indexes for table `pengalaman_pelamar`
--
ALTER TABLE `pengalaman_pelamar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelamar_id` (`pelamar_id`);

--
-- Indexes for table `perusahaan`
--
ALTER TABLE `perusahaan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `profile_pelamar`
--
ALTER TABLE `profile_pelamar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `simpan_lamaran`
--
ALTER TABLE `simpan_lamaran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_simpan` (`pelamar_id`,`lowongan_id`),
  ADD KEY `lowongan_id` (`lowongan_id`);

--
-- Indexes for table `verification_tokens`
--
ALTER TABLE `verification_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `company_id` (`company_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `company_profiles`
--
ALTER TABLE `company_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lamaran`
--
ALTER TABLE `lamaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `lowongan_kerja`
--
ALTER TABLE `lowongan_kerja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pelamar`
--
ALTER TABLE `pelamar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `pendidikan_pelamar`
--
ALTER TABLE `pendidikan_pelamar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pengalaman_pelamar`
--
ALTER TABLE `pengalaman_pelamar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `perusahaan`
--
ALTER TABLE `perusahaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `profile_pelamar`
--
ALTER TABLE `profile_pelamar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `simpan_lamaran`
--
ALTER TABLE `simpan_lamaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `verification_tokens`
--
ALTER TABLE `verification_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `company_profiles`
--
ALTER TABLE `company_profiles`
  ADD CONSTRAINT `company_profiles_ibfk_1` FOREIGN KEY (`id_perusahaan`) REFERENCES `perusahaan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lamaran`
--
ALTER TABLE `lamaran`
  ADD CONSTRAINT `lamaran_ibfk_1` FOREIGN KEY (`pelamar_id`) REFERENCES `pelamar` (`id`),
  ADD CONSTRAINT `lamaran_ibfk_2` FOREIGN KEY (`lowongan_id`) REFERENCES `lowongan_kerja` (`id`);

--
-- Constraints for table `pendidikan_pelamar`
--
ALTER TABLE `pendidikan_pelamar`
  ADD CONSTRAINT `pendidikan_pelamar_ibfk_1` FOREIGN KEY (`pelamar_id`) REFERENCES `pelamar` (`id`);

--
-- Constraints for table `pengalaman_pelamar`
--
ALTER TABLE `pengalaman_pelamar`
  ADD CONSTRAINT `pengalaman_pelamar_ibfk_1` FOREIGN KEY (`pelamar_id`) REFERENCES `pelamar` (`id`);

--
-- Constraints for table `profile_pelamar`
--
ALTER TABLE `profile_pelamar`
  ADD CONSTRAINT `profile_pelamar_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `pelamar` (`id`);

--
-- Constraints for table `simpan_lamaran`
--
ALTER TABLE `simpan_lamaran`
  ADD CONSTRAINT `simpan_lamaran_ibfk_1` FOREIGN KEY (`pelamar_id`) REFERENCES `pelamar` (`id`),
  ADD CONSTRAINT `simpan_lamaran_ibfk_2` FOREIGN KEY (`lowongan_id`) REFERENCES `lowongan_kerja` (`id`);

--
-- Constraints for table `verification_tokens`
--
ALTER TABLE `verification_tokens`
  ADD CONSTRAINT `verification_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `pelamar` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `verification_tokens_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `perusahaan` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
