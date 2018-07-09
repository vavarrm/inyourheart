-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2018 at 11:26 AM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qr.sihalive`
--

-- --------------------------------------------------------

--
-- Table structure for table `tuktuk`
--

CREATE TABLE `tuktuk` (
  `id` int(11) NOT NULL,
  `phone` varchar(13) NOT NULL,
  `status` enum('unlock','lock') NOT NULL DEFAULT 'unlock',
  `name` varchar(50) NOT NULL,
  `password` varchar(200) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tuktuk`
--

INSERT INTO `tuktuk` (`id`, `phone`, `status`, `name`, `password`, `date_created`) VALUES
(1, '12345678', 'unlock', '33333', '', '2018-04-25 06:47:58'),
(2, 'wewew', 'unlock', 'wewe', '', '2018-04-25 06:47:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tuktuk`
--
ALTER TABLE `tuktuk`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tuktuk`
--
ALTER TABLE `tuktuk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
