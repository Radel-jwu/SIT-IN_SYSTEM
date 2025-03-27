-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 28, 2025 at 12:48 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `studentinfo`
--

-- --------------------------------------------------------

--
-- Table structure for table `studentinfo`
--

CREATE TABLE `studentinfo` (
  `idno` int(10) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) NOT NULL,
  `course` varchar(50) NOT NULL,
  `year_lvl` int(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `session` int(30) NOT NULL,
  `address` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studentinfo`
--

INSERT INTO `studentinfo` (`idno`, `lastname`, `firstname`, `middlename`, `course`, `year_lvl`, `email`, `username`, `password`, `session`, `address`) VALUES
(1, '1', '1', '1', 'BSIT', 3, 'admin@gmail.com', 'admin', 'admin', 30, 'admin'),
(1000, 'Munoz', 'Annelob', '', 'BSIT', 3, 'annelob@gmail.com', 'annelob', 'annelob', 26, '123'),
(22017013, 'Abao', 'Christopher', '', 'BSIT', 3, 'abao@gmail.com', 'abaoskie1432', 'abaoskie14U', 23, 'Sitio Kalunasan Cebu City'),
(22217013, 'Pagente', 'Chrisnyonin', 'Generale', 'BSIT', 3, 'nyoninwallet@gmail.com', 'nyoninesports', 'nyoisgood', 26, 'Basak Bardo Cebu City'),
(22517013, 'test', 'test', 'test', 'BSCS', 3, 'test@gmail.com', 'test', 'test123', 26, 'test address'),
(22617013, 'Padoga', 'Ranidel', 'Luzon', 'BSIT', 3, 'ranidelpadoga@gmail.com', 'assalt122', 'Rpadoga25', 21, 'Sitio Tugas, Mambaling');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `studentinfo`
--
ALTER TABLE `studentinfo`
  ADD PRIMARY KEY (`idno`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
