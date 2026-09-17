-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2026 at 06:41 AM
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
-- Database: `smart_helpdesk`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'คอมพิวเตอร์และอุปกรณ์ต่อพ่วง'),
(2, 'ระบบเครือข่ายและอินเทอร์เน็ต'),
(3, 'โปรแกรมและซอฟต์แวร์');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(11) NOT NULL,
  `building` varchar(100) NOT NULL,
  `room` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `building`, `room`) VALUES
(1, 'อาคาร 1 (ตึกเรียนรวม)', 'ห้องคอมพิวเตอร์ 101'),
(2, 'อาคาร 1 (ตึกเรียนรวม)', 'ห้องพักครู 202'),
(3, 'อาคาร 2 (ตึกอำนวยการ)', 'ห้องประชุมใหญ่');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `technician_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `priority` enum('Low','Medium','High','Urgent') DEFAULT 'Medium',
  `status` enum('Open','Assigned','InProgress','Resolved','Closed') DEFAULT 'Open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `user_id`, `category_id`, `location_id`, `technician_id`, `title`, `description`, `priority`, `status`, `created_at`) VALUES
(1, 1, 1, 2, NULL, 'คอมไม่ติด', 'โกรธผัวเลยพังคอม', 'Medium', 'Closed', '2026-09-17 03:19:43'),
(2, 1, 1, 1, NULL, 'คอมดับเปืดไม่ติด', 'โดนผัวตบโดนคอมเลยดับ', 'Medium', 'Closed', '2026-09-17 03:29:40'),
(3, 1, 1, 1, NULL, 'คอมพัง', 'โง่', 'Medium', 'Closed', '2026-09-17 03:36:55'),
(4, 1, 2, 2, NULL, 'ชนพัง', 'ไม่มี', 'Medium', 'Closed', '2026-09-17 03:38:38'),
(5, 1, 1, 1, NULL, 'พัง', 'ไม่มี', 'Medium', 'Closed', '2026-09-17 03:42:31'),
(6, 1, 3, 3, NULL, 'พัง', 'ไม่มี', 'High', 'Closed', '2026-09-17 03:46:32'),
(7, 1, 1, 1, NULL, 'พัง', 'พัง', 'Medium', 'Closed', '2026-09-17 03:56:13'),
(8, 4, 1, 1, NULL, 'ดับ', 'ไม่มี', 'Medium', 'Closed', '2026-09-17 03:58:23'),
(9, 4, 1, 1, NULL, 'พัง', 'พัง', 'Medium', 'Closed', '2026-09-17 04:10:14'),
(10, 4, 1, 1, NULL, 'พัง', 'พัง', 'Medium', 'Closed', '2026-09-17 04:13:00'),
(11, 4, 2, 3, NULL, 'พัง', 'ไม่มี', 'Medium', 'Closed', '2026-09-17 04:29:53'),
(12, 4, 1, 3, NULL, 'คอมไม่ติด', 'ไม่มี', 'Medium', 'Closed', '2026-09-17 04:40:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('User','Technician','Admin') NOT NULL DEFAULT 'User'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`) VALUES
(1, 'ผู้ใช้งาน ทดสอบ', 'user@test.com', 'User'),
(2, 'ช่างไอที สมชาย', 'tech@test.com', 'Technician'),
(3, 'หัวหน้าไอที แอดมิน', 'admin@test.com', 'Admin'),
(4, 'ปาล์มมี่', '674230008@webmail.npru.ac.th', 'User');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `location_id` (`location_id`),
  ADD KEY `technician_id` (`technician_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `tickets_ibfk_4` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
