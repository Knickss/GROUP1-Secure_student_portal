-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 24, 2025 at 03:04 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `escolink_centra`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `details` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `status` enum('success','failed') DEFAULT NULL,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`log_id`, `user_id`, `action`, `details`, `ip_address`, `role`, `status`, `timestamp`) VALUES
(1, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-22 01:47:40'),
(2, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-22 01:47:43'),
(3, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-22 01:55:41'),
(4, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-22 01:55:45'),
(5, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '127.0.0.1', 'admin', 'success', '2025-11-22 01:55:53'),
(6, 1, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'student', 'success', '2025-11-22 02:13:52'),
(7, 1, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'student', 'success', '2025-11-22 02:16:23'),
(8, 2, 'Login Failed', 'Wrong password entered.', '127.0.0.1', 'teacher', 'failed', '2025-11-22 02:16:41'),
(9, 2, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'teacher', 'success', '2025-11-22 02:16:54'),
(10, 2, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'teacher', 'success', '2025-11-22 02:17:01'),
(11, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-22 02:17:13'),
(12, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-22 02:17:16'),
(13, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '127.0.0.1', 'admin', 'success', '2025-11-22 02:17:27'),
(14, 3, 'Edited User', 'Edited user ID 4 (Jose Rizal Ponce, username: joserizal, role: student) without password change.', '127.0.0.1', 'admin', 'success', '2025-11-22 02:41:21'),
(15, 3, 'Created User', 'Created user \'Michael Stevens\' (username: Vsauce, role: teacher).', '127.0.0.1', 'admin', 'success', '2025-11-22 02:44:10'),
(16, 3, 'Edited User', 'Edited user ID 4 (Jose Rizal Ponce, username: joserizal, role: student) with password change.', '127.0.0.1', 'admin', 'success', '2025-11-22 02:45:30'),
(17, 3, 'Updated Student Info', 'Updated academic info for student user_id 1 (Student ID: 2025-01, Program: Rocket Science, Year: 3).', '127.0.0.1', 'admin', 'success', '2025-11-22 02:58:08'),
(18, 3, 'Added Faculty Info', 'Created faculty info for user_id 5 (Teacher ID: PROF-222, Department ID: 3).', '127.0.0.1', 'admin', 'success', '2025-11-22 03:08:38'),
(19, 3, 'Updated Course', 'Updated course HIST-2022 (Adrestian Military History), Units=3, Semester=1st Semester, Day=Tue & Thu, Time=1:00–3:30 PM, TeacherID=5.', '127.0.0.1', 'admin', 'success', '2025-11-22 03:17:17'),
(20, 3, 'Updated Enrollments', 'Assigned students [1, 4] to HIST-2022 - Adrestian Military History.', '127.0.0.1', 'admin', 'success', '2025-11-22 03:23:30'),
(21, 3, 'Added Program', 'Created new program \'General Biology\'.', '127.0.0.1', 'admin', 'success', '2025-11-22 03:44:58'),
(22, 3, 'Added Department', 'Created new department \'Something\'.', '127.0.0.1', 'admin', 'success', '2025-11-22 03:52:03'),
(23, 3, 'Edited Department', 'Updated department ID 6 to \'Medicine\'.', '127.0.0.1', 'admin', 'success', '2025-11-22 03:52:28'),
(24, 3, 'Deleted Announcement', 'Deleted announcement \'all users, do you see this?\' (ID 11).', '127.0.0.1', 'admin', 'success', '2025-11-22 03:59:04'),
(25, 2, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'teacher', 'success', '2025-11-22 04:05:58'),
(26, 2, 'Edited Class Announcement', 'Updated announcement \'Testing for Imperial Governance course\' (ID 9) for POLSCI-101 - Imperial Governance.', '127.0.0.1', 'teacher', 'success', '2025-11-22 04:12:34'),
(27, 2, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'teacher', 'success', '2025-11-22 04:13:03'),
(28, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-22 04:13:12'),
(29, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-22 04:13:15'),
(30, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '127.0.0.1', 'admin', 'success', '2025-11-22 04:13:22'),
(31, 3, 'Updated Profile', 'Updated profile details (Name: Niccolo Abella, Email: escolinkcentra@gmail.com, Profile Pic: 1763756337_capitano1.jpg).', '127.0.0.1', 'admin', 'success', '2025-11-22 04:18:57'),
(32, 1, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'student', 'success', '2025-11-22 04:34:26'),
(33, 1, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'student', 'success', '2025-11-22 04:34:34'),
(34, 2, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'teacher', 'success', '2025-11-22 04:34:55'),
(35, 2, 'Updated Grade', 'Changed grade of student 1 for PHIL-303 - Ethics & Reform from 1.15 to 1.15.', '127.0.0.1', 'teacher', 'success', '2025-11-22 04:38:59'),
(36, 2, 'Updated Grade', 'Changed grade of student 4 for PHIL-303 - Ethics & Reform from 1.15 to 1.25.', '127.0.0.1', 'teacher', 'success', '2025-11-22 04:38:59'),
(37, 2, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'teacher', 'success', '2025-11-22 04:39:02'),
(38, 5, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'teacher', 'success', '2025-11-22 04:39:17'),
(39, 5, 'Updated Grade', 'Changed grade of student 1 for HIST-2022 - Adrestian Military History from 1.25 to 1.25.', '127.0.0.1', 'teacher', 'success', '2025-11-22 04:39:29'),
(40, 5, 'Updated Grade', 'Changed grade of student 4 for HIST-2022 - Adrestian Military History from 1.25 to 1.75.', '127.0.0.1', 'teacher', 'success', '2025-11-22 04:39:29'),
(41, 5, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'teacher', 'success', '2025-11-22 04:39:39'),
(42, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-22 04:39:54'),
(43, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-22 04:39:57'),
(44, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '127.0.0.1', 'admin', 'success', '2025-11-22 04:40:07'),
(45, 3, 'Added Department', 'Created new department \'Chemistry\'.', '127.0.0.1', 'admin', 'success', '2025-11-22 04:52:43'),
(46, 1, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'student', 'success', '2025-11-22 05:26:21'),
(47, 1, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'student', 'success', '2025-11-22 05:26:24'),
(48, 2, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'teacher', 'success', '2025-11-22 05:26:31'),
(49, 2, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'teacher', 'success', '2025-11-22 05:26:33'),
(50, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-22 05:26:55'),
(51, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-22 05:26:58'),
(52, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '127.0.0.1', 'admin', 'success', '2025-11-22 05:27:06'),
(53, NULL, 'Login Failed', 'Unknown username entered: \'f\'', '127.0.0.1', NULL, 'failed', '2025-11-22 05:36:58'),
(54, 1, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'student', 'success', '2025-11-22 06:06:25'),
(55, 1, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'student', 'success', '2025-11-22 06:06:32'),
(56, 2, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'teacher', 'success', '2025-11-22 06:07:20'),
(57, 2, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'teacher', 'success', '2025-11-22 06:07:31'),
(58, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-22 06:12:38'),
(59, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-22 06:12:43'),
(60, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '127.0.0.1', 'admin', 'success', '2025-11-22 06:12:52'),
(61, 1, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'student', 'success', '2025-11-22 06:29:51'),
(62, 1, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'student', 'success', '2025-11-22 06:29:56'),
(63, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-22 06:31:40'),
(64, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-22 06:31:44'),
(65, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '127.0.0.1', 'admin', 'success', '2025-11-22 06:32:19'),
(66, 3, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'admin', 'success', '2025-11-22 06:38:54'),
(67, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-22 06:45:51'),
(68, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-22 06:45:55'),
(69, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '127.0.0.1', 'admin', 'success', '2025-11-22 06:46:05'),
(70, 3, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'admin', 'success', '2025-11-22 06:46:13'),
(71, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-22 06:51:14'),
(72, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-22 06:51:18'),
(73, 3, 'Admin 2FA Failed', 'Wrong OTP entered.', '127.0.0.1', 'admin', 'failed', '2025-11-22 06:52:39'),
(74, 3, 'Admin 2FA Resent', 'A new OTP was generated and emailed.', '127.0.0.1', 'admin', 'success', '2025-11-22 06:52:44'),
(75, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '127.0.0.1', 'admin', 'success', '2025-11-22 06:52:54'),
(76, 3, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'admin', 'success', '2025-11-22 06:54:59'),
(77, 1, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'student', 'success', '2025-11-22 06:57:21'),
(78, 1, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'student', 'success', '2025-11-22 07:03:15'),
(79, 2, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'teacher', 'success', '2025-11-22 07:03:49'),
(80, 2, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'teacher', 'success', '2025-11-22 07:04:01'),
(81, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-22 07:04:34'),
(82, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-22 07:04:38'),
(83, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '127.0.0.1', 'admin', 'success', '2025-11-22 07:04:51'),
(84, 3, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'admin', 'success', '2025-11-22 07:04:56'),
(85, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-22 09:49:59'),
(86, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-22 09:50:03'),
(87, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '127.0.0.1', 'admin', 'success', '2025-11-22 09:50:22'),
(88, 3, 'Created Announcement', 'Created announcement \'testing if teachers only will work\' (ID 13), audience=\'teachers\'.', '127.0.0.1', 'admin', 'success', '2025-11-22 10:22:20'),
(89, 3, 'Created Announcement', 'Created announcement \'dadadas\' (ID 14), audience=\'students\'.', '127.0.0.1', 'admin', 'success', '2025-11-22 10:22:31'),
(90, 3, 'Created Announcement', 'Created announcement \'adada\' (ID 15), audience=\'all\'.', '127.0.0.1', 'admin', 'success', '2025-11-22 10:22:38'),
(91, 3, 'Edited Announcement', 'Edited announcement \'testing if teachers only will work\' (ID 13), new audience=\'teachers\'.', '127.0.0.1', 'admin', 'success', '2025-11-22 10:23:34'),
(92, 3, 'Created Announcement', 'Created announcement \'asdadadfggg\' (ID 16), audience=\'teachers\'.', '127.0.0.1', 'admin', 'success', '2025-11-22 10:25:50'),
(93, 3, 'Created Announcement', 'Created announcement \'afafadaffaf\' (ID 17), audience=\'all\'.', '127.0.0.1', 'admin', 'success', '2025-11-22 10:28:06'),
(94, 3, 'Created Announcement', 'Created announcement \'FINAL PROJECT\' (ID 18), audience=\'students\'.', '127.0.0.1', 'admin', 'success', '2025-11-22 10:30:18'),
(95, 3, 'Created Announcement', 'Created announcement \'asdadasd\' (ID 19).', '127.0.0.1', 'admin', 'success', '2025-11-22 10:55:43'),
(96, 3, 'Deleted Announcement', 'Deleted announcement \'asdadasd\' (ID 19).', '127.0.0.1', 'admin', 'success', '2025-11-22 10:55:45'),
(97, 3, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'admin', 'success', '2025-11-22 11:06:57'),
(98, 2, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'teacher', 'success', '2025-11-22 11:07:04'),
(99, 2, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'teacher', 'success', '2025-11-22 11:20:00'),
(100, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-22 11:20:06'),
(101, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-22 11:20:09'),
(102, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '127.0.0.1', 'admin', 'success', '2025-11-22 11:20:21'),
(103, 3, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'admin', 'success', '2025-11-22 11:20:40'),
(104, 2, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'teacher', 'success', '2025-11-22 11:20:47'),
(105, 2, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'teacher', 'success', '2025-11-22 11:27:49'),
(106, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-22 11:28:05'),
(107, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-22 11:28:09'),
(108, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '127.0.0.1', 'admin', 'success', '2025-11-22 11:28:16'),
(109, 3, 'Created Announcement', 'Created announcement \'code lines\' (ID 20).', '127.0.0.1', 'admin', 'success', '2025-11-22 11:28:34'),
(110, 3, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'admin', 'success', '2025-11-22 11:28:46'),
(111, 2, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'teacher', 'success', '2025-11-22 11:28:54'),
(112, 2, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'teacher', 'success', '2025-11-22 11:31:32'),
(113, 1, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'student', 'success', '2025-11-22 11:31:34'),
(114, 1, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'student', 'success', '2025-11-22 11:33:39'),
(115, 2, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'teacher', 'success', '2025-11-22 11:34:34'),
(116, 2, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'teacher', 'success', '2025-11-22 11:39:06'),
(117, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-22 11:39:12'),
(118, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-22 11:39:15'),
(119, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '127.0.0.1', 'admin', 'success', '2025-11-22 11:39:26'),
(120, 3, 'Edited User', 'Edited user ID 1 (Edelgard von Hresvelg, username: edelgard, role: student) with password change.', '127.0.0.1', 'admin', 'success', '2025-11-22 12:16:31'),
(121, 3, 'Edited User', 'Edited user ID 4 (Jose Rizal Ponce, username: joserizal, role: student) with password change.', '127.0.0.1', 'admin', 'success', '2025-11-22 12:19:52'),
(122, 3, 'Edited User', 'Edited user ID 2 (Byleth Eisner, username: byleth, role: teacher) with password change.', '127.0.0.1', 'admin', 'success', '2025-11-22 12:20:08'),
(123, 3, 'Edited User', 'Edited user ID 5 (Michael Stevens, username: Vsauce, role: teacher) with password change.', '127.0.0.1', 'admin', 'success', '2025-11-22 12:20:19'),
(124, 3, 'Edited User', 'Edited user ID 3 (Niccolo Abella, username: admin, role: admin) with password change.', '127.0.0.1', 'admin', 'success', '2025-11-22 12:20:31'),
(125, 3, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'admin', 'success', '2025-11-22 12:40:29'),
(126, 2, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'teacher', 'success', '2025-11-22 12:41:12'),
(127, 2, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'teacher', 'success', '2025-11-22 12:42:48'),
(128, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-22 12:42:54'),
(129, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-22 12:42:57'),
(130, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '127.0.0.1', 'admin', 'success', '2025-11-22 12:43:04'),
(131, 3, 'Edited User', 'Edited user ID 3 (Niccolo Abella, username: admin, role: admin) with password change.', '127.0.0.1', 'admin', 'success', '2025-11-22 12:43:23'),
(132, 3, 'Edited User', 'Edited user ID 5 (Michael Stevens, username: Vsauce1, role: teacher) without password change.', '127.0.0.1', 'admin', 'success', '2025-11-22 12:43:36'),
(133, 3, 'Changed Password', 'Admin changed their account password.', '127.0.0.1', 'admin', 'success', '2025-11-22 12:44:50'),
(134, 3, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'admin', 'success', '2025-11-22 12:49:15'),
(135, 2, 'Login Failed', 'Wrong password entered.', '127.0.0.1', 'teacher', 'failed', '2025-11-22 12:49:23'),
(136, 2, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'teacher', 'success', '2025-11-22 12:49:37'),
(137, 2, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'teacher', 'success', '2025-11-22 12:49:49'),
(138, 1, 'Login Failed', 'Wrong password entered.', '127.0.0.1', 'student', 'failed', '2025-11-22 12:49:51'),
(139, 1, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'student', 'success', '2025-11-22 12:50:03'),
(140, 1, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'student', 'success', '2025-11-22 13:02:50'),
(141, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-22 13:03:09'),
(142, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-22 13:03:12'),
(143, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '127.0.0.1', 'admin', 'success', '2025-11-22 13:06:02'),
(144, 3, 'Added Department', 'Created new department \'Medical Technology\'.', '127.0.0.1', 'admin', 'success', '2025-11-22 19:58:55'),
(145, 3, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'admin', 'success', '2025-11-22 19:59:19'),
(146, 3, 'Login Failed', 'Wrong password entered.', '127.0.0.1', 'admin', 'failed', '2025-11-22 19:59:26'),
(147, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-22 19:59:36'),
(148, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-22 19:59:40'),
(149, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '127.0.0.1', 'admin', 'success', '2025-11-22 19:59:48'),
(150, 3, 'Created Announcement', 'Created announcement \'sfsfsf\' (ID 21).', '127.0.0.1', 'admin', 'success', '2025-11-22 20:01:36'),
(151, 3, 'Created Announcement', 'Created announcement \'gghhhh\' (ID 22).', '127.0.0.1', 'admin', 'success', '2025-11-22 20:01:42'),
(152, 3, 'Deleted Announcement', 'Deleted announcement \'gghhhh\' (ID 22).', '127.0.0.1', 'admin', 'success', '2025-11-22 20:08:22'),
(153, 3, 'Deleted Announcement', 'Deleted announcement \'sfsfsf\' (ID 21).', '127.0.0.1', 'admin', 'success', '2025-11-22 20:08:24'),
(154, 3, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'admin', 'success', '2025-11-22 20:39:20'),
(155, 1, 'Login Failed', 'Wrong password entered.', '127.0.0.1', 'student', 'failed', '2025-11-23 14:32:30'),
(156, 1, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'student', 'success', '2025-11-23 14:32:37'),
(157, 1, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'student', 'success', '2025-11-23 14:32:55'),
(158, 1, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'student', 'success', '2025-11-23 15:19:45'),
(159, 1, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'student', 'success', '2025-11-23 15:19:50'),
(160, 1, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'student', 'success', '2025-11-23 15:22:21'),
(161, 3, 'Logged In', 'User successfully logged in.', '::1', 'admin', 'success', '2025-11-23 15:27:30'),
(162, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '::1', 'admin', 'success', '2025-11-23 15:27:34'),
(163, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '::1', 'admin', 'success', '2025-11-23 15:27:54'),
(164, 3, 'Logged Out', 'User logged out of the system.', '::1', 'admin', 'success', '2025-11-23 15:28:18'),
(165, 3, 'Logged In', 'User successfully logged in.', '::1', 'admin', 'success', '2025-11-23 15:29:24'),
(166, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '::1', 'admin', 'success', '2025-11-23 15:29:28'),
(167, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '::1', 'admin', 'success', '2025-11-23 15:29:38'),
(168, NULL, 'Login Failed', 'Unknown username entered: \'ZAP\'', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:24'),
(169, NULL, 'Login Failed', 'Unknown username entered: \'c:/Windows/system.ini\'', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:29'),
(170, NULL, 'Login Failed', 'Unknown username entered: \'c:/Windows/system.ini\'', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:29'),
(171, NULL, 'Login Failed', 'Unknown username entered: \'../../../../../../../../../../../../../../../../Windows/system.ini\'', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:29'),
(172, NULL, 'Login Failed', 'Unknown username entered: \'../../../../../../../../../../../../../../../../Windows/system.ini\'', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:29'),
(173, NULL, 'Login Failed', 'Unknown username entered: \'c:\\Windows\\system.ini\'', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:29'),
(174, NULL, 'Login Failed', 'Unknown username entered: \'c:\\Windows\\system.ini\'', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:29'),
(175, NULL, 'Login Failed', 'Unknown username entered: \'..\\..\\..\\..\\..\\..\\..\\..\\..\\..\\..\\..\\..\\..\\..\\..\\Windows\\system.ini\'', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(176, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(177, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(178, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(179, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(180, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(181, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(182, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(183, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(184, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(185, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(186, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(187, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(188, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(189, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(190, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(191, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(192, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(193, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(194, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(195, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(196, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(197, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(198, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(199, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(200, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(201, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(202, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(203, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(204, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(205, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(206, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(207, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(208, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(209, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(210, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:30'),
(211, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(212, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(213, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(214, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(215, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(216, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(217, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(218, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(219, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(220, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(221, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(222, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(223, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(224, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(225, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(226, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(227, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(228, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(229, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(230, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(231, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(232, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(233, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(234, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(235, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(236, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(237, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(238, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(239, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(240, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(241, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(242, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(243, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(244, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:31'),
(245, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(246, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(247, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(248, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(249, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(250, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(251, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(252, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(253, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(254, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(255, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(256, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(257, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(258, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(259, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(260, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(261, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(262, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(263, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(264, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(265, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(266, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(267, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(268, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(269, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(270, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(271, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(272, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(273, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(274, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(275, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(276, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(277, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(278, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:32'),
(279, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(280, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(281, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(282, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(283, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(284, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(285, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(286, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(287, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(288, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(289, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(290, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(291, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(292, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(293, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(294, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(295, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(296, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(297, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(298, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(299, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(300, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(301, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(302, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(303, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(304, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(305, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(306, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(307, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(308, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(309, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(310, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:33'),
(311, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(312, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(313, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(314, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(315, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(316, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(317, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(318, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(319, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(320, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(321, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(322, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(323, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(324, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(325, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(326, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(327, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(328, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(329, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(330, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(331, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(332, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(333, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(334, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(335, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(336, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(337, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(338, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(339, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(340, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(341, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(342, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(343, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(344, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:34'),
(345, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(346, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(347, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(348, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(349, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(350, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(351, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(352, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(353, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(354, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(355, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(356, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(357, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(358, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(359, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(360, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(361, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(362, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(363, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(364, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(365, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(366, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(367, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(368, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(369, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(370, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(371, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(372, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(373, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(374, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(375, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(376, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(377, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(378, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:35'),
(379, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(380, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(381, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(382, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(383, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36');
INSERT INTO `activity_logs` (`log_id`, `user_id`, `action`, `details`, `ip_address`, `role`, `status`, `timestamp`) VALUES
(384, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(385, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(386, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(387, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(388, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(389, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(390, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(391, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(392, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(393, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(394, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(395, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(396, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(397, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(398, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(399, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(400, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(401, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(402, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(403, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(404, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(405, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(406, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(407, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(408, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(409, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(410, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(411, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:36'),
(412, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(413, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(414, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(415, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(416, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(417, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(418, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(419, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(420, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(421, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(422, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(423, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(424, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(425, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(426, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(427, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(428, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(429, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(430, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(431, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(432, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(433, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(434, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(435, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(436, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(437, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(438, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(439, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(440, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(441, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(442, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(443, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(444, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(445, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(446, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:37'),
(447, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(448, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(449, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(450, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(451, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(452, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(453, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(454, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(455, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(456, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(457, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(458, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(459, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(460, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(461, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(462, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(463, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(464, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(465, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(466, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(467, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(468, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(469, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(470, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(471, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(472, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(473, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(474, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(475, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(476, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(477, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(478, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(479, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(480, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:38'),
(481, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(482, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(483, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(484, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(485, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(486, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(487, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(488, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(489, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(490, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(491, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(492, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(493, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(494, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(495, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(496, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(497, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(498, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(499, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(500, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(501, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(502, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(503, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(504, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(505, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(506, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(507, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(508, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(509, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(510, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(511, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(512, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:39'),
(513, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(514, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(515, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(516, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(517, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(518, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(519, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(520, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(521, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(522, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(523, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(524, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(525, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(526, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(527, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(528, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(529, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(530, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(531, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(532, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(533, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(534, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(535, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(536, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(537, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(538, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(539, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(540, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(541, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(542, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(543, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(544, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:40'),
(545, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(546, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(547, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(548, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(549, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(550, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(551, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(552, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(553, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(554, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(555, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(556, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(557, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(558, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(559, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(560, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(561, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(562, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(563, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(564, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(565, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(566, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(567, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(568, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(569, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(570, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(571, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(572, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(573, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(574, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(575, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(576, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(577, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(578, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(579, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(580, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(581, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:41'),
(582, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(583, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(584, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(585, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(586, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(587, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(588, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(589, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(590, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(591, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(592, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(593, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(594, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(595, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(596, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(597, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(598, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(599, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(600, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(601, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(602, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:42'),
(603, NULL, 'Login Failed', 'Unknown username entered: \'#jaVasCript:/*-/*`/*\\`/*&#039;/*&quot;/**/(/* */oNcliCk=alert(5397) )//%0D%0A%0d%0a//\'', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:46'),
(604, NULL, 'Login Failed', 'Unknown username entered: \'#jaVasCript:/*-/*`/*\\`/*&#039;/*&quot;/**/(/* */oNcliCk=alert(5397) )//%0D%0A%0d%0a//\'', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:47'),
(605, NULL, 'Login Failed', 'Unknown username entered: \'#javascript:alert(5397)\'', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:47'),
(606, NULL, 'Login Failed', 'Unknown username entered: \'#javascript:alert(5397)\'', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:48'),
(607, NULL, 'Login Failed', 'Unknown username entered: \'?name=abc#\'', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:48'),
(608, NULL, 'Login Failed', 'Unknown username entered: \'?name=abc#\'', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:48'),
(609, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:52'),
(610, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:52'),
(611, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:52'),
(612, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:52'),
(613, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:52'),
(614, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:52'),
(615, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:52'),
(616, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:52'),
(617, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:52'),
(618, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:52'),
(619, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(620, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(621, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(622, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(623, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(624, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(625, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(626, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(627, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(628, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(629, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(630, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(631, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(632, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(633, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(634, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(635, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(636, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(637, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(638, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(639, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(640, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(641, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(642, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(643, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(644, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(645, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(646, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(647, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(648, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(649, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(650, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:53'),
(651, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(652, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(653, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(654, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(655, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(656, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(657, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(658, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(659, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(660, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(661, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(662, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(663, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(664, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(665, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(666, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(667, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(668, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(669, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(670, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(671, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(672, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(673, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(674, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(675, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(676, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(677, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(678, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(679, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(680, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(681, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(682, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(683, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(684, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:54'),
(685, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(686, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(687, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(688, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(689, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(690, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(691, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(692, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(693, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(694, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(695, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(696, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(697, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(698, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(699, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(700, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(701, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(702, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(703, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(704, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(705, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(706, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(707, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(708, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(709, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(710, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(711, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(712, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(713, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(714, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(715, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(716, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(717, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:55'),
(718, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(719, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(720, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(721, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(722, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(723, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(724, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(725, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(726, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(727, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(728, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(729, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(730, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(731, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(732, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(733, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(734, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(735, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(736, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(737, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(738, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(739, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(740, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(741, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(742, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(743, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(744, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(745, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(746, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(747, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(748, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(749, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(750, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56'),
(751, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:56');
INSERT INTO `activity_logs` (`log_id`, `user_id`, `action`, `details`, `ip_address`, `role`, `status`, `timestamp`) VALUES
(752, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(753, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(754, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(755, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(756, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(757, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(758, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(759, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(760, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(761, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(762, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(763, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(764, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(765, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(766, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(767, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(768, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(769, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(770, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(771, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(772, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(773, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(774, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(775, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(776, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(777, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(778, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(779, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(780, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(781, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(782, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(783, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(784, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:57'),
(785, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(786, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(787, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(788, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(789, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(790, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(791, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(792, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(793, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(794, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(795, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(796, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(797, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(798, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(799, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(800, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(801, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(802, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(803, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(804, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(805, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(806, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(807, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(808, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(809, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(810, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(811, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(812, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(813, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(814, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(815, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(816, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:58'),
(817, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(818, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(819, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(820, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(821, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(822, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(823, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(824, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(825, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(826, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(827, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(828, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(829, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(830, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(831, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(832, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(833, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(834, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(835, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(836, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(837, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(838, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(839, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(840, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(841, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(842, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(843, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(844, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(845, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(846, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(847, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(848, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(849, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(850, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(851, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:34:59'),
(852, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(853, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(854, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(855, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(856, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(857, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(858, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(859, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(860, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(861, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(862, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(863, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(864, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(865, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(866, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(867, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(868, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(869, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(870, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(871, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(872, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(873, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(874, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(875, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(876, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(877, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(878, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(879, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(880, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(881, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(882, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(883, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:00'),
(884, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(885, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(886, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(887, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(888, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(889, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(890, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(891, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(892, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(893, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(894, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(895, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(896, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(897, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(898, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(899, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(900, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(901, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(902, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(903, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(904, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(905, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(906, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(907, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(908, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(909, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(910, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(911, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(912, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(913, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(914, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(915, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:01'),
(916, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(917, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(918, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(919, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(920, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(921, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(922, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(923, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(924, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(925, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(926, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(927, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(928, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(929, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(930, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(931, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(932, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(933, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(934, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(935, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(936, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(937, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(938, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(939, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(940, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(941, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(942, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(943, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(944, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(945, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(946, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(947, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:02'),
(948, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(949, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(950, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(951, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(952, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(953, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(954, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(955, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(956, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(957, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(958, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(959, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(960, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(961, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(962, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(963, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(964, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(965, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(966, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(967, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(968, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(969, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(970, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(971, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(972, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(973, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(974, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(975, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(976, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(977, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(978, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(979, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(980, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:03'),
(981, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(982, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(983, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(984, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(985, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(986, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(987, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(988, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(989, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(990, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(991, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(992, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(993, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(994, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(995, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(996, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(997, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(998, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(999, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(1000, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(1001, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(1002, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(1003, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(1004, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(1005, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(1006, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(1007, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(1008, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(1009, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(1010, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(1011, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(1012, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(1013, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(1014, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:04'),
(1015, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1016, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1017, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1018, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1019, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1020, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1021, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1022, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1023, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1024, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1025, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1026, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1027, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1028, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1029, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1030, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1031, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1032, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1033, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1034, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1035, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1036, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1037, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1038, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1039, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1040, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1041, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1042, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1043, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1044, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1045, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1046, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1047, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1048, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1049, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:05'),
(1050, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:06'),
(1051, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:06'),
(1052, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:06'),
(1053, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:06'),
(1054, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:06'),
(1055, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:06'),
(1056, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:06'),
(1057, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:06'),
(1058, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:06'),
(1059, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:06'),
(1060, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:06'),
(1061, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:06'),
(1062, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:06'),
(1063, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:06'),
(1064, NULL, 'Login Locked', 'Account temporarily locked due to too many attempts.', '127.0.0.1', NULL, 'failed', '2025-11-23 15:35:06'),
(1065, 3, 'Logged In', 'User successfully logged in.', '::1', 'admin', 'success', '2025-11-23 15:57:31'),
(1066, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '::1', 'admin', 'success', '2025-11-23 15:57:35'),
(1067, 3, 'Admin 2FA Resent', 'A new OTP was generated and emailed.', '::1', 'admin', 'success', '2025-11-23 16:09:56'),
(1068, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '::1', 'admin', 'success', '2025-11-23 16:10:45'),
(1069, NULL, 'Login Failed', 'Unknown username entered: \'ZAP\'', '127.0.0.1', NULL, 'failed', '2025-11-23 16:24:14'),
(1070, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-23 17:51:32'),
(1071, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-23 17:51:36'),
(1072, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '127.0.0.1', 'admin', 'success', '2025-11-23 17:51:47'),
(1073, 3, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'admin', 'success', '2025-11-24 18:00:20'),
(1074, 3, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'admin', 'success', '2025-11-24 18:00:47'),
(1075, 3, 'Admin 2FA Initiated', 'OTP sent to admin email.', '127.0.0.1', 'admin', 'success', '2025-11-24 18:00:50'),
(1076, 3, 'Admin 2FA Verified', 'Correct OTP entered.', '127.0.0.1', 'admin', 'success', '2025-11-24 18:01:07'),
(1077, 3, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'admin', 'success', '2025-11-24 18:05:55'),
(1078, 1, 'Login Failed', 'Wrong password entered.', '127.0.0.1', 'student', 'failed', '2025-11-24 18:06:58'),
(1079, 1, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'student', 'success', '2025-11-24 18:07:05'),
(1080, 1, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'student', 'success', '2025-11-24 18:07:17'),
(1081, 2, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'teacher', 'success', '2025-11-24 18:07:48'),
(1082, 2, 'Logged Out', 'User logged out of the system.', '127.0.0.1', 'teacher', 'success', '2025-11-24 18:07:57'),
(1083, 1, 'Logged In', 'User successfully logged in.', '127.0.0.1', 'student', 'success', '2025-11-24 18:17:41');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `content` text,
  `author_id` int DEFAULT NULL,
  `audience` enum('all','students','teachers','class') NOT NULL,
  `course_id` int DEFAULT NULL,
  `date_posted` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`announcement_id`, `title`, `content`, `author_id`, `audience`, `course_id`, `date_posted`) VALUES
(1, 'Enrollment for 2nd Semester Opens', 'Enrollment for the upcoming semester starts next week. Make sure to finalize your subjects early.', NULL, 'students', NULL, '2025-11-10 20:09:12'),
(2, 'Power Interruption on Campus', 'Maintenance work will cause brief power outages in the main building this Saturday.', NULL, 'all', NULL, '2025-11-10 20:09:12'),
(3, 'Library Digital Access', 'Students can now access e-books and journals using their Escolink Centra account credentials.', NULL, 'students', NULL, '2025-11-10 20:09:12'),
(7, 'asdadadaadadafhhhhhh', 'fagsdgsfsdfsf', 2, 'class', 1, '2025-11-13 11:45:03'),
(8, 'wassup', 'hello hello again', 3, 'all', NULL, '2025-11-21 20:44:07'),
(9, 'Testing for Imperial Governance course', 'seen?', 2, 'class', 1, '2025-11-22 04:12:34'),
(10, 'all users, do you see this?', 'do you? -admin', 3, 'all', NULL, '2025-11-21 23:20:52'),
(12, 'only for students to see test', 'do you students see this?', 3, 'students', NULL, '2025-11-21 23:31:20'),
(13, 'testing if teachers only will work', 'do you see?ddd', 3, 'teachers', NULL, '2025-11-22 10:23:34'),
(14, 'dadadas', 'asdadas', 3, 'students', NULL, '2025-11-22 10:22:31'),
(15, 'adada', 'dasdad', 3, 'all', NULL, '2025-11-22 10:22:38'),
(16, 'asdadadfggg', 'adadaf', 3, 'teachers', NULL, '2025-11-22 10:25:50'),
(17, 'afafadaffaf', '<div id=\"deleteModal\" class=\"modal\" style=\"align-items:center; justify-content:center;\">\r\n  <div class=\"modal-content\" style=\"width:380px; text-align:center;\">\r\n    <span class=\"close\" onclick=\"closeDelete()\">&times;</span>\r\n    <h2>Delete Announcement?</h2>\r\n    <p>Are you sure?</p>\r\n\r\n    <form method=\"POST\">\r\n      <input type=\"hidden\" name=\"action\" value=\"delete\">\r\n      <input type=\"hidden\" name=\"announcement_id\" id=\"delete_id\">\r\n\r\n      <div class=\"modal-buttons\">\r\n        <button type=\"submit\" class=\"delete-btn\">Delete</button>\r\n        <button type=\"button\" class=\"cancel-btn\" onclick=\"closeDelete()\">Cancel</button>\r\n      </div>\r\n    </form>\r\n  </div>\r\n</div>', 3, 'all', NULL, '2025-11-22 10:28:06'),
(18, 'FINAL PROJECT', 'FINAL PROJECT - CORE PRESENTATION FLOW AND SCHEDULE\r\n\r\nFor Final Project Defense – Secure Web Application & SDLC Project Plan\r\n1. Introduction (1–2 minutes)\r\n\r\n            Industry chosen\r\n            Short description of the system\r\n            Why this system is needed\r\n\r\n2. System Demo (Most Important)\r\nShow ONLY:\r\n\r\n                Login & RBAC (Admin / User / Guest)\r\n                At least 3–5 core features\r\n                Security features in action:\r\n                    Input validation\r\n                    Encrypted data example\r\n                    Secure session behavior\r\n                Activity logging (show logs)\r\n                System vulnerability scan results (before & after fix)\r\n\r\n3. Security Implementations (Critical Section)\r\nExplain briefly:\r\n\r\n                    OWASP Top 10 mitigations applied\r\n                    HTTPS & SSL\r\n                    Encryption (what data is encrypted?)\r\n                    Session management (timeout, fixation protection)\r\n\r\nShow:\r\n\r\n                Security vulnerability scan results (before & after fix)\r\n\r\n4. System Architecture (Short Overview)\r\nShow only:\r\n\r\n                Architecture diagram (frontend, backend, DB)\r\n                Database schema (highlight encrypted fields)\r\n\r\n5. Data Security Policy (Summarized)\r\nHighlight ONLY:\r\n\r\n                Data classification\r\n                Access control policy\r\n                Incident response plan\r\n                Backup & recovery\r\n\r\n(No need to read the entire policy.)\r\n6. SDLC Project Plan (Condensed)\r\nShow quick highlights:\r\n\r\n                Timeline & Gantt chart\r\n                Risk matrix (Top 3 risks)\r\n                Tools & technologies used\r\n                Testing summary (functional + security)\r\n\r\n7. Challenges & Solutions (Short)\r\nShare 2–3 items:\r\n\r\n                Technical challenge\r\n                Security challenge\r\n                Team/PM challenge\r\n                (And how they solved them)\r\n\r\n8. Conclusion\r\n\r\n            Summary\r\n            What the system achieved\r\n            Future improvements\r\n\r\nTOTAL PRESENTATION LENGTH: 30–45 minutes\r\nFinal Project Presentation Reminders\r\n\r\nPlease take note of the following guidelines for your final project defense:\r\n1. No PowerPoint Presentation Required\r\n\r\n            You will present directly using your system, documentation, and project outputs.\r\n\r\n            Focus on demonstrating your application and explaining your work clearly.\r\n\r\n2. Proper Uniform Attire\r\n\r\n            Wear complete school uniform during the presentation.\r\n\r\n            This is part of your professionalism score.\r\n\r\n3. Printed & Compiled Documentation\r\n\r\nPrepare the following in printed hard copies:\r\n\r\n                Project documentation\r\n\r\n                Data Security Policy\r\n\r\n                SDLC Project Plan\r\n\r\n                Score matrix sheets (as instructed)\r\n\r\nAll printed documents must be:\r\n\r\n                Complete\r\n\r\n                Clean and readable\r\n\r\n                Compiled in one folder per group\r\n\r\n4. Softcopy Submission\r\n\r\n            All digital files must be compiled and ready for submission.\r\n\r\n            Organize them in clear folders (e.g., Documentation, Source Code, Policy, Scans).\r\n\r\n            Ensure the final build of your system is included.\r\n\r\n5. Be On Time\r\n\r\n            Late groups may be penalized.\r\n\r\n            Arrive at least 15 minutes before your scheduled presentation.\r\n\r\n6. Provide Your Own Devices\r\n\r\nPlease bring:\r\n\r\n                Laptop with your working system\r\n\r\n                Charger\r\n\r\n                Mobile hotspot or backup internet (if needed)\r\n\r\n                Any additional device required for your demo\r\n\r\nMake sure:\r\n\r\n                        Your system runs offline or with your own internet\r\n\r\n                        All login credentials for testing are prepared\r\n\r\n7. Professionalism & Preparedness\r\n\r\n            Assign clear roles for each presenter.\r\n\r\n            Test your system before arriving.\r\n\r\n            Be ready to answer technical and security-related questions.\r\n\r\nTOTAL PRESENTATION LENGTH: 30–45 minutes', 3, 'students', NULL, '2025-11-22 10:30:18'),
(20, 'code lines', '<?php\r\ninclude(\"../includes/auth_session.php\");\r\ninclude(\"../includes/auth_teacher.php\");\r\ninclude(\"../config/db_connect.php\");\r\ninclude(\"../includes/logging.php\");\r\n\r\n$user_id   = $_SESSION[\'user_id\'];\r\n$full_name = $_SESSION[\'full_name\'] ?? \"Professor\";\r\n\r\n/* ---------------------------------------------------------\r\n   FETCH PROFILE PICTURE\r\n--------------------------------------------------------- */\r\n$stmt = $conn->prepare(\"SELECT profile_pic FROM users WHERE user_id = ?\");\r\n$stmt->bind_param(\"i\", $user_id);\r\n$stmt->execute();\r\n$stmt->bind_result($profile_pic);\r\n$stmt->fetch();\r\n$stmt->close();\r\n\r\n$avatar = (!empty($profile_pic))\r\n    ? \"../uploads/\" . htmlspecialchars($profile_pic)\r\n    : \"images/ProfileImg.png\";\r\n\r\n/* ---------------------------------------------------------\r\n   FETCH TEACHER\'S COURSES (USED FOR LOGGING)\r\n--------------------------------------------------------- */\r\n$courseMap = [];\r\n$stmt = $conn->prepare(\"SELECT course_id, course_code, course_name FROM courses WHERE teacher_id = ?\");\r\n$stmt->bind_param(\"i\", $user_id);\r\n$stmt->execute();\r\n$res = $stmt->get_result();\r\nwhile ($row = $res->fetch_assoc()) {\r\n    $courseMap[$row[\'course_id\']] = $row[\'course_code\'] . \" - \" . $row[\'course_name\'];\r\n}\r\n$stmt->close();\r\n\r\n/* ---------------------------------------------------------\r\n   CREATE ANNOUNCEMENT\r\n   LOGGING ADDED\r\n--------------------------------------------------------- */\r\nif ($_SERVER[\'REQUEST_METHOD\'] === \'POST\' && ($_POST[\'action\'] ?? \'\') === \'create\') {\r\n    $title     = trim($_POST[\'title\'] ?? \'\');\r\n    $content   = trim($_POST[\'content\'] ?? \'\');\r\n    $course_id = (int)($_POST[\'course_id\'] ?? 0);\r\n\r\n    if ($title !== \'\' && $content !== \'\' && $course_id > 0) {\r\n\r\n        $stmt = $conn->prepare(\"\r\n          INSERT INTO announcements (title, content, author_id, audience, course_id, date_posted)\r\n          VALUES (?, ?, ?, \'class\', ?, NOW())\r\n        \");\r\n        $stmt->bind_param(\"ssii\", $title, $content, $user_id, $course_id);\r\n        $stmt->execute();\r\n        $newId = $stmt->insert_id;\r\n        $stmt->close();\r\n\r\n        /* ------ LOG: CREATE ------ */\r\n        $cLabel = $courseMap[$course_id] ?? \"Course ID {$course_id}\";\r\n        log_activity(\r\n            $conn,\r\n            (int)$user_id,\r\n            \"Posted Class Announcement\",\r\n            \"Created announcement \'{$title}\' (ID {$newId}) for {$cLabel}.\",\r\n            \"success\"\r\n        );\r\n    }\r\n\r\n    header(\"Location: announcements_prof.php\");\r\n    exit;\r\n}\r\n\r\n/* ---------------------------------------------------------\r\n   EDIT ANNOUNCEMENT\r\n   LOGGING ADDED\r\n--------------------------------------------------------- */\r\nif ($_SERVER[\'REQUEST_METHOD\'] === \'POST\' && ($_POST[\'action\'] ?? \'\') === \'edit\') {\r\n    $id        = (int)($_POST[\'announcement_id\'] ?? 0);\r\n    $title     = trim($_POST[\'title\'] ?? \'\');\r\n    $content   = trim($_POST[\'content\'] ?? \'\');\r\n    $course_id = (int)($_POST[\'course_id\'] ?? 0);\r\n\r\n    if ($id > 0 && $title !== \'\' && $content !== \'\' && $course_id > 0) {\r\n\r\n        $stmt = $conn->prepare(\"\r\n          UPDATE announcements\r\n          SET title = ?, content = ?, course_id = ?, date_posted = NOW()\r\n          WHERE announcement_id = ? AND author_id = ?\r\n        \");\r\n        $stmt->bind_param(\"ssiii\", $title, $content, $course_id, $id, $user_id);\r\n        $stmt->execute();\r\n        $stmt->close();\r\n\r\n        /* ------ LOG: EDIT ------ */\r\n        $cLabel = $courseMap[$course_id] ?? \"Course ID {$course_id}\";\r\n        log_activity(\r\n            $conn,\r\n            (int)$user_id,\r\n            \"Edited Class Announcement\",\r\n            \"Updated announcement \'{$title}\' (ID {$id}) for {$cLabel}.\",\r\n            \"success\"\r\n        );\r\n    }\r\n\r\n    header(\"Location: announcements_prof.php\");\r\n    exit;\r\n}\r\n\r\n/* ---------------------------------------------------------\r\n   DELETE ANNOUNCEMENT\r\n   LOGGING ADDED\r\n--------------------------------------------------------- */\r\nif ($_SERVER[\'REQUEST_METHOD\'] === \'POST\' && ($_POST[\'action\'] ?? \'\') === \'delete\') {\r\n    $id = (int)($_POST[\'announcement_id\'] ?? 0);\r\n\r\n    if ($id > 0) {\r\n\r\n        // Fetch announcement title & course for logging\r\n        $stmt = $conn->prepare(\"\r\n          SELECT title, course_id \r\n          FROM announcements \r\n          WHERE announcement_id = ? AND author_id = ?\r\n        \");\r\n        $stmt->bind_param(\"ii\", $id, $user_id);\r\n        $stmt->execute();\r\n        $stmt->bind_result($delTitle, $delCourse);\r\n        $stmt->fetch();\r\n        $stmt->close();\r\n\r\n        $stmt = $conn->prepare(\"DELETE FROM announcements WHERE announcement_id = ? AND author_id = ?\");\r\n        $stmt->bind_param(\"ii\", $id, $user_id);\r\n        $stmt->execute();\r\n        $stmt->close();\r\n\r\n        /* ------ LOG: DELETE ------ */\r\n        $cLabel = $courseMap[$delCourse] ?? \"Course ID {$delCourse}\";\r\n        log_activity(\r\n            $conn,\r\n            (int)$user_id,\r\n            \"Deleted Class Announcement\",\r\n            \"Deleted announcement \'{$delTitle}\' (ID {$id}) from {$cLabel}.\",\r\n            \"success\"\r\n        );\r\n    }\r\n\r\n    header(\"Location: announcements_prof.php\");\r\n    exit;\r\n}\r\n\r\n/* ---------------------------------------------------------\r\n   FETCH TEACHER COURSES AGAIN (for dropdown)\r\n--------------------------------------------------------- */\r\n$courses = [];\r\n$stmt = $conn->prepare(\"SELECT course_id, course_code, course_name FROM courses WHERE teacher_id = ?\");\r\n$stmt->bind_param(\"i\", $user_id);\r\n$stmt->execute();\r\n$res = $stmt->get_result();\r\nwhile ($row = $res->fetch_assoc()) $courses[] = $row;\r\n$stmt->close();\r\n\r\n/* ---------------------------------------------------------\r\n   FETCH ANNOUNCEMENTS VISIBLE TO TEACHER\r\n--------------------------------------------------------- */\r\n$stmt = $conn->prepare(\"\r\n  SELECT \r\n      a.announcement_id,\r\n      a.title,\r\n      a.content,\r\n      a.date_posted,\r\n      a.audience,\r\n      a.course_id,\r\n      c.course_code,\r\n      c.course_name,\r\n      u.full_name AS author_name\r\n  FROM announcements a\r\n  LEFT JOIN courses c ON c.course_id = a.course_id\r\n  LEFT JOIN users  u ON u.user_id  = a.author_id\r\n  WHERE \r\n      a.audience = \'all\'\r\n      OR a.audience = \'teachers\'\r\n      OR (a.audience = \'class\' AND c.teacher_id = ?)\r\n      OR a.author_id = ?\r\n  ORDER BY a.date_posted DESC\r\n\");\r\n$stmt->bind_param(\"ii\", $user_id, $user_id);\r\n$stmt->execute();\r\n$announcements = $stmt->get_result();\r\n$stmt->close();\r\n?>\r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n  <meta charset=\"UTF-8\">\r\n  <title>Escolink Centra | Professor Announcements</title>\r\n  <link rel=\"stylesheet\" href=\"CSS/format.css\">\r\n  <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css\">\r\n\r\n  <style>\r\n    /* lock body scroll when modal open */\r\n    body.modal-open {\r\n      overflow: hidden;\r\n    }\r\n\r\n    /* center modals (view / edit / delete) */\r\n    .modal {\r\n      display: none;\r\n      align-items: center;\r\n      justify-content: center;\r\n    }\r\n\r\n    /* make modal itself scrollable if content is long */\r\n    .modal-content {\r\n      max-height: 80vh !important;\r\n      overflow-y: auto !important;\r\n      word-wrap: break-word;\r\n    }\r\n\r\n    /* truncated preview in announcement cards */\r\n    .announce-preview {\r\n      max-height: 120px;\r\n      overflow: hidden;\r\n    }\r\n\r\n    /* full text in view modal */\r\n    #viewBody {\r\n      white-space: pre-wrap;\r\n      word-wrap: break-word;\r\n      line-height: 1.6;\r\n    }\r\n\r\n    /* equal-size buttons in delete modal (prof view) */\r\n    .modal-buttons {\r\n      display: flex;\r\n      justify-content: center;\r\n      gap: 10px;\r\n      margin-top: 15px;\r\n    }\r\n    .modal-buttons .delete-btn,\r\n    .modal-buttons .cancel-btn {\r\n      min-width: 110px;\r\n      padding: 8px 18px;\r\n      text-align: center;\r\n    }\r\n  </style>\r\n</head>\r\n\r\n<body>\r\n  <div class=\"portal-layout\">\r\n    <?php include(\'sidebar_prof.php\'); ?>\r\n\r\n    <main class=\"main-content\">\r\n\r\n      <!-- CLEAN TOPBAR -->\r\n      <header class=\"topbar\">\r\n        <div></div>\r\n        <div class=\"profile-section\">\r\n          <img src=\"<?= $avatar ?>\" class=\"avatar\">\r\n          <span class=\"profile-name\"><?= htmlspecialchars($full_name) ?></span>\r\n        </div>\r\n      </header>\r\n\r\n      <section class=\"dashboard-body\">\r\n        <h1>Announcements</h1>\r\n        <p class=\"semester-text\">Create, review, or manage your class announcements for students.</p>\r\n\r\n        <!-- ================= CREATE FORM ================= -->\r\n        <div class=\"announcement-form\">\r\n          <h3><i class=\"fa-solid fa-bullhorn\"></i> Create New Announcement</h3>\r\n\r\n          <form method=\"POST\">\r\n            <input type=\"hidden\" name=\"action\" value=\"create\">\r\n\r\n            <label>Title:</label>\r\n            <input type=\"text\" name=\"title\" required>\r\n\r\n            <label>Message:</label>\r\n            <textarea name=\"content\" rows=\"5\" required></textarea>\r\n\r\n            <label>Select Course:</label>\r\n            <select name=\"course_id\" required>\r\n              <option value=\"\">-- Select a course --</option>\r\n              <?php foreach ($courses as $c): ?>\r\n                <option value=\"<?= $c[\'course_id\'] ?>\">\r\n                  <?= htmlspecialchars($c[\'course_code\'] . \": \" . $c[\'course_name\']) ?>\r\n                </option>\r\n              <?php endforeach; ?>\r\n            </select>\r\n\r\n            <button type=\"submit\" class=\"save-btn\">Post Announcement</button>\r\n          </form>\r\n        </div>\r\n\r\n        <!-- ================= LIST ================= -->\r\n        <section class=\"announcements-section\">\r\n          <h2><i class=\"fa-solid fa-bullhorn\"></i> Recent Announcements</h2>\r\n\r\n          <?php if ($announcements->num_rows > 0): ?>\r\n            <?php while ($a = $announcements->fetch_assoc()): ?>\r\n              <?php\r\n                $aid     = (int)$a[\'announcement_id\'];\r\n                $title   = $a[\'title\'] ?? \'\';\r\n                $content = $a[\'content\'] ?? \'\';\r\n\r\n                // truncated preview like admin view\r\n                if (mb_strlen($content) > 300) {\r\n                    $preview = mb_substr($content, 0, 300) . \"…\";\r\n                } else {\r\n                    $preview = $content;\r\n                }\r\n\r\n                // target label (no nested ternary to avoid PHP 8 warning)\r\n                if ($a[\'audience\'] === \'all\') {\r\n                    $target = \"All Users\";\r\n                } elseif ($a[\'audience\'] === \'teachers\') {\r\n                    $target = \"All Teachers\";\r\n                } elseif ($a[\'audience\'] === \'class\') {\r\n                    $target = $a[\'course_code\'] ?: \"Class\";\r\n                } else {\r\n                    $target = \"Unknown\";\r\n                }\r\n\r\n                $author = $a[\'author_name\'] ?? \'Unknown\';\r\n              ?>\r\n\r\n              <div class=\"announcement-card\"\r\n                data-id=\"<?= $aid ?>\"\r\n                data-title=\"<?= htmlspecialchars($title, ENT_QUOTES) ?>\"\r\n                data-content=\"<?= htmlspecialchars($content, ENT_QUOTES) ?>\"\r\n                data-target=\"<?= htmlspecialchars($target, ENT_QUOTES) ?>\"\r\n                data-author=\"<?= htmlspecialchars($author, ENT_QUOTES) ?>\"\r\n                data-course=\"<?= (int)$a[\'course_id\'] ?>\"\r\n              >\r\n                <h3><?= htmlspecialchars($title) ?></h3>\r\n                <p class=\"announce-date\">\r\n                  Posted: <?= date(\"F j, Y\", strtotime($a[\'date_posted\'])) ?> |\r\n                  Target: <?= htmlspecialchars($target) ?> |\r\n                  By: <?= htmlspecialchars($author) ?>\r\n                </p>\r\n\r\n                <p class=\"announce-preview\"><?= nl2br(htmlspecialchars($preview)) ?></p>\r\n\r\n                <div class=\"card-actions\">\r\n                  <!-- VIEW -->\r\n                  <button class=\"details-btn\" onclick=\"openView(this)\">\r\n                    <i class=\"fa-solid fa-eye\"></i> View Details\r\n                  </button>\r\n\r\n                  <!-- EDIT & DELETE: only if teacher authored it -->\r\n                  <?php if ($a[\'author_name\'] === $full_name): ?>\r\n                    <button class=\"edit-btn\" onclick=\"openEdit(this)\">\r\n                      <i class=\"fa-solid fa-pen\"></i> Edit\r\n                    </button>\r\n                    <button class=\"delete-btn\" onclick=\"openDelete(this)\">\r\n                      <i class=\"fa-solid fa-trash\"></i> Delete\r\n                    </button>\r\n                  <?php endif; ?>\r\n                </div>\r\n              </div>\r\n\r\n            <?php endwhile; ?>\r\n          <?php else: ?>\r\n            <p style=\"text-align:center; font-style:italic;\">No announcements to show.</p>\r\n          <?php endif; ?>\r\n\r\n        </section>\r\n      </section>\r\n    </main>\r\n  </div>\r\n\r\n  <!-- VIEW MODAL -->\r\n  <div id=\"viewModal\" class=\"modal\">\r\n    <div class=\"modal-content\">\r\n      <span class=\"close\" onclick=\"closeView()\">&times;</span>\r\n      <h2 id=\"viewTitle\"></h2>\r\n      <p id=\"viewMeta\"></p>\r\n      <div id=\"viewBody\"></div>\r\n    </div>\r\n  </div>\r\n\r\n  <!-- EDIT MODAL -->\r\n  <div id=\"editModal\" class=\"modal\">\r\n    <div class=\"modal-content\">\r\n      <span class=\"close\" onclick=\"closeEdit()\">&times;</span>\r\n      <h2>Edit Announcement</h2>\r\n      <form method=\"POST\">\r\n        <input type=\"hidden\" name=\"action\" value=\"edit\">\r\n        <input type=\"hidden\" name=\"announcement_id\" id=\"edit_id\">\r\n\r\n        <label>Title:</label>\r\n        <input type=\"text\" id=\"edit_title\" name=\"title\" required>\r\n\r\n        <label>Message:</label>\r\n        <textarea id=\"edit_content\" name=\"content\" rows=\"5\" required></textarea>\r\n\r\n        <label>Select Course:</label>\r\n        <select name=\"course_id\" id=\"edit_course\" required>\r\n          <?php foreach ($courses as $c): ?>\r\n            <option value=\"<?= $c[\'course_id\'] ?>\">\r\n              <?= htmlspecialchars($c[\'course_code\'] . \": \" . $c[\'course_name\']) ?>\r\n            </option>\r\n          <?php endforeach; ?>\r\n        </select>\r\n\r\n        <div class=\"modal-buttons\">\r\n          <button class=\"save-btn\" type=\"submit\">Save Changes</button>\r\n          <button class=\"cancel-btn\" type=\"button\" onclick=\"closeEdit()\">Cancel</button>\r\n        </div>\r\n      </form>\r\n    </div>\r\n  </div>\r\n\r\n  <!-- DELETE MODAL -->\r\n  <div id=\"deleteModal\" class=\"modal\">\r\n    <div class=\"modal-content\" style=\"width:380px; text-align:center;\">\r\n      <span class=\"close\" onclick=\"closeDelete()\">&times;</span>\r\n      <h2>Delete Announcement</h2>\r\n      <p>Are you sure?</p>\r\n\r\n      <form method=\"POST\">\r\n        <input type=\"hidden\" name=\"action\" value=\"delete\">\r\n        <input type=\"hidden\" name=\"announcement_id\" id=\"delete_id\">\r\n\r\n        <div class=\"modal-buttons\">\r\n          <button class=\"delete-btn\" type=\"submit\">Delete</button>\r\n          <button class=\"cancel-btn\" type=\"button\" onclick=\"closeDelete()\">Cancel</button>\r\n        </div>\r\n      </form>\r\n    </div>\r\n  </div>\r\n\r\n  <script>\r\n    const viewModal   = document.getElementById(\'viewModal\');\r\n    const editModal   = document.getElementById(\'editModal\');\r\n    const deleteModal = document.getElementById(\'deleteModal\');\r\n\r\n    const viewTitle = document.getElementById(\'viewTitle\');\r\n    const viewMeta  = document.getElementById(\'viewMeta\');\r\n    const viewBody  = document.getElementById(\'viewBody\');\r\n\r\n    const editId      = document.getElementById(\'edit_id\');\r\n    const editTitle   = document.getElementById(\'edit_title\');\r\n    const editContent = document.getElementById(\'edit_content\');\r\n    const editCourse  = document.getElementById(\'edit_course\');\r\n\r\n    const deleteId = document.getElementById(\'delete_id\');\r\n\r\n    function lockBody() {\r\n      document.body.classList.add(\'modal-open\');\r\n    }\r\n    function unlockBody() {\r\n      document.body.classList.remove(\'modal-open\');\r\n    }\r\n\r\n    // ---------- VIEW ----------\r\n    function openView(btn) {\r\n      const card    = btn.closest(\'.announcement-card\');\r\n      const title   = card.dataset.title || \'\';\r\n      const content = card.dataset.content || \'\';\r\n      const target  = card.dataset.target || \'\';\r\n      const author  = card.dataset.author || \'\';\r\n\r\n      viewTitle.textContent = title;\r\n\r\n      let metaText = \'\';\r\n      if (target) metaText += \'Target: \' + target;\r\n      if (author) metaText += (metaText ? \' | \' : \'\') + \'By: \' + author;\r\n      viewMeta.textContent = metaText;\r\n\r\n      viewBody.textContent = content;\r\n\r\n      viewModal.style.display = \'flex\';\r\n      lockBody();\r\n    }\r\n    function closeView() {\r\n      viewModal.style.display = \'none\';\r\n      unlockBody();\r\n    }\r\n\r\n    // ---------- EDIT ----------\r\n    function openEdit(btn) {\r\n      const card    = btn.closest(\'.announcement-card\');\r\n      const id      = card.dataset.id;\r\n      const title   = card.dataset.title || \'\';\r\n      const content = card.dataset.content || \'\';\r\n      const course  = card.dataset.course || \'\';\r\n\r\n      editId.value      = id;\r\n      editTitle.value   = title;\r\n      editContent.value = content;\r\n      editCourse.value  = course;\r\n\r\n      editModal.style.display = \'flex\';\r\n      lockBody();\r\n    }\r\n    function closeEdit() {\r\n      editModal.style.display = \'none\';\r\n      unlockBody();\r\n    }\r\n\r\n    // ---------- DELETE ----------\r\n    function openDelete(btn) {\r\n      const card = btn.closest(\'.announcement-card\');\r\n      const id   = card.dataset.id;\r\n\r\n      deleteId.value = id;\r\n\r\n      deleteModal.style.display = \'flex\';\r\n      lockBody();\r\n    }\r\n    function closeDelete() {\r\n      deleteModal.style.display = \'none\';\r\n      unlockBody();\r\n    }\r\n\r\n    // click outside to close\r\n    window.addEventListener(\'click\', (e) => {\r\n      if (e.target === viewModal)  closeView();\r\n      if (e.target === editModal)  closeEdit();\r\n      if (e.target === deleteModal) closeDelete();\r\n    });\r\n  </script>\r\n\r\n</body>\r\n</html>', 3, 'teachers', NULL, '2025-11-22 11:28:34');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int NOT NULL,
  `course_code` varchar(20) DEFAULT NULL,
  `course_name` varchar(100) DEFAULT NULL,
  `units` decimal(3,1) DEFAULT NULL,
  `teacher_id` int DEFAULT NULL,
  `semester` varchar(20) DEFAULT NULL,
  `schedule_day` varchar(50) DEFAULT NULL,
  `schedule_time` varchar(50) DEFAULT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_code`, `course_name`, `units`, `teacher_id`, `semester`, `schedule_day`, `schedule_time`, `description`) VALUES
(1, 'POLSCI-101', 'Imperial Governance', 3.0, 2, '1st Semester', 'Mon & Wed', '9:00–10:30 AM', 'Covers leadership and governance principles.'),
(2, 'HIST-2022', 'Adrestian Military History', 3.0, 5, '1st Semester', 'Tue & Thu', '1:00–3:30 PM', 'Focuses on the empire’s strategic campaigns.'),
(3, 'PHIL-303', 'Ethics & Reform', 2.0, 2, '1st Semester', 'Fri', '10:00–12:00 PM', 'Explores ethics and reform in governance.');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int NOT NULL,
  `department_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`) VALUES
(1, 'Information Technology'),
(2, 'Computer Science'),
(3, 'Information Systems'),
(5, 'Nothing'),
(6, 'Medicine'),
(7, 'Chemistry'),
(8, 'Medical Technology');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` int NOT NULL,
  `student_id` int DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `date_enrolled` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `course_id`, `date_enrolled`) VALUES
(1, 1, 1, '2025-11-10 17:14:11'),
(4, 1, 3, '2025-11-21 13:45:39'),
(5, 4, 3, '2025-11-21 13:45:39'),
(6, 1, 2, '2025-11-22 03:23:30'),
(7, 4, 2, '2025-11-22 03:23:30');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `grade_id` int NOT NULL,
  `student_id` int DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `grade` decimal(3,2) DEFAULT NULL,
  `encoded_by` int DEFAULT NULL,
  `date_encoded` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`grade_id`, `student_id`, `course_id`, `grade`, `encoded_by`, `date_encoded`) VALUES
(4, 1, 1, 2.00, 2, '2025-11-12 23:37:30'),
(5, 1, 2, 1.25, 5, '2025-11-22 04:39:29'),
(6, 1, 3, 1.15, 2, '2025-11-22 04:38:59'),
(11, 4, 3, 1.25, 2, '2025-11-22 04:38:59'),
(13, 4, 2, 1.75, 5, '2025-11-22 04:39:29');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `program_id` int NOT NULL,
  `program_name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`program_id`, `program_name`) VALUES
(2, 'Rocket Science'),
(3, 'Farming'),
(4, 'General Biology');

-- --------------------------------------------------------

--
-- Table structure for table `student_info`
--

CREATE TABLE `student_info` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `program` varchar(100) DEFAULT NULL,
  `year_level` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student_info`
--

INSERT INTO `student_info` (`id`, `user_id`, `student_id`, `program`, `year_level`) VALUES
(4, 1, '2025-01', 'Rocket Science', 3),
(5, 4, '2025-02', 'Farming', 4);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_info`
--

CREATE TABLE `teacher_info` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `teacher_id` varchar(20) NOT NULL,
  `department_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `teacher_info`
--

INSERT INTO `teacher_info` (`id`, `user_id`, `teacher_id`, `department_id`) VALUES
(2, 2, 'PROF-111', 5),
(3, 5, 'PROF-222', 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('student','teacher','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'student',
  `profile_pic` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `about_me` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `full_name`, `email`, `role`, `profile_pic`, `created_at`, `updated_at`, `about_me`) VALUES
(1, 'edelgard', '$2y$10$p8V3sWbJ951FC0l3bEy2wegdZaDcx8PFg/ftvhOaB3EilCNQi13Qi', 'Edelgard von Hresvelg', 'edelgard@ceu.edu.ph', 'student', '1762822896_cyreneE6.jpg', '2025-11-10 13:51:44', '2025-11-22 12:16:31', '1st student ever.'),
(2, 'byleth', '$2y$10$b34BN3HFdEZRYukpPnlUc.298YupPCNDEvjGPWe0tc5aIjEiMUYUK', 'Byleth Eisner', 'byleth.eisner@ceu.edu.ph', 'teacher', '1763026298_mai_model.jpg', '2025-11-11 15:06:39', '2025-11-22 12:20:08', NULL),
(3, 'admin', '$2y$10$xVMS7YiViJJA33R50CXt8eKZ6z3D/61M0bEtZozrW2dM95otQRrfO', 'Niccolo Abella', 'escolinkcentra@gmail.com', 'admin', '1763756337_capitano1.jpg', '2025-11-13 17:44:07', '2025-11-22 12:44:50', NULL),
(4, 'joserizal', '$2y$10$GD.sWp1DniunDR1jUHQISORevEYVidPs43boyzs5iQoPrRPUBO09e', 'Jose Rizal Ponce', 'joserizal@gmail.com', 'student', NULL, '2025-11-21 13:42:09', '2025-11-22 12:19:52', NULL),
(5, 'Vsauce1', '$2y$10$OYOccKgUluCZuc7lwftQTeS9zOI/PcJQVCadePuHWDqd..u/sELqW', 'Michael Stevens', 'vsauce1@gmail.com', 'teacher', NULL, '2025-11-22 02:44:10', '2025-11-22 12:43:36', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `courses_ibfk_1` (`teacher_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`grade_id`),
  ADD UNIQUE KEY `unique_grade` (`student_id`,`course_id`),
  ADD KEY `fk_grades_course` (`course_id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`program_id`);

--
-- Indexes for table `student_info`
--
ALTER TABLE `student_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `teacher_info`
--
ALTER TABLE `teacher_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1084;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `grade_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `program_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `student_info`
--
ALTER TABLE `student_info`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `teacher_info`
--
ALTER TABLE `teacher_info`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `fk_grades_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_grades_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_info`
--
ALTER TABLE `student_info`
  ADD CONSTRAINT `student_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `teacher_info`
--
ALTER TABLE `teacher_info`
  ADD CONSTRAINT `teacher_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
