-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2026 at 08:41 AM
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
-- Database: `semarak`
--

-- --------------------------------------------------------

--
-- Table structure for table `anak`
--

CREATE TABLE `anak` (
  `id` varchar(20) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `jenis_kelamin` varchar(20) NOT NULL,
  `asal_daerah` varchar(100) NOT NULL,
  `agama` varchar(50) NOT NULL,
  `umur` int(11) NOT NULL,
  `angkatan` varchar(50) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anak`
--

INSERT INTO `anak` (`id`, `nama`, `jenis_kelamin`, `asal_daerah`, `agama`, `umur`, `angkatan`, `status`) VALUES
('AN-001', 'Endang Sri Ayu', 'Perempuan', 'Bandung', 'Islam', 16, 'Angkatan II', 'Aktif'),
('AN-002', 'Doddy', 'Laki-laki', 'Bogor', 'Islam', 15, 'Angkatan II', 'Aktif'),
('AN-003', 'Rohmah Hidayah', 'Perempuan', 'Garut', 'Islam', 17, 'Angkatan II', 'Aktif'),
('AN-004', 'Isti Komah Insani', 'Perempuan', 'Cianjur', 'Islam', 16, 'Angkatan II', 'Aktif'),
('AN-005', 'Cece', 'Laki-laki', 'Sukabumi', 'Islam', 15, 'Angkatan II', 'Aktif'),
('AN-006', 'Qatrunada Mahardika', 'Perempuan', 'Karawang', 'Islam', 20, '23', 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `catatan_rapor`
--

CREATE TABLE `catatan_rapor` (
  `id` int(11) NOT NULL,
  `anak_id` varchar(20) NOT NULL,
  `periode_mulai` date NOT NULL,
  `periode_selesai` date NOT NULL,
  `catatan` text NOT NULL DEFAULT '',
  `rekomendasi` text NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evaluasi`
--

CREATE TABLE `evaluasi` (
  `id` int(11) NOT NULL,
  `anak_id` varchar(20) NOT NULL,
  `kegiatan_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `nilai` enum('sesuai','arahan','telat','tidak','libur') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluasi`
--

INSERT INTO `evaluasi` (`id`, `anak_id`, `kegiatan_id`, `tanggal`, `nilai`) VALUES
(1, 'AN-001', 1, '2026-09-24', 'sesuai'),
(2, 'AN-001', 2, '2026-09-24', 'sesuai'),
(3, 'AN-001', 3, '2026-09-24', 'arahan'),
(4, 'AN-001', 4, '2026-09-24', 'sesuai'),
(5, 'AN-001', 5, '2026-09-24', 'sesuai'),
(6, 'AN-001', 6, '2026-09-24', 'telat'),
(7, 'AN-001', 7, '2026-09-24', 'sesuai'),
(8, 'AN-001', 8, '2026-09-24', 'sesuai'),
(9, 'AN-002', 1, '2026-09-24', 'arahan'),
(10, 'AN-002', 2, '2026-09-24', 'sesuai'),
(11, 'AN-002', 3, '2026-09-24', 'sesuai'),
(12, 'AN-002', 4, '2026-09-24', 'arahan'),
(13, 'AN-002', 5, '2026-09-24', 'sesuai'),
(14, 'AN-003', 1, '2026-09-24', 'sesuai'),
(15, 'AN-003', 2, '2026-09-24', 'sesuai'),
(16, 'AN-003', 3, '2026-09-24', 'sesuai'),
(17, 'AN-003', 4, '2026-09-24', 'arahan'),
(18, 'AN-004', 1, '2026-09-24', 'sesuai'),
(19, 'AN-004', 2, '2026-09-24', 'arahan'),
(20, 'AN-004', 3, '2026-09-24', 'sesuai'),
(21, 'AN-005', 1, '2026-09-24', 'tidak'),
(22, 'AN-005', 2, '2026-09-24', 'arahan'),
(23, 'AN-005', 3, '2026-09-24', 'sesuai'),
(25, 'AN-005', 1, '2026-11-10', 'sesuai'),
(26, 'AN-002', 1, '2026-11-10', 'sesuai'),
(27, 'AN-001', 1, '2026-11-10', 'sesuai'),
(28, 'AN-004', 1, '2026-11-10', 'libur'),
(29, 'AN-006', 1, '2026-11-10', 'sesuai'),
(30, 'AN-003', 1, '2026-11-10', 'sesuai'),
(56, 'AN-004', 2, '2026-11-10', 'libur'),
(57, 'AN-004', 3, '2026-11-10', 'libur'),
(58, 'AN-004', 4, '2026-11-10', 'libur'),
(59, 'AN-004', 5, '2026-11-10', 'libur'),
(60, 'AN-004', 6, '2026-11-10', 'libur'),
(61, 'AN-004', 7, '2026-11-10', 'libur'),
(62, 'AN-004', 8, '2026-11-10', 'libur'),
(63, 'AN-004', 9, '2026-11-10', 'libur'),
(64, 'AN-004', 10, '2026-11-10', 'libur'),
(65, 'AN-004', 11, '2026-11-10', 'libur'),
(66, 'AN-004', 12, '2026-11-10', 'libur'),
(67, 'AN-004', 13, '2026-11-10', 'libur'),
(68, 'AN-004', 14, '2026-11-10', 'libur'),
(69, 'AN-004', 15, '2026-11-10', 'libur'),
(70, 'AN-004', 16, '2026-11-10', 'libur'),
(71, 'AN-004', 17, '2026-11-10', 'libur'),
(72, 'AN-004', 18, '2026-11-10', 'libur'),
(73, 'AN-004', 19, '2026-11-10', 'libur'),
(74, 'AN-004', 20, '2026-11-10', 'libur'),
(75, 'AN-004', 21, '2026-11-10', 'libur'),
(76, 'AN-004', 22, '2026-11-10', 'libur');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_kegiatan`
--

CREATE TABLE `jadwal_kegiatan` (
  `kegiatan_id` int(11) NOT NULL,
  `aturan` enum('setiap_hari','hari_kerja','hari_tertentu','multi_rentang') NOT NULL DEFAULT 'setiap_hari',
  `hari_json` text DEFAULT NULL,
  `rentang_json` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan`
--

CREATE TABLE `kegiatan` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kegiatan`
--

INSERT INTO `kegiatan` (`id`, `nama`) VALUES
(1, 'Aku Bangun Pagi Sendiri Jam 04.30'),
(16, 'Aku Mandi Sore Jam 15.30 s.d. 16.00'),
(6, 'Aku Membersihkan Tempat Tidur, Menyapu dan Mengepel Asrama Jam 07.15 s.d. 08.00'),
(17, 'Aku Mencuci Pakaian Kotor dan Menyetrika Pakaian Jam 16.00 s.d. 17.30'),
(9, 'Aku Mengikuti Kegiatan Bimbingan Batik Ecoprint Jam 08.00 s.d. 12.00'),
(14, 'Aku Mengikuti Kegiatan Bimbingan Farming Jam 13.00 s.d. 15.00'),
(8, 'Aku Mengikuti Kegiatan Bimbingan Kedisiplinan Jam 08.00 s.d. 11.00'),
(7, 'Aku Mengikuti Kegiatan Bimbingan Keterampilan Jam 08.00 s.d. 11.00'),
(10, 'Aku Mengikuti Kegiatan Bimbingan Sosial I Jam 11.00 s.d. 12.00'),
(13, 'Aku Mengikuti Kegiatan Bimbingan Sosial II Jam 13.00 s.d. 15.00'),
(19, 'Aku Mengikuti Kegiatan Makan Malam Jam 18.00 s.d. 18.30'),
(5, 'Aku Mengikuti Kegiatan Makan Pagi Jam 06.45 s.d. 07.15'),
(12, 'Aku Mengikuti Kegiatan Makan Siang Jam 12.30 s.d. 13.00'),
(4, 'Aku Mengikuti Kegiatan Mandi Pagi Jam 06.15 s.d. 06.45'),
(3, 'Aku Mengikuti Kegiatan Olahraga Jam 05.30 s.d. 06.15'),
(21, 'Aku Mengikuti Kegiatan Pengajian Jam 19.15 s.d. 20.00'),
(15, 'Aku Shalat Ashar Jam 15.00 di Masjid'),
(11, 'Aku Shalat Dzuhur Jam 12.00 di Masjid'),
(20, 'Aku Shalat Isya\' Jam 19.00 di Masjid'),
(18, 'Aku Shalat Maghrib Jam 17.45 di Masjid'),
(2, 'Aku Shalat Shubuh Jam 04.30 di Masjid'),
(22, 'Aku Tidur Tepat Waktu di Kamar Tempat Tidurku Jam 21.00');

-- --------------------------------------------------------

--
-- Table structure for table `masa_pembinaan`
--

CREATE TABLE `masa_pembinaan` (
  `angkatan` varchar(50) NOT NULL,
  `tanggal_selesai` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anak`
--
ALTER TABLE `anak`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `catatan_rapor`
--
ALTER TABLE `catatan_rapor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rapor` (`anak_id`,`periode_mulai`,`periode_selesai`);

--
-- Indexes for table `evaluasi`
--
ALTER TABLE `evaluasi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_eval` (`anak_id`,`kegiatan_id`,`tanggal`),
  ADD KEY `kegiatan_id` (`kegiatan_id`);

--
-- Indexes for table `jadwal_kegiatan`
--
ALTER TABLE `jadwal_kegiatan`
  ADD PRIMARY KEY (`kegiatan_id`);

--
-- Indexes for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama` (`nama`);

--
-- Indexes for table `masa_pembinaan`
--
ALTER TABLE `masa_pembinaan`
  ADD PRIMARY KEY (`angkatan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `catatan_rapor`
--
ALTER TABLE `catatan_rapor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `evaluasi`
--
ALTER TABLE `evaluasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8229;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `catatan_rapor`
--
ALTER TABLE `catatan_rapor`
  ADD CONSTRAINT `catatan_rapor_ibfk_1` FOREIGN KEY (`anak_id`) REFERENCES `anak` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `evaluasi`
--
ALTER TABLE `evaluasi`
  ADD CONSTRAINT `evaluasi_ibfk_1` FOREIGN KEY (`anak_id`) REFERENCES `anak` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluasi_ibfk_2` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jadwal_kegiatan`
--
ALTER TABLE `jadwal_kegiatan`
  ADD CONSTRAINT `jadwal_kegiatan_ibfk_1` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
