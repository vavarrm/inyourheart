-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2018 at 07:01 AM
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
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `pe_id` int(5) NOT NULL,
  `pe_name` varchar(50) NOT NULL,
  `pe_control` varchar(100) NOT NULL,
  `pe_func` varchar(100) NOT NULL,
  `pe_type` enum('menu','action','button') NOT NULL DEFAULT 'menu',
  `add_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pe_parents_id` int(5) NOT NULL DEFAULT '0',
  `pe_page` varchar(100) DEFAULT NULL,
  `pe_order_id` int(2) NOT NULL DEFAULT '0',
  `pe_icon` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`pe_id`, `pe_name`, `pe_control`, `pe_func`, `pe_type`, `add_datetime`, `pe_parents_id`, `pe_page`, `pe_order_id`, `pe_icon`) VALUES
(1, 'QR management', 'AdminQr', '', 'menu', '2018-04-07 08:33:48', 0, NULL, 99, NULL),
(2, 'list', 'AdminQr', 'getList', 'menu', '2018-04-07 08:34:23', 1, 'table_list', 0, NULL),
(3, 'TukTuk Manager', 'AdminTukTuk', '', 'menu', '2018-04-24 02:34:18', 0, NULL, 0, NULL),
(4, 'Tuk Tuk List', 'AdminTukTuk', 'getList', 'menu', '2018-04-24 02:46:45', 3, 'table_list', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`pe_id`),
  ADD UNIQUE KEY `pe_control` (`pe_control`,`pe_func`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `pe_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
