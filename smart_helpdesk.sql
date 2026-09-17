-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2026 at 09:55 AM
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
-- Database: `smart_helpdesk`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Hardware & PC', 'เปิดไม่ติด จอฟ้า เครื่องดับ อุปกรณ์ชำรุด'),
(2, 'Software & OS', 'Windows ติดไวรัส โปรแกรมเปิดไม่ขึ้น'),
(3, 'Network & Internet', 'เชื่อมต่ออินเทอร์เน็ตไม่ได้ สายแลนชำรุด'),
(4, 'Printer & Devices', 'ปริ้นเตอร์ไม่ออก กระดาษติด สแกนไม่ได้');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `body` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `score` tinyint(3) UNSIGNED NOT NULL CHECK (`score` between 1 and 5),
  `feedback` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `ticket_id`, `score`, `feedback`, `created_at`) VALUES
(1, 1, 3, 'ดีมาก', '2026-09-17 14:51:39'),
(7, 2, 4, 'เยี่ยมมาก', '2026-09-17 14:54:13');

-- --------------------------------------------------------

--
-- Table structure for table `status_logs`
--

CREATE TABLE `status_logs` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `changed_by` int(11) NOT NULL,
  `from_status` enum('Open','Assigned','InProgress','Resolved','Closed') NOT NULL,
  `to_status` enum('Open','Assigned','InProgress','Resolved','Closed') NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `status_logs`
--

INSERT INTO `status_logs` (`id`, `ticket_id`, `changed_by`, `from_status`, `to_status`, `note`, `created_at`) VALUES
(1, 1, 3, '', 'Open', 'User created ticket', '2026-09-17 14:38:35'),
(2, 1, 1, 'Open', 'Assigned', NULL, '2026-09-17 14:47:08'),
(3, 1, 2, 'Assigned', 'InProgress', NULL, '2026-09-17 14:47:30'),
(4, 1, 2, 'InProgress', 'Resolved', 'เรียบร้อย', '2026-09-17 14:47:43'),
(5, 1, 3, 'Resolved', 'Closed', 'ผู้ใช้ประเมินและปิดงาน', '2026-09-17 14:51:39'),
(6, 2, 3, '', 'Open', 'User created ticket', '2026-09-17 14:52:24'),
(7, 2, 1, 'Open', 'Assigned', NULL, '2026-09-17 14:52:35'),
(8, 2, 2, 'Assigned', 'InProgress', NULL, '2026-09-17 14:52:48'),
(9, 2, 2, 'InProgress', 'Resolved', 'เรียบร้อย', '2026-09-17 14:53:28'),
(10, 2, 3, 'Resolved', 'InProgress', 'ยังไม่ได้', '2026-09-17 14:53:45'),
(11, 2, 2, 'InProgress', 'Resolved', 'เรียบร้อย', '2026-09-17 14:53:58'),
(12, 2, 3, 'Resolved', 'Closed', 'ผู้ใช้ประเมินและปิดงาน', '2026-09-17 14:54:13');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `technician_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('Open','Assigned','InProgress','Resolved','Closed') NOT NULL DEFAULT 'Open',
  `priority` enum('Low','Medium','High','Urgent') NOT NULL DEFAULT 'Medium',
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resolved_at` datetime DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `user_id`, `category_id`, `technician_id`, `title`, `description`, `status`, `priority`, `image_path`, `created_at`, `updated_at`, `resolved_at`, `closed_at`) VALUES
(1, 3, 3, 2, 'เปิดไม่ติด', 'หอพักแสนสุข', 'Closed', 'High', NULL, '2026-09-17 14:38:35', '2026-09-17 14:51:39', '2026-09-17 09:47:43', '2026-09-17 09:51:39'),
(2, 3, 1, 2, 'จอฟ้ายาวไม่ดับ', 'หอพักแสนสุข', 'Closed', 'Medium', NULL, '2026-09-17 14:52:24', '2026-09-17 14:54:13', '2026-09-17 09:53:58', '2026-09-17 09:54:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','technician','admin') NOT NULL DEFAULT 'user',
  `line_user_id` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `line_user_id`, `created_at`) VALUES
(1, 'Admin IT Support', 'admin@helpdesk.local', '$2y$10$wE0vjI7n5yQyB9yP8B1XjeC8nN5D8t6XyK7qP9zM6vY8rQ7kO8vOe', 'admin', NULL, '2026-09-17 14:10:11'),
(2, 'ช่างซ่อม สมชาย (IT Tech)', 'tech1@helpdesk.local', '$2y$10$eA3f7pDqL8Hj/YxU2wR/A.WvGqTfxGv7Csm6J41v81mJqEfZW02kG', 'technician', NULL, '2026-09-17 14:10:11'),
(3, 'สมศรี ผู้ใช้ทั่วไป (User)', 'user1@helpdesk.local', '$2y$10$wE0vjI7n5yQyB9yP8B1XjeC8nN5D8t6XyK7qP9zM6vY8rQ7kO8vOe', 'user', NULL, '2026-09-17 14:10:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comments_ticket` (`ticket_id`),
  ADD KEY `fk_comments_user` (`user_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_id` (`ticket_id`);

--
-- Indexes for table `status_logs`
--
ALTER TABLE `status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_logs_ticket` (`ticket_id`),
  ADD KEY `fk_logs_user` (`changed_by`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tickets_user` (`user_id`),
  ADD KEY `fk_tickets_category` (`category_id`),
  ADD KEY `fk_tickets_technician` (`technician_id`),
  ADD KEY `idx_tickets_status` (`status`),
  ADD KEY `idx_tickets_priority` (`priority`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `status_logs`
--
ALTER TABLE `status_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `fk_ratings_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `status_logs`
--
ALTER TABLE `status_logs`
  ADD CONSTRAINT `fk_logs_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_logs_user` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `fk_tickets_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tickets_technician` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tickets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
