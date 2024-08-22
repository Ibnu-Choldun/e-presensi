-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 22, 2024 at 06:35 PM
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
-- Database: `presensigps`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `status_pengajuan` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `id_karyawan`, `keterangan`, `tanggal`, `deskripsi`, `file`, `status_pengajuan`) VALUES
(14, 14, 'Izin', '2024-08-25', 'healing', 'surat_14_20240819.png', 'APPROVED');

-- --------------------------------------------------------

--
-- Table structure for table `jabatan`
--

CREATE TABLE `jabatan` (
  `id` int(11) NOT NULL,
  `jabatan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jabatan`
--

INSERT INTO `jabatan` (`id`, `jabatan`) VALUES
(5, 'Admin'),
(9, 'Keuangan'),
(13, 'Sekretaris'),
(14, 'General Manager');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int(11) NOT NULL,
  `nik` varchar(15) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `jenis_kelamin` varchar(10) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `jabatan` varchar(50) NOT NULL,
  `foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id`, `nik`, `nama`, `jenis_kelamin`, `alamat`, `no_hp`, `jabatan`, `foto`) VALUES
(8, 'EMP-0004', 'Siti Maryam', 'Perempuan', 'Jl. Gatot Subroto No.02', '88776655421', 'Admin', 'EMP-0004.png'),
(10, 'EMP-0002', 'Rama', 'Laki-Laki', 'Jl. Sudirman No.01', '5678901234', 'Keuangan', 'EMP-0002.png'),
(11, 'EMP-0006', 'Khalid', 'Laki-Laki', 'Jl. Husni Thamrin No.11', '8877665544', 'Admin', 'EMP-0006.png'),
(14, 'EMP-0009', 'nana', 'Laki-laki', 'Jl. Rajawali No.14', '123456789009', 'Sekretaris', 'EMP-0009.png');

-- --------------------------------------------------------

--
-- Table structure for table `presensi`
--

CREATE TABLE `presensi` (
  `id` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `jam_masuk` time NOT NULL,
  `foto_masuk` varchar(255) NOT NULL,
  `tanggal_pulang` date NOT NULL,
  `jam_pulang` time NOT NULL,
  `foto_pulang` varchar(255) NOT NULL,
  `lokasi_masuk` text NOT NULL,
  `lokasi_pulang` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `presensi`
--

INSERT INTO `presensi` (`id`, `id_karyawan`, `tanggal_masuk`, `jam_masuk`, `foto_masuk`, `tanggal_pulang`, `jam_pulang`, `foto_pulang`, `lokasi_masuk`, `lokasi_pulang`) VALUES
(73, 10, '2024-08-18', '14:34:18', 'masuk_10_2024-08-18_14-34-29.png', '2024-08-18', '14:34:31', 'pulang_10_2024-08-18_14-34-33.png', '-3.0408704,104.7887872', '-3.0408704,104.7887872'),
(77, 10, '2024-08-19', '20:24:21', 'masuk_10_2024-08-19_20-33-51.png', '2024-08-19', '20:33:53', 'pulang_10_2024-08-19_20-33-59.png', '-3.0408704,104.7887872', '-3.0408704,104.7887872'),
(78, 14, '2024-08-19', '20:51:06', 'masuk_14_2024-08-19.png', '2024-08-19', '20:51:14', 'pulang_14_2024-08-19.png', '-3.0408704,104.7887872', '-3.0408704,104.7887872');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL,
  `peran` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `id_karyawan`, `username`, `password`, `status`, `peran`) VALUES
(8, 8, 'mareta123', '$2y$10$foYw98IkIoVgNM4hbkwgneKLQkwFYTLUFiO5O7iObZvCY7F0FMYii', 'Aktif', 'Admin'),
(10, 10, 'rama01', '$2y$10$oY9HKn6azswrTj47/co6b.KRpeYRrzPRetzwQKiIcnSfNOHIaZOLS', 'Aktif', 'Karyawan'),
(11, 11, 'walid', '$2y$10$MR.XYKSxxTDak4KXm1Vy4e5jUtGWt4yBZviHuTYIPP6Bzs8yAv90y', 'Aktif', 'Admin'),
(14, 14, 'nana11', '$2y$10$foYw98IkIoVgNM4hbkwgneKLQkwFYTLUFiO5O7iObZvCY7F0FMYii', 'Aktif', 'Karyawan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- Indexes for table `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `presensi`
--
ALTER TABLE `presensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `jabatan`
--
ALTER TABLE `jabatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `presensi`
--
ALTER TABLE `presensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `presensi`
--
ALTER TABLE `presensi`
  ADD CONSTRAINT `presensi_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
