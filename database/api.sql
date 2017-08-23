-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 23, 2017 at 01:22 PM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `api`
--

-- --------------------------------------------------------

--
-- Table structure for table `keys`
--

CREATE TABLE `keys` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `key` varchar(40) NOT NULL,
  `level` int(2) NOT NULL,
  `ignore_limits` tinyint(1) NOT NULL DEFAULT '0',
  `is_private_key` tinyint(1) NOT NULL DEFAULT '0',
  `ip_addresses` text,
  `date_created` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `keys`
--

INSERT INTO `keys` (`id`, `user_id`, `key`, `level`, `ignore_limits`, `is_private_key`, `ip_addresses`, `date_created`) VALUES
(1, 1, '12345', 1, 0, 0, NULL, 2017);

-- --------------------------------------------------------

--
-- Table structure for table `m_siswa`
--

CREATE TABLE `m_siswa` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `jenis_kelamin` varchar(30) NOT NULL,
  `agama` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_siswa`
--

INSERT INTO `m_siswa` (`id`, `name`, `jenis_kelamin`, `agama`) VALUES
(1, 'ibnu', 'Laki laki', 'Islam'),
(2, 'Warsika', 'Laki laki', 'Islam');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `keys`
--
ALTER TABLE `keys`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `keys`
--
ALTER TABLE `keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
