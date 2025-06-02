-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 30, 2025 at 09:09 AM
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
-- Database: `latestelib`
--

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department_name`) VALUES
(1, 'ICTD'),
(2, 'BDD'),
(3, 'ACCOUNTING'),
(4, 'BRANCH');

-- --------------------------------------------------------

--
-- Table structure for table `pdf_files`
--

CREATE TABLE `pdf_files` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `archived` tinyint(1) DEFAULT 0,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `department_id` int(11) DEFAULT NULL,
  `target_users` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pdf_files`
--

INSERT INTO `pdf_files` (`id`, `file_name`, `file_path`, `uploaded_by`, `archived`, `uploaded_at`, `department_id`, `target_users`) VALUES
(2, 'sample - Copy.pdf', '../uploads/sample - Copy.pdf', 1, 0, '2025-05-30 01:25:44', 2, '2'),
(3, 'sample.pdf', '../uploads/sample.pdf', 5, 0, '2025-05-30 06:26:26', 4, '3,4');

-- --------------------------------------------------------

--
-- Table structure for table `pdf_log`
--

CREATE TABLE `pdf_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `pdf_file_id` int(11) DEFAULT NULL,
  `view_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pdf_log`
--

INSERT INTO `pdf_log` (`id`, `user_id`, `pdf_file_id`, `view_time`) VALUES
(1, 2, 1, '2025-05-30 01:15:16'),
(2, 2, 1, '2025-05-30 01:15:29'),
(3, 2, 1, '2025-05-30 01:16:15'),
(4, 2, 1, '2025-05-30 01:16:15'),
(5, 2, 1, '2025-05-30 01:16:16'),
(6, 2, 1, '2025-05-30 01:16:16'),
(7, 2, 1, '2025-05-30 01:16:16'),
(8, 2, 1, '2025-05-30 01:16:16'),
(9, 2, 1, '2025-05-30 01:16:17'),
(10, 2, 2, '2025-05-30 01:43:54'),
(11, 2, 2, '2025-05-30 02:53:03'),
(12, 2, 2, '2025-05-30 03:13:44'),
(13, 2, 2, '2025-05-30 03:48:54'),
(14, 2, 2, '2025-05-30 03:57:58'),
(15, 2, 2, '2025-05-30 04:03:30'),
(16, 3, 3, '2025-05-30 06:32:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin','super_admin') DEFAULT 'user',
  `department_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `password_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `department_id`, `created_at`, `is_active`, `password_expiry`) VALUES
(1, 'ictd', '$2y$10$XYeLJBVPdheJ0A1ckat59OuJWnJ5XGOgEScW5emT/iOaOojjXI5EK', 'super_admin', 1, '2025-05-31 01:08:39', 1, '2025-09-28 10:08:33'),
(2, 'bdd', '$2y$10$o1fYhx586c/ypy0KL2.bsezC.dyHqsBrq7DaR2KMnDlfiP.NAE0gO', 'user', 2, '2025-05-30 01:12:18', 1, '2025-06-30 10:09:06'),
(3, 'alicia', '$2y$10$x4uQtZh.P1QwbTmCrBXnMOaAefn/pbuMvw2FbMdnt2wD2yB9PnWQK', 'user', 4, '2025-05-30 02:16:04', 1, '2025-08-28 04:16:04'),
(4, 'aparri', '$2y$10$JuTCcLcrJUqpDQNhooxK6.RiKEdBgV6hz7/QqfXnOzMSFVKOA7tZy', 'user', 4, '2025-05-30 03:08:49', 1, '2025-08-28 05:08:49'),
(5, 'ictd_systemadmin', '$2y$10$zs/dXfKEb9D7yWc9ni2ucOWwgkX0fiftEHRZSt3hzme2cpBV653TK', 'admin', 1, '2025-05-30 03:15:53', 1, '2025-08-28 05:15:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pdf_files`
--
ALTER TABLE `pdf_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pdf_log`
--
ALTER TABLE `pdf_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pdf_files`
--
ALTER TABLE `pdf_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pdf_log`
--
ALTER TABLE `pdf_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
