-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2026 at 06:33 PM
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
-- Database: `concern_track`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin_dev', '$2y$10$R/0ddfTjtKqd86vGHPbZreRLVZ5aV9mUO8ZXZd4B87blxzPMzw4EO');

-- --------------------------------------------------------

--
-- Table structure for table `audit_trail`
--

CREATE TABLE `audit_trail` (
  `id` int(11) NOT NULL,
  `concern_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `actor` varchar(100) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_trail`
--

INSERT INTO `audit_trail` (`id`, `concern_id`, `action`, `actor`, `timestamp`) VALUES
(1, 6, 'Status changed to Screened', 'Admin_User', '2026-03-16 14:29:27'),
(2, 6, 'Status changed to Resolved', 'Admin_User', '2026-03-16 14:30:16'),
(3, 11, 'Status changed to Screened', 'Admin_User', '2026-03-16 16:23:56'),
(4, 11, 'Status changed to Resolved', 'Admin_User', '2026-03-16 16:24:53'),
(5, 10, 'Status changed to Read', 'Admin_User', '2026-03-16 16:34:26'),
(6, 10, 'Status changed to Read', 'Admin_User', '2026-03-16 16:35:23'),
(7, 8, 'Status changed to Read', 'Admin_User', '2026-03-16 16:35:44'),
(8, 12, 'Status changed to Read', 'Admin_User', '2026-03-16 17:07:16'),
(9, 12, 'Status changed to Screened', 'Admin_User', '2026-03-16 17:07:39'),
(10, 12, 'Status changed to Resolved', 'Admin_User', '2026-03-16 17:13:04');

-- --------------------------------------------------------

--
-- Table structure for table `concerns`
--

CREATE TABLE `concerns` (
  `id` int(11) NOT NULL,
  `ticket_id` varchar(10) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `is_anonymous` tinyint(1) DEFAULT 0,
  `student_email` varchar(100) DEFAULT NULL,
  `status` enum('Submitted','Routed','Read','Screened','Resolved','Escalated') DEFAULT 'Submitted',
  `dept_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `read_at` datetime DEFAULT NULL,
  `screened_at` datetime DEFAULT NULL,
  `priority` enum('Low','Medium','Urgent') DEFAULT 'Low'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `concerns`
--

INSERT INTO `concerns` (`id`, `ticket_id`, `category`, `description`, `attachment`, `is_anonymous`, `student_email`, `status`, `dept_id`, `created_at`, `last_updated`, `read_at`, `screened_at`, `priority`) VALUES
(1, 'TKT-9515', 'Academic', 'dwqdwq', '', 0, 'laysonjerielino@gmail.com', 'Submitted', 1, '2026-03-16 07:52:06', '2026-03-16 07:52:06', NULL, NULL, 'Low'),
(2, 'TKT-6858', 'Academic', 'dwqdwq', '', 0, 'laysonjerielino@gmail.com', 'Submitted', 1, '2026-03-16 08:09:30', '2026-03-16 08:09:30', NULL, NULL, 'Low'),
(3, 'TKT-2261', 'Academic', 'dwqdwq', '', 0, 'laysonjerielino@gmail.com', 'Submitted', 1, '2026-03-16 08:13:38', '2026-03-16 08:13:38', NULL, NULL, 'Low'),
(4, 'WE-16-44F1', 'Welfare', 'Subject: \n\n', NULL, 0, NULL, 'Submitted', 3, '2026-03-16 08:15:37', '2026-03-16 08:15:37', NULL, NULL, 'Low'),
(5, 'AC-16-CC65', 'Academic', 'Subject: VDZV\n\nZXVZX', NULL, 0, 'laysonjerielino@gmail.com', 'Submitted', 1, '2026-03-16 08:21:22', '2026-03-16 08:21:22', NULL, NULL, 'Low'),
(6, 'FI-16-C987', 'Financial', 'Subject: VDZV\n\nZXVZX', NULL, 0, 'laysonjerielino@gmail.com', 'Resolved', 2, '2026-03-16 08:24:01', '2026-03-16 14:30:16', NULL, '2026-03-16 22:29:27', 'Low'),
(7, 'FI-16-2625', 'Financial', 'Subject: Tuition & Fees\n\nhi, my name is ken and i want my tf back', 'file_69b824a613bc29.33800660.jpg', 1, 'ken@gmail.com', 'Routed', 2, '2026-03-16 15:41:26', '2026-03-16 15:41:26', NULL, NULL, 'Medium'),
(8, 'FI-16-C332', 'Financial', 'Subject: Tuition & Fees\n\ni want my tf back', NULL, 1, 'ken@gmail.com', 'Read', 2, '2026-03-16 15:47:07', '2026-03-16 16:35:44', '2026-03-17 00:35:44', NULL, 'Medium'),
(9, 'WE-16-8173', 'Welfare', 'Subject: Bullying / Harassment\n\nninanakaw po ung baon ko sa bag, may proof po ako', 'file_69b827946ab575.05108265.jpg', 0, 'ken@gmail.com', 'Escalated', 3, '1997-03-11 15:53:56', '2026-03-16 16:32:14', NULL, NULL, 'Urgent'),
(10, 'FI-16-3E0A', 'Financial', 'Subject: Tuition & Fees\n\nmagkano po tuition fee? BSIT po', NULL, 0, 'laysonjerielino@gmail.com', 'Read', 2, '2026-03-16 16:16:13', '2026-03-16 16:35:23', '2026-03-17 00:35:23', NULL, 'Low'),
(11, 'FI-16-5551', 'Financial', 'Subject: Tuition & Fees\n\nmagkano po tuition fee? BSIT po', NULL, 0, 'laysonjerielino@gmail.com', 'Resolved', 2, '2026-03-16 16:21:48', '2026-03-16 16:24:53', NULL, '2026-03-17 00:23:56', 'Low'),
(12, 'WE-16-B9E6', 'Welfare', 'Subject: Mental Health\n\nanxienty', NULL, 1, 'laysonjerielino@gmail.com', 'Resolved', 3, '2026-03-16 17:06:15', '2026-03-16 17:13:04', '2026-03-17 01:07:16', '2026-03-17 01:07:39', 'Urgent');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `email`) VALUES
(1, 'Academic', 'academic.dept@school.edu'),
(2, 'Financial', 'finance.dept@school.edu'),
(3, 'Welfare', 'guidance.dept@school.edu');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_number` varchar(50) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_number`, `full_name`, `email`, `password`, `created_at`) VALUES
(1, 'ken123', 'ken', 'ken@gmail.com', '$2y$10$wxFAikXNxlbCytgbv5Am/.zMbWKeNLvusFfvrlavNX7K/RHzrXplW', '2026-03-16 10:54:11'),
(2, '2024-07-00663', 'Layson, Jeriel Ino G.', 'laysonjerielino@gmail.com', '$2y$10$lwpN3ziGL0vJ0pzt7T5GFekvZWRn71nRBbaNAHYU6Cqxwq9TxmMY2', '2026-03-16 16:15:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `audit_trail`
--
ALTER TABLE `audit_trail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `concerns`
--
ALTER TABLE `concerns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_id` (`ticket_id`),
  ADD KEY `dept_id` (`dept_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_number` (`student_number`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `audit_trail`
--
ALTER TABLE `audit_trail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `concerns`
--
ALTER TABLE `concerns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `concerns`
--
ALTER TABLE `concerns`
  ADD CONSTRAINT `concerns_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`id`);

ALTER TABLE concerns 
ADD COLUMN routed_to VARCHAR(100),
ADD COLUMN last_action_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD COLUMN is_escalated TINYINT(1) DEFAULT 0;


COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
