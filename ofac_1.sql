-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 21, 2025 at 09:42 PM
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
-- Database: `ofac_1`
--

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `eid` varchar(100) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `added_by` varchar(255) NOT NULL,
  `time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`fname`, `lname`, `eid`, `designation`, `password`, `status`, `added_by`, `time`) VALUES
('Chayan', 'Das', 'daschayan267@gmail.com', 'user', '$2y$10$rzDozs2/5cqlhyX/jozII.Hf.8PWIiqlKTVqdVXsxJBIGRmCYc81W', 'active', 'soujatyabhunia2003@gmail.com', '2025-02-04 11:22:37'),
('Nabhajit', 'Roy', 'nabhajit93@gmail.com', 'user', '$2y$10$SeV1RfL3F3DbYCD1V3skbeiBCfaluYbUHYVHpuyz4QWYfrYzlz9kq', 'active', 'soujatyabhunia2003@gmail.com', '2025-02-08 19:31:34'),
('Souharda', 'Biswas', 'souhardashikharbiswas@gmail.com', 'admin', '$2y$10$RFTSUdOH2udTDsbq3nuz9uedD9eZ7ASOw8oSXPXK.dvfhdb79jTwi', 'active', 'soujatyabhunia2003@gmail.com', '2025-02-04 03:08:32'),
('Soujatya', 'B', 'soujatya2003bhunia@gmail.com', 'user', '$2y$10$lCmR9mQIxREFxa109WqwL.KDDUvYKWPssOFcyfWQI6CcTp.ptLuO6', 'active', 'soujatyabhunia2003@gmail.com', '2025-03-20 10:44:29'),
('Soujatya', 'Bhunia', 'soujatyabhunia2003@gmail.com', 'admin', '$2y$10$WIlLfT0mdkRH/JDDqRP9I.Kz4yq/4gh.OxYOi9ELpc4eh/jXReCly', 'active', 'basu.mukharjee@admin.ofac.com', '2025-02-03 23:57:23'),
('Susmik', 'Maity', 'sunsusmik8638@gmail.com', 'admin', '$2y$10$fwlwTiKvYtMEceYetIev.Ol1jh7A5nEc0Yp2g0x9JPnznRW.mE1Gu', 'active', 'soujatyabhunia2003@gmail.com', '2025-03-16 15:09:31'),
('Suvendu', 'Adhikary', 'suvendudashadhikary@gmail.com', 'admin', '$2y$10$/wS7L4.QjKXWBqm54VSMtuG0.zW1mvEFQYOqVsoLcebzPHiZ7Z2JS', 'active', 'soujatyabhunia2003@gmail.com', '2025-02-04 11:33:15');

-- --------------------------------------------------------

--
-- Table structure for table `employee_activity`
--

CREATE TABLE `employee_activity` (
  `sl_no` int(255) NOT NULL,
  `eid` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_activity`
--

INSERT INTO `employee_activity` (`sl_no`, `eid`, `fname`, `lname`, `login_time`, `logout_time`) VALUES
(170, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-02-07 10:13:16', '2025-02-07 10:13:27'),
(172, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-02-07 10:19:09', '2025-02-07 10:58:10'),
(173, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-02-07 10:59:07', '2025-02-07 10:59:39'),
(174, 'soujatya2003bhunia@gmail.com', 'Soujatya', 'Bhunia', '2025-02-07 10:59:50', '2025-02-07 11:00:18'),
(176, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-02-07 11:18:44', '2025-02-07 11:23:02'),
(177, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-02-07 11:23:05', '2025-02-07 11:23:09'),
(178, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-02-07 11:23:18', '2025-02-07 11:23:19'),
(181, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-02-07 19:57:45', '2025-02-07 20:26:17'),
(189, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-02-08 18:24:02', '2025-02-08 18:35:56'),
(190, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-02-08 18:39:12', '2025-02-08 18:39:25'),
(191, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-02-08 18:40:05', '2025-02-08 18:42:24'),
(192, 'soujatya2003bhunia@gmail.com', 'Soujatya', 'Bhunia', '2025-02-08 18:42:38', '2025-02-08 18:48:02'),
(193, 'soujatya2003bhunia@gmail.com', 'Soujatya', 'ABC', '2025-02-08 18:48:27', '2025-02-08 18:48:57'),
(208, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-15 05:21:19', '2025-03-15 06:12:53'),
(209, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-15 06:12:59', '2025-03-15 06:18:37'),
(211, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-15 06:23:40', '2025-03-15 06:26:13'),
(212, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-15 06:26:19', '2025-03-15 06:29:56'),
(213, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-15 10:33:48', '2025-03-15 11:02:00'),
(215, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-16 12:43:56', '2025-03-16 12:46:20'),
(217, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-16 14:07:12', '2025-03-16 14:59:08'),
(218, 'soujatya2003bhunia@gmail.com', 'Soujatya', 'B', '2025-03-16 14:59:41', '2025-03-16 14:59:52'),
(220, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-16 17:00:21', '2025-03-16 17:01:14'),
(222, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-16 17:22:56', '2025-03-16 17:24:37'),
(226, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-16 20:18:39', NULL),
(227, 'soujatyabhunia2003@gmail.com', '                            Soujatya', 'Bhunia', '2025-03-17 09:53:49', '2025-03-17 09:56:11'),
(228, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-20 07:18:08', NULL),
(229, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-20 08:28:33', '2025-03-20 08:55:05'),
(230, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-20 09:54:01', NULL),
(231, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-21 10:19:20', NULL),
(232, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-21 11:19:29', NULL),
(233, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-21 11:32:50', NULL),
(234, 'soujatya2003bhunia@gmail.com', 'Soujatya', 'B', '2025-03-21 16:16:15', NULL),
(235, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-21 16:20:56', NULL),
(236, 'soujatya2003bhunia@gmail.com', 'Soujatya', 'B', '2025-03-21 17:25:42', NULL),
(237, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-21 17:45:27', '2025-03-21 17:46:15'),
(238, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-21 18:49:17', '2025-03-21 19:36:25'),
(239, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-21 19:38:04', NULL),
(240, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-21 20:37:59', '2025-03-21 20:46:40'),
(241, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-21 20:46:47', NULL),
(242, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-22 00:57:20', '2025-03-22 01:56:29'),
(243, 'soujatyabhunia2003@gmail.com', 'Soujatya', 'Bhunia', '2025-03-22 02:02:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `master_list_edit_history`
--

CREATE TABLE `master_list_edit_history` (
  `business_name` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `reg_no` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_list_edit_history`
--

INSERT INTO `master_list_edit_history` (`business_name`, `owner`, `address`, `reg_no`, `status`) VALUES
('TechCorp Solutions', 'John Doe', '123 Elm Street', 'REG99', 'Not Eligible');

-- --------------------------------------------------------

--
-- Table structure for table `master_list_upload_history`
--

CREATE TABLE `master_list_upload_history` (
  `id` int(11) NOT NULL,
  `uploaded_by` varchar(255) NOT NULL,
  `file_id` int(255) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_list_upload_history`
--

INSERT INTO `master_list_upload_history` (`id`, `uploaded_by`, `file_id`, `uploaded_at`) VALUES
(68, 'soujatyabhunia2003@gmail.com', 1, '2025-03-22 01:21:31'),
(69, 'soujatyabhunia2003@gmail.com', 2, '2025-03-22 01:50:18');

-- --------------------------------------------------------

--
-- Table structure for table `master_list_versions`
--

CREATE TABLE `master_list_versions` (
  `sl_no` int(10) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `reg_no` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL,
  `uploaded_by` varchar(255) NOT NULL,
  `uploaded_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_list_versions`
--

INSERT INTO `master_list_versions` (`sl_no`, `business_name`, `owner`, `address`, `reg_no`, `status`, `uploaded_by`, `uploaded_at`) VALUES
(35, 'TechCorp Solutions', 'John Doe', '123 Elm Street', 'REG99', 'Eligible', 'soujatyabhunia2003@gmail.com', '2025-03-22 01:53:02');

-- --------------------------------------------------------

--
-- Table structure for table `ofac_master`
--

CREATE TABLE `ofac_master` (
  `business_name` varchar(200) NOT NULL,
  `owner` varchar(200) NOT NULL,
  `address` varchar(500) NOT NULL,
  `reg_no` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL,
  `file_id` int(255) NOT NULL,
  `date` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `eid` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ofac_master`
--

INSERT INTO `ofac_master` (`business_name`, `owner`, `address`, `reg_no`, `status`, `file_id`, `date`, `eid`) VALUES
('Business_1', 'Owner_1', '1 Main Street City_1', 'REG-1001', 'Eligible', 1, '2025-03-22 01:21:31.425606', 'soujatyabhunia2003@gmail.com'),
('Business_2', 'Owner_2', '2 Main Street City_2', 'REG-1002', 'Eligible', 1, '2025-03-22 01:21:31.431015', 'soujatyabhunia2003@gmail.com'),
('Business_3', 'Owner_3', '3 Main Street City_3', 'REG-1003', 'Eligible', 1, '2025-03-22 01:21:31.435930', 'soujatyabhunia2003@gmail.com'),
('Business_4', 'Owner_4', '4 Main Street City_4', 'REG-1004', 'Eligible', 1, '2025-03-22 01:21:31.439876', 'soujatyabhunia2003@gmail.com'),
('Business_5', 'Owner_5', '5 Main Street City_5', 'REG-1005', 'Eligible', 1, '2025-03-22 01:21:31.444008', 'soujatyabhunia2003@gmail.com'),
('Business_6', 'Owner_6', '6 Main Street City_6', 'REG-1006', 'Eligible', 1, '2025-03-22 01:21:31.448017', 'soujatyabhunia2003@gmail.com'),
('Business_7', 'Owner_7', '7 Main Street City_7', 'REG-1007', 'Eligible', 1, '2025-03-22 01:21:31.452399', 'soujatyabhunia2003@gmail.com'),
('Business_8', 'Owner_8', '8 Main Street City_8', 'REG-1008', 'Eligible', 1, '2025-03-22 01:21:31.456103', 'soujatyabhunia2003@gmail.com'),
('Business_9', 'Owner_9', '9 Main Street City_9', 'REG-1009', 'Eligible', 1, '2025-03-22 01:21:31.459677', 'soujatyabhunia2003@gmail.com'),
('Business_10', 'Owner_10', '10 Main Street City_10', 'REG-1010', 'Eligible', 1, '2025-03-22 01:21:31.463625', 'soujatyabhunia2003@gmail.com'),
('Business_11', 'Owner_11', '11 Main Street City_11', 'REG-1011', 'Eligible', 1, '2025-03-22 01:21:31.468788', 'soujatyabhunia2003@gmail.com'),
('Business_12', 'Owner_12', '12 Main Street City_12', 'REG-1012', 'Eligible', 1, '2025-03-22 01:21:31.472914', 'soujatyabhunia2003@gmail.com'),
('Business_13', 'Owner_13', '13 Main Street City_13', 'REG-1013', 'Eligible', 1, '2025-03-22 01:21:31.476303', 'soujatyabhunia2003@gmail.com'),
('Business_14', 'Owner_14', '14 Main Street City_14', 'REG-1014', 'Eligible', 1, '2025-03-22 01:21:31.481350', 'soujatyabhunia2003@gmail.com'),
('Business_15', 'Owner_15', '15 Main Street City_15', 'REG-1015', 'Eligible', 1, '2025-03-22 01:21:31.486673', 'soujatyabhunia2003@gmail.com'),
('Business_16', 'Owner_16', '16 Main Street City_16', 'REG-1016', 'Eligible', 1, '2025-03-22 01:21:31.491278', 'soujatyabhunia2003@gmail.com'),
('Business_17', 'Owner_17', '17 Main Street City_17', 'REG-1017', 'Eligible', 1, '2025-03-22 01:21:31.495066', 'soujatyabhunia2003@gmail.com'),
('Business_18', 'Owner_18', '18 Main Street City_18', 'REG-1018', 'Eligible', 1, '2025-03-22 01:21:31.498797', 'soujatyabhunia2003@gmail.com'),
('Business_19', 'Owner_19', '19 Main Street City_19', 'REG-1019', 'Eligible', 1, '2025-03-22 01:21:31.502757', 'soujatyabhunia2003@gmail.com'),
('Business_20', 'Owner_20', '20 Main Street City_20', 'REG-1020', 'Eligible', 1, '2025-03-22 01:21:31.506524', 'soujatyabhunia2003@gmail.com'),
('TechCorp Solutions', 'John Doe', '123 Elm Street', 'REG99', 'Not Eligible', 2, '2025-03-22 01:50:18.876384', 'soujatyabhunia2003@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `otp`
--

CREATE TABLE `otp` (
  `id` int(11) NOT NULL,
  `eid` varchar(255) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `otp_expiry` datetime NOT NULL,
  `verification_token` varchar(64) NOT NULL,
  `flag` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp`
--

INSERT INTO `otp` (`id`, `eid`, `otp_code`, `otp_expiry`, `verification_token`, `flag`, `created_at`) VALUES
(2, 'daschayan267@gmail.com', '930807', '2025-01-23 15:08:19', '5b528d53e5d6f75e1f5ac55596e78f16', 0, '2025-01-23 09:28:19'),
(3, 'daschayan267@gmail.com', '990108', '2025-01-23 15:14:32', '4ac2bdc52de293561e9c4c1eaeaedb64', 0, '2025-01-23 09:34:32'),
(4, 'daschayan267@gmail.com', '176477', '2025-01-23 15:29:24', 'a6bf3a06f04990236a2e67e0b4c6c996', 0, '2025-01-23 09:49:24'),
(5, 'daschayan267@gmail.com', '435785', '2025-01-23 15:54:33', 'fd4e525f57e32b3b2b25e14120689dc9', 0, '2025-01-23 10:14:33'),
(6, 'daschayan267@gmail.com', '326458', '2025-01-23 18:35:52', '84db1f65497d1d3ed6d693be2d6ee1d7', 0, '2025-01-23 12:55:52'),
(7, 'daschayan267@gmail.com', '885806', '2025-01-24 16:08:52', 'dcfbee14fb810117f5666bf18e2090af', 0, '2025-01-24 10:28:52'),
(9, 'daschayan267@gmail.com', '154202', '2025-01-30 14:59:33', 'd24cf081915ec99851372921e1304bd5', 0, '2025-01-30 09:19:33'),
(10, 'daschayan267@gmail.com', '932670', '2025-01-30 15:14:20', '60d4a1e41702094ba51cd4bd7fd32f0d', 0, '2025-01-30 09:34:20'),
(11, 'daschayan267@gmail.com', '220247', '2025-01-30 15:17:51', '2de9e310ad5adf11c22e16b484b4d58c', 0, '2025-01-30 09:37:51'),
(13, 'daschayan267@gmail.com', '221035', '2025-01-30 15:36:23', '152b42958066f10f3521ba1acc0b324f', 0, '2025-01-30 09:56:23'),
(17, 'soujatyabhunia2003@gmail.com', '254481', '2025-02-07 00:33:56', '86ffde1de33490b6b0e15933c6f3a1aa', 0, '2025-02-06 18:53:56'),
(18, 'soujatyabhunia2003@gmail.com', '986454', '2025-02-07 00:37:40', '65c482688e909376f26e0aed40ff20c1', 0, '2025-02-06 18:57:40');

-- --------------------------------------------------------

--
-- Table structure for table `pending_checks_resolve`
--

CREATE TABLE `pending_checks_resolve` (
  `sl_no` int(255) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `reg_no` varchar(50) NOT NULL,
  `new_status` varchar(20) NOT NULL,
  `resolved_by` varchar(255) NOT NULL,
  `resolved_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `skipped_master_list`
--

CREATE TABLE `skipped_master_list` (
  `sno` int(11) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `reg_no` varchar(20) NOT NULL,
  `status` varchar(100) NOT NULL,
  `uploaded_by` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `upload_history`
--

CREATE TABLE `upload_history` (
  `id` int(11) NOT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `owner` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `reg_no` varchar(100) DEFAULT NULL,
  `uploaded_by` varchar(255) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `upload_history`
--

INSERT INTO `upload_history` (`id`, `business_name`, `owner`, `address`, `reg_no`, `uploaded_by`, `status`, `uploaded_at`) VALUES
(15, 'TechCorp Solutions', 'John Doe', '123 Elm Street', 'REG99', 'soujatyabhunia2003@gmail.com', 'Eligible', '2025-03-21 20:20:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`eid`);

--
-- Indexes for table `employee_activity`
--
ALTER TABLE `employee_activity`
  ADD PRIMARY KEY (`sl_no`),
  ADD UNIQUE KEY `1` (`sl_no`);

--
-- Indexes for table `master_list_edit_history`
--
ALTER TABLE `master_list_edit_history`
  ADD PRIMARY KEY (`reg_no`);

--
-- Indexes for table `master_list_upload_history`
--
ALTER TABLE `master_list_upload_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `master_list_versions`
--
ALTER TABLE `master_list_versions`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `ofac_master`
--
ALTER TABLE `ofac_master`
  ADD PRIMARY KEY (`reg_no`);

--
-- Indexes for table `otp`
--
ALTER TABLE `otp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eid` (`eid`);

--
-- Indexes for table `pending_checks_resolve`
--
ALTER TABLE `pending_checks_resolve`
  ADD PRIMARY KEY (`reg_no`),
  ADD UNIQUE KEY `sl_no` (`sl_no`);

--
-- Indexes for table `skipped_master_list`
--
ALTER TABLE `skipped_master_list`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `upload_history`
--
ALTER TABLE `upload_history`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employee_activity`
--
ALTER TABLE `employee_activity`
  MODIFY `sl_no` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=244;

--
-- AUTO_INCREMENT for table `master_list_upload_history`
--
ALTER TABLE `master_list_upload_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `master_list_versions`
--
ALTER TABLE `master_list_versions`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `otp`
--
ALTER TABLE `otp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `pending_checks_resolve`
--
ALTER TABLE `pending_checks_resolve`
  MODIFY `sl_no` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `skipped_master_list`
--
ALTER TABLE `skipped_master_list`
  MODIFY `sno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `upload_history`
--
ALTER TABLE `upload_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `otp`
--
ALTER TABLE `otp`
  ADD CONSTRAINT `otp_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `employees` (`eid`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
