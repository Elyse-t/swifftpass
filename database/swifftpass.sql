-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2025 at 11:06 AM
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
-- Database: `swifftpass`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `notification_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','danger') DEFAULT 'info',
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_notifications`
--

INSERT INTO `admin_notifications` (`notification_id`, `title`, `message`, `type`, `status`, `created_at`) VALUES
(1, 'Trip Completed', 'Driver Jane Smith has completed trip #2 from Nyabugogo to Muhanga', 'success', 'unread', '2025-10-17 17:37:52'),
(2, 'Trip Completed', 'Driver Jane Smith has completed trip #2 from Nyabugogo to Muhanga', 'success', 'unread', '2025-10-17 17:44:34'),
(3, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 17:58:22'),
(4, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 17:58:53'),
(5, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 17:59:24'),
(6, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 17:59:55'),
(7, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:00:26'),
(8, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:00:57'),
(9, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:01:28'),
(10, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:01:59'),
(11, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:02:30'),
(12, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:03:01'),
(13, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:03:32'),
(14, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:04:03'),
(15, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:04:34'),
(16, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:05:05'),
(17, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:05:36'),
(18, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:06:07'),
(19, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:06:38'),
(20, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:07:09'),
(21, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:07:40'),
(22, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:08:11'),
(23, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:08:42'),
(24, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:09:13'),
(25, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:09:44'),
(26, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:10:15'),
(27, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:10:46'),
(28, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:11:17'),
(29, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:11:48'),
(30, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:12:19'),
(31, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:12:50'),
(32, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:13:21'),
(33, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:13:52'),
(34, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:14:23'),
(35, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:14:54'),
(36, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:15:25'),
(37, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:15:56'),
(38, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:16:27'),
(39, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:16:58'),
(40, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:17:29'),
(41, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:18:00'),
(42, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:18:31'),
(43, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:19:02'),
(44, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:19:33'),
(45, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:20:04'),
(46, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:20:35'),
(47, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:21:06'),
(48, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:21:37'),
(49, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:22:08'),
(50, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:22:39'),
(51, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:23:10'),
(52, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:23:41'),
(53, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:24:12'),
(54, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:24:43'),
(55, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:25:14'),
(56, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:25:45'),
(57, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:26:16'),
(58, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:26:47'),
(59, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:27:18'),
(60, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:27:49'),
(61, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:28:20'),
(62, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:28:51'),
(63, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:29:22'),
(64, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:29:53'),
(65, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:30:24'),
(66, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:30:55'),
(67, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:31:26'),
(68, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:31:57'),
(69, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:32:28'),
(70, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:32:59'),
(71, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:33:30'),
(72, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:34:01'),
(73, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:34:32'),
(74, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:35:03'),
(75, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:35:34'),
(76, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:36:05'),
(77, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:36:36'),
(78, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:37:07'),
(79, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:37:38'),
(80, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:38:09'),
(81, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:38:40'),
(82, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:39:11'),
(83, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:39:42'),
(84, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:40:13'),
(85, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:40:44'),
(86, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:41:15'),
(87, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 18:57:53'),
(88, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:00:34'),
(89, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:01:06'),
(90, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:01:37'),
(91, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:02:07'),
(92, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:02:39'),
(93, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:03:10'),
(94, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:03:41'),
(95, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:04:12'),
(96, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:04:43'),
(97, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:05:14'),
(98, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:05:45'),
(99, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:06:16'),
(100, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:06:47'),
(101, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:07:18'),
(102, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:07:49'),
(103, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:08:20'),
(104, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:08:51'),
(105, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:09:22'),
(106, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:09:53'),
(107, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:10:24'),
(108, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:10:55'),
(109, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:11:26'),
(110, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:11:57'),
(111, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:12:28'),
(112, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:12:59'),
(113, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:13:30'),
(114, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:14:01'),
(115, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:14:32'),
(116, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:15:03'),
(117, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:15:34'),
(118, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:16:05'),
(119, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:16:36'),
(120, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:17:07'),
(121, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:17:38'),
(122, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:18:09'),
(123, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:18:40'),
(124, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:19:11'),
(125, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:19:42'),
(126, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:20:13'),
(127, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:20:44'),
(128, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:21:15'),
(129, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:21:46'),
(130, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:22:17'),
(131, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:22:48'),
(132, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:23:19'),
(133, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:23:50'),
(134, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:24:21'),
(135, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:24:52'),
(136, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:25:23'),
(137, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:25:54'),
(138, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:26:25'),
(139, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:26:56'),
(140, 'Problem Reported', 'Driver Jane Smith reported a problem: traffic - there is more traffic', 'warning', 'unread', '2025-10-17 19:27:27'),
(141, 'Trip Completed', 'Driver Jane Smith has completed trip #2 from Nyabugogo to Muhanga', 'success', 'unread', '2025-10-17 22:05:49'),
(142, 'Trip Completed', 'Driver Mike Johnson has completed trip #3 from Nyabugogo to Musanze', 'success', 'unread', '2025-10-20 07:46:00'),
(143, 'Problem Reported', 'Driver Mike Johnson reported a problem: emergency - wesdftgyhjkjnvbcx', 'warning', 'unread', '2025-10-20 07:49:20'),
(144, 'Problem Reported', 'Driver Mike Johnson reported a problem: emergency - wesdftgyhjkjnvbcx', 'warning', 'unread', '2025-10-20 07:49:51'),
(145, 'Problem Reported', 'Driver Mike Johnson reported a problem: emergency - wesdftgyhjkjnvbcx', 'warning', 'unread', '2025-10-20 07:50:22'),
(146, 'Problem Reported', 'Driver Mike Johnson reported a problem: emergency - wesdftgyhjkjnvbcx', 'warning', 'unread', '2025-10-20 07:50:53'),
(147, 'Problem Reported', 'Driver Mike Johnson reported a problem: emergency - wesdftgyhjkjnvbcx', 'warning', 'unread', '2025-10-20 07:51:24'),
(148, 'Problem Reported', 'Driver Mike Johnson reported a problem: emergency - wesdftgyhjkjnvbcx', 'warning', 'unread', '2025-10-20 07:51:55'),
(149, 'Problem Reported', 'Driver Mike Johnson reported a problem: emergency - wesdftgyhjkjnvbcx', 'warning', 'unread', '2025-10-20 07:52:26'),
(150, 'Problem Reported', 'Driver Mike Johnson reported a problem: emergency - wesdftgyhjkjnvbcx', 'warning', 'unread', '2025-10-20 07:52:57'),
(151, 'Problem Reported', 'Driver Mike Johnson reported a problem: emergency - wesdftgyhjkjnvbcx', 'warning', 'unread', '2025-10-20 07:53:27'),
(152, 'Problem Reported', 'Driver Mike Johnson reported a problem: emergency - wesdftgyhjkjnvbcx', 'warning', 'unread', '2025-10-20 07:53:57'),
(153, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 07:54:16'),
(154, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 07:54:47'),
(155, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 07:55:18'),
(156, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 07:55:49'),
(157, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 07:56:20'),
(158, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 07:56:51'),
(159, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 07:57:22'),
(160, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 07:57:53'),
(161, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 07:58:24'),
(162, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 07:58:55'),
(163, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 07:59:26'),
(164, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 07:59:57'),
(165, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:00:28'),
(166, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:00:59'),
(167, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:01:30'),
(168, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:02:01'),
(169, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:02:32'),
(170, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:03:03'),
(171, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:03:34'),
(172, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:04:05'),
(173, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:04:36'),
(174, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:05:07'),
(175, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:05:38'),
(176, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:06:09'),
(177, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:06:40'),
(178, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:07:11'),
(179, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:07:42'),
(180, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:08:13'),
(181, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:08:44'),
(182, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:09:15'),
(183, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:09:46'),
(184, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:10:17'),
(185, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:10:48'),
(186, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:11:19'),
(187, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:11:50'),
(188, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:12:21'),
(189, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:12:52'),
(190, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:13:23'),
(191, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:13:54'),
(192, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:14:25'),
(193, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:14:56'),
(194, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:15:27'),
(195, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:15:58'),
(196, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:16:29'),
(197, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:17:00'),
(198, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:17:31'),
(199, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:18:02'),
(200, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:18:33'),
(201, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:19:04'),
(202, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:19:35'),
(203, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:20:06'),
(204, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:20:37'),
(205, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:21:08'),
(206, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:21:39'),
(207, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:22:10'),
(208, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:22:41'),
(209, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:23:12'),
(210, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:23:43'),
(211, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:24:14'),
(212, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:24:45'),
(213, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:25:16'),
(214, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:25:47'),
(215, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:26:18'),
(216, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:26:49'),
(217, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:27:20'),
(218, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:27:51'),
(219, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:28:22'),
(220, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:28:53'),
(221, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:29:24'),
(222, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:29:55'),
(223, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:30:26'),
(224, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:30:57'),
(225, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:31:28'),
(226, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:31:59'),
(227, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:32:30'),
(228, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:33:01'),
(229, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:33:32'),
(230, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:34:03'),
(231, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:34:34'),
(232, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:35:04'),
(233, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:35:35'),
(234, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:36:06'),
(235, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:36:37'),
(236, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:37:08'),
(237, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:37:39'),
(238, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:38:10'),
(239, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:38:41'),
(240, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:39:12'),
(241, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:39:43'),
(242, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:40:14'),
(243, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:40:45'),
(244, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:41:16'),
(245, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:41:47'),
(246, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:42:18'),
(247, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:42:49'),
(248, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:43:20'),
(249, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:43:51'),
(250, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:44:22'),
(251, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:44:53'),
(252, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:45:24'),
(253, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:45:55'),
(254, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:46:26'),
(255, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:46:57'),
(256, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:47:28'),
(257, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:47:59'),
(258, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:48:30'),
(259, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:49:01'),
(260, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:49:32'),
(261, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:50:03'),
(262, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:50:34'),
(263, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:51:05'),
(264, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:51:36'),
(265, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:52:07'),
(266, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:52:38'),
(267, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:53:09'),
(268, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:53:40'),
(269, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:54:11'),
(270, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:54:42'),
(271, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:55:13'),
(272, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:55:44'),
(273, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:56:15'),
(274, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:56:46'),
(275, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:57:17'),
(276, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:57:48'),
(277, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:58:19'),
(278, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:58:50'),
(279, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:59:21'),
(280, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 08:59:52'),
(281, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:00:23'),
(282, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:00:54'),
(283, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:01:25'),
(284, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:01:56'),
(285, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:02:27'),
(286, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:02:58'),
(287, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:03:29'),
(288, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:04:00'),
(289, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:04:31'),
(290, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:05:02'),
(291, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:05:33'),
(292, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:06:04'),
(293, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:06:35'),
(294, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:07:06'),
(295, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:07:37'),
(296, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:08:08'),
(297, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:08:39'),
(298, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:09:10'),
(299, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:09:41'),
(300, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:10:12'),
(301, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:10:43'),
(302, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:11:14'),
(303, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:11:45'),
(304, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:12:16'),
(305, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:12:47'),
(306, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:13:18'),
(307, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:13:49'),
(308, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:14:20'),
(309, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:14:51'),
(310, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:15:22'),
(311, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:15:53'),
(312, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:16:24'),
(313, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:16:55'),
(314, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:17:26'),
(315, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:17:57'),
(316, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:18:28'),
(317, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:18:59'),
(318, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:19:30'),
(319, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:20:01'),
(320, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:20:32'),
(321, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:21:03'),
(322, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:21:34'),
(323, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:22:05'),
(324, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:22:36'),
(325, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:23:07'),
(326, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:23:38'),
(327, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:24:09'),
(328, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:24:40'),
(329, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:25:11'),
(330, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:25:42'),
(331, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:26:13'),
(332, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:26:44'),
(333, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:27:15'),
(334, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:27:46'),
(335, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:28:17'),
(336, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:28:48'),
(337, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:29:19'),
(338, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:29:50'),
(339, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:30:21'),
(340, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:30:52'),
(341, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:31:23'),
(342, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:31:54'),
(343, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:32:25'),
(344, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:32:56'),
(345, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:33:27'),
(346, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:33:58'),
(347, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:34:29'),
(348, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:35:00'),
(349, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:35:31'),
(350, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:36:02'),
(351, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:36:33'),
(352, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:37:04'),
(353, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:37:35'),
(354, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:38:06'),
(355, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:38:37'),
(356, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:39:08'),
(357, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:39:39'),
(358, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:40:10'),
(359, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:40:41'),
(360, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:41:12'),
(361, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:41:43'),
(362, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:42:14'),
(363, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:42:45'),
(364, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:43:16'),
(365, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:43:47'),
(366, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:44:18'),
(367, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:44:49');
INSERT INTO `admin_notifications` (`notification_id`, `title`, `message`, `type`, `status`, `created_at`) VALUES
(368, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:45:20'),
(369, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:45:51'),
(370, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:46:22'),
(371, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:46:53'),
(372, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:47:24'),
(373, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:47:55'),
(374, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:48:26'),
(375, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:48:57'),
(376, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:49:28'),
(377, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:49:59'),
(378, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:50:30'),
(379, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:51:01'),
(380, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:51:32'),
(381, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:52:03'),
(382, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:52:34'),
(383, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:53:05'),
(384, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:53:36'),
(385, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:54:07'),
(386, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:54:38'),
(387, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:55:09'),
(388, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:55:40'),
(389, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:56:11'),
(390, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:56:42'),
(391, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:57:13'),
(392, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:57:43'),
(393, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:58:14'),
(394, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:58:45'),
(395, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:59:16'),
(396, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 09:59:47'),
(397, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 10:00:18'),
(398, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 10:00:49'),
(399, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 10:01:20'),
(400, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 11:57:58'),
(401, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 11:58:28'),
(402, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 11:58:59'),
(403, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 11:59:30'),
(404, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:00:01'),
(405, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:00:32'),
(406, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:01:03'),
(407, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:01:34'),
(408, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:02:05'),
(409, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:02:36'),
(410, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:03:07'),
(411, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:03:38'),
(412, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:04:09'),
(413, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:04:40'),
(414, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:05:11'),
(415, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:05:42'),
(416, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:06:13'),
(417, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:06:44'),
(418, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:07:15'),
(419, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:07:46'),
(420, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:08:17'),
(421, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:08:48'),
(422, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:09:19'),
(423, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:09:50'),
(424, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:10:21'),
(425, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:10:52'),
(426, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:11:23'),
(427, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:11:54'),
(428, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:12:25'),
(429, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:12:56'),
(430, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:13:27'),
(431, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:13:58'),
(432, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:14:29'),
(433, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:15:00'),
(434, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:15:31'),
(435, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:16:02'),
(436, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:16:33'),
(437, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:17:04'),
(438, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:17:35'),
(439, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:18:06'),
(440, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:18:37'),
(441, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:19:08'),
(442, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:19:39'),
(443, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:20:10'),
(444, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:20:41'),
(445, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:21:12'),
(446, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:21:43'),
(447, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:22:14'),
(448, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:22:45'),
(449, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:23:16'),
(450, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:23:47'),
(451, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:24:18'),
(452, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:24:49'),
(453, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:25:20'),
(454, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:25:51'),
(455, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:26:22'),
(456, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:26:53'),
(457, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:27:24'),
(458, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:27:55'),
(459, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:28:26'),
(460, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:28:57'),
(461, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:29:28'),
(462, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:29:59'),
(463, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:30:30'),
(464, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:31:01'),
(465, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:31:32'),
(466, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:32:03'),
(467, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:32:34'),
(468, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:33:05'),
(469, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:33:36'),
(470, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:34:07'),
(471, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:34:38'),
(472, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:35:09'),
(473, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:35:40'),
(474, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:36:11'),
(475, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:36:42'),
(476, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:37:13'),
(477, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:37:44'),
(478, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:38:15'),
(479, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:38:46'),
(480, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:39:17'),
(481, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:39:48'),
(482, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:40:19'),
(483, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:40:50'),
(484, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:41:21'),
(485, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:41:52'),
(486, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:42:23'),
(487, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:42:54'),
(488, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:43:25'),
(489, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:43:56'),
(490, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:44:27'),
(491, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:44:58'),
(492, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:45:29'),
(493, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:46:00'),
(494, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:46:31'),
(495, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:47:02'),
(496, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:47:33'),
(497, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:48:04'),
(498, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:48:35'),
(499, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:49:06'),
(500, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:49:37'),
(501, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:50:08'),
(502, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:50:39'),
(503, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:51:10'),
(504, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:51:40'),
(505, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:52:11'),
(506, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:52:42'),
(507, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:53:13'),
(508, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:53:44'),
(509, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:54:15'),
(510, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:54:46'),
(511, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:55:17'),
(512, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:55:48'),
(513, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:56:19'),
(514, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:56:50'),
(515, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:57:21'),
(516, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:57:52'),
(517, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:58:23'),
(518, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:58:54'),
(519, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:59:25'),
(520, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 12:59:56'),
(521, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:00:27'),
(522, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:00:58'),
(523, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:01:29'),
(524, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:02:00'),
(525, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:02:31'),
(526, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:03:02'),
(527, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:03:33'),
(528, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:04:04'),
(529, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:04:35'),
(530, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:05:06'),
(531, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:05:37'),
(532, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:06:08'),
(533, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:06:39'),
(534, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:07:10'),
(535, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:07:41'),
(536, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:08:12'),
(537, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:08:43'),
(538, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:09:14'),
(539, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:09:45'),
(540, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:10:16'),
(541, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:10:47'),
(542, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:11:18'),
(543, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:11:49'),
(544, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:12:20'),
(545, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:12:51'),
(546, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:13:22'),
(547, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:13:53'),
(548, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:14:24'),
(549, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:14:55'),
(550, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:15:26'),
(551, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:15:57'),
(552, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:16:28'),
(553, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:16:59'),
(554, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:17:30'),
(555, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:18:01'),
(556, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:18:32'),
(557, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:19:03'),
(558, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:19:34'),
(559, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:20:05'),
(560, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:20:36'),
(561, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:21:07'),
(562, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:21:38'),
(563, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:22:09'),
(564, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:22:40'),
(565, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:23:11'),
(566, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:23:42'),
(567, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:24:13'),
(568, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:24:44'),
(569, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:25:15'),
(570, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:25:46'),
(571, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:26:17'),
(572, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:26:48'),
(573, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:27:19'),
(574, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:27:50'),
(575, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:28:21'),
(576, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:28:52'),
(577, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:29:23'),
(578, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:29:54'),
(579, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:30:25'),
(580, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:30:56'),
(581, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:31:27'),
(582, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:31:58'),
(583, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:32:29'),
(584, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:33:00'),
(585, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:33:31'),
(586, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:34:02'),
(587, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:34:33'),
(588, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:35:04'),
(589, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:35:35'),
(590, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:36:06'),
(591, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:36:37'),
(592, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:37:08'),
(593, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:37:39'),
(594, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:38:10'),
(595, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:38:41'),
(596, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:39:12'),
(597, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:39:43'),
(598, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:40:14'),
(599, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:40:45'),
(600, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:41:16'),
(601, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:41:47'),
(602, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:42:18'),
(603, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:42:49'),
(604, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:43:20'),
(605, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:43:51'),
(606, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:44:22'),
(607, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:44:53'),
(608, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:45:24'),
(609, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:45:55'),
(610, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:46:26'),
(611, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:46:57'),
(612, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:47:28'),
(613, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:47:59'),
(614, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:48:30'),
(615, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:49:01'),
(616, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:49:32'),
(617, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:50:03'),
(618, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:50:34'),
(619, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:51:05'),
(620, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:51:36'),
(621, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:52:07'),
(622, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:52:38'),
(623, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:53:09'),
(624, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:53:40'),
(625, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:54:11'),
(626, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:54:42'),
(627, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:55:13'),
(628, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:55:44'),
(629, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:56:15'),
(630, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:56:46'),
(631, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:57:17'),
(632, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:57:48'),
(633, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:58:19'),
(634, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:58:50'),
(635, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:59:21'),
(636, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 13:59:52'),
(637, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:00:23'),
(638, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:00:54'),
(639, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:01:25'),
(640, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:01:56'),
(641, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:02:27'),
(642, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:02:58'),
(643, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:03:29'),
(644, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:04:00'),
(645, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:04:31'),
(646, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:05:02'),
(647, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:05:33'),
(648, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:06:04'),
(649, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:06:35'),
(650, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:07:06'),
(651, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:07:37'),
(652, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:08:08'),
(653, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:08:39'),
(654, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:09:10'),
(655, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:09:41'),
(656, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:10:12'),
(657, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:10:43'),
(658, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:11:14'),
(659, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:11:45'),
(660, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:12:16'),
(661, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:12:47'),
(662, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:13:18'),
(663, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:13:49'),
(664, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:14:20'),
(665, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:14:51'),
(666, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:15:22'),
(667, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:15:53'),
(668, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:16:24'),
(669, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:16:55'),
(670, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:17:26'),
(671, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:17:57'),
(672, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:18:28'),
(673, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:18:59'),
(674, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:19:30'),
(675, 'Problem Reported', 'Driver Mike Johnson reported a problem: mechanical - asdfgh', 'warning', 'unread', '2025-10-20 14:20:02');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `number_of_seats` int(11) NOT NULL CHECK (`number_of_seats` > 0),
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `customer_id`, `trip_id`, `number_of_seats`, `booking_date`) VALUES
(2, 1, 1, 1, '2025-10-19 07:55:36'),
(3, 1, 1, 1, '2025-10-19 08:02:06'),
(4, 1, 1, 1, '2025-10-19 08:08:41'),
(5, 1, 1, 1, '2025-10-19 08:12:04'),
(6, 1, 1, 1, '2025-10-19 08:19:22'),
(7, 1, 3, 1, '2025-10-19 21:57:19'),
(8, 5, 1, 1, '2025-10-20 16:25:30'),
(9, 8, 1, 1, '2025-10-20 16:51:35'),
(11, 10, 1, 1, '2025-10-20 16:58:25'),
(12, 11, 1, 1, '2025-10-20 17:01:41'),
(13, 12, 1, 1, '2025-10-21 09:50:23'),
(14, 13, 1, 1, '2025-10-21 09:50:48'),
(15, 14, 1, 1, '2025-10-21 09:53:49'),
(16, 15, 1, 1, '2025-10-21 10:24:06'),
(17, 16, 1, 5, '2025-10-21 10:35:01'),
(18, 17, 1, 4, '2025-10-21 10:57:07'),
(19, 18, 1, 3, '2025-10-22 07:44:18'),
(20, 19, 1, 4, '2025-10-22 07:46:28'),
(21, 20, 1, 1, '2025-10-22 07:58:47'),
(22, 21, 1, 1, '2025-10-22 09:03:29');

-- --------------------------------------------------------

--
-- Table structure for table `buses`
--

CREATE TABLE `buses` (
  `bus_id` int(11) NOT NULL,
  `plates_number` varchar(20) NOT NULL,
  `model` varchar(50) NOT NULL,
  `number_of_seats` int(11) NOT NULL CHECK (`number_of_seats` > 0),
  `status` enum('Active','Maintenance','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buses`
--

INSERT INTO `buses` (`bus_id`, `plates_number`, `model`, `number_of_seats`, `status`, `created_at`, `updated_at`) VALUES
(1, 'RAA222A', 'Coaster', 30, 'Active', '2025-10-17 07:17:22', '2025-10-19 22:14:37'),
(2, 'RAA333A', 'Coaster', 30, 'Maintenance', '2025-10-17 07:17:45', '2025-10-17 07:18:01'),
(3, 'RAA444A', 'Coaster', 30, 'Active', '2025-10-17 07:21:52', '2025-10-19 22:14:37'),
(4, 'RAA555A', 'Hiace', 18, 'Active', '2025-10-17 17:48:38', '2025-10-19 22:14:37');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `firstname`, `lastname`, `contact`, `email`, `created_at`, `updated_at`) VALUES
(1, 'Elyse', 'TUYISHIMIRE', '0789588161', 'telyse50@gmail.com', '2025-10-14 20:04:29', '2025-10-14 20:30:01'),
(5, 'Elyse', 'TUYISHIMIRE', '0789588161', 'telyse50@gmail.com', '2025-10-20 16:25:30', '2025-10-20 16:25:30'),
(8, 'Elyse', 'TUYISHIMIRE', '0789588161', 'telyse50@gmail.com', '2025-10-20 16:51:35', '2025-10-20 16:51:35'),
(10, 'Elyse', 'Paccy', '0789588161', 'telyse50@gmail.com', '2025-10-20 16:58:25', '2025-10-20 16:58:25'),
(11, 'Elyse', 'TUYISHIMIRE', '0789588161', 'telyse50@gmail.com', '2025-10-20 17:01:41', '2025-10-20 17:01:41'),
(12, 'Elyse', 'herve', '0789588161', 'telyse50@gmail.com', '2025-10-21 09:50:23', '2025-10-21 09:50:23'),
(13, 'Elyse', 'TUYISHIMIRE', '0789588161', 'telyse50@gmail.com', '2025-10-21 09:50:48', '2025-10-21 09:50:48'),
(14, 'Elyse', 'TUYISHIMIRE', '0789588161', 'telyse50@gmail.com', '2025-10-21 09:53:49', '2025-10-21 09:53:49'),
(15, 'Elyse', 'TUYISHIMIRE', '0789588161', 'telyse50@gmail.com', '2025-10-21 10:24:06', '2025-10-21 10:24:06'),
(16, 'Elyse', 'TUYISHIMIRE', '0789588161', 'telyse50@gmail.com', '2025-10-21 10:35:01', '2025-10-21 10:35:01'),
(17, 'ange', 'kelly', '0789588161', 'ange@gmail.com', '2025-10-21 10:57:07', '2025-10-21 10:57:07'),
(18, 'bernice', 'berwa', '0789588151', 'telyse50@gmail.com', '2025-10-22 07:44:18', '2025-10-22 07:44:18'),
(19, 'fabrice', 'nshuti', '0789588151', 'fab@gmail.com', '2025-10-22 07:46:28', '2025-10-22 07:46:28'),
(20, 'carine', 'ange', '0789588151', 'ange44@gmail.com', '2025-10-22 07:58:47', '2025-10-22 07:58:47'),
(21, 'Elyse', 'TUYISHIMIRE', '0789588161', 'telyse50@gmail.com', '2025-10-22 09:03:29', '2025-10-22 09:03:29');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `driver_id` int(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `license` varchar(20) NOT NULL,
  `status` enum('active','on_leave','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`driver_id`, `user_id`, `name`, `contact`, `license`, `status`, `created_at`, `updated_at`) VALUES
(1, 7, 'John Doe', '+250788123456', 'DL001234', 'active', '2025-10-14 07:34:20', '2025-10-17 10:04:18'),
(2, 3, 'Jane Smith', '+250788654321', 'DL005679', 'active', '2025-10-14 07:34:20', '2025-10-15 19:47:53'),
(3, 6, 'Mike Johnson', '+250788987654', 'DL009876', 'active', '2025-10-14 07:34:20', '2025-10-17 10:04:36');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL CHECK (`amount` > 0),
  `payment_method` enum('momo','airtel_money') NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_status` enum('completed') DEFAULT NULL,
  `time_paid` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `amount`, `payment_method`, `transaction_id`, `payment_status`, `time_paid`, `created_at`) VALUES
(1, 9, 5000.00, 'momo', '2015486474', 'completed', '2025-10-20 16:51:35', '2025-10-20 16:51:35'),
(3, 11, 5000.00, 'momo', '853646595', 'completed', '2025-10-20 16:58:25', '2025-10-20 16:58:25'),
(4, 12, 5000.00, 'momo', '1502370823', 'completed', '2025-10-20 17:01:41', '2025-10-20 17:01:41'),
(5, 13, 5000.00, 'momo', '330332824', 'completed', '2025-10-21 09:50:23', '2025-10-21 09:50:23'),
(6, 14, 5000.00, 'momo', '1369285673', 'completed', '2025-10-21 09:50:48', '2025-10-21 09:50:48'),
(7, 15, 5000.00, 'momo', '1400880464', 'completed', '2025-10-21 09:53:49', '2025-10-21 09:53:49'),
(8, 16, 5000.00, 'momo', '488684158', 'completed', '2025-10-21 10:24:06', '2025-10-21 10:24:06'),
(9, 17, 25000.00, 'momo', '199249405', 'completed', '2025-10-21 10:35:01', '2025-10-21 10:35:01'),
(10, 18, 20000.00, 'momo', '744306672', 'completed', '2025-10-21 10:57:07', '2025-10-21 10:57:07'),
(11, 19, 15000.00, 'momo', '1059347127', 'completed', '2025-10-22 07:44:18', '2025-10-22 07:44:18'),
(12, 20, 20000.00, 'momo', '1602511681', 'completed', '2025-10-22 07:46:28', '2025-10-22 07:46:28'),
(13, 21, 5000.00, 'momo', '1667416667', 'completed', '2025-10-22 07:58:47', '2025-10-22 07:58:47'),
(14, 22, 5000.00, 'momo', '26273121', 'completed', '2025-10-22 09:03:29', '2025-10-22 09:03:29');

-- --------------------------------------------------------

--
-- Table structure for table `problem_reports`
--

CREATE TABLE `problem_reports` (
  `report_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `trip_id` int(11) DEFAULT NULL,
  `problem_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `status` enum('reported','in_progress','resolved') DEFAULT 'reported',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `problem_reports`
--

INSERT INTO `problem_reports` (`report_id`, `driver_id`, `trip_id`, `problem_type`, `description`, `status`, `created_at`) VALUES
(1, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 17:58:22'),
(2, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 17:58:53'),
(3, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 17:59:24'),
(4, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 17:59:55'),
(5, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:00:26'),
(6, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:00:57'),
(7, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:01:28'),
(8, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:01:59'),
(9, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:02:30'),
(10, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:03:01'),
(11, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:03:32'),
(12, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:04:03'),
(13, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:04:34'),
(14, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:05:05'),
(15, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:05:36'),
(16, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:06:07'),
(17, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:06:38'),
(18, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:07:09'),
(19, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:07:40'),
(20, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:08:11'),
(21, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:08:42'),
(22, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:09:13'),
(23, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:09:44'),
(24, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:10:15'),
(25, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:10:46'),
(26, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:11:17'),
(27, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:11:48'),
(28, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:12:19'),
(29, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:12:50'),
(30, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:13:21'),
(31, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:13:52'),
(32, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:14:23'),
(33, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:14:54'),
(34, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:15:25'),
(35, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:15:56'),
(36, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:16:27'),
(37, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:16:58'),
(38, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:17:29'),
(39, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:18:00'),
(40, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:18:31'),
(41, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:19:02'),
(42, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:19:33'),
(43, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:20:04'),
(44, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:20:35'),
(45, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:21:06'),
(46, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:21:37'),
(47, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:22:08'),
(48, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:22:39'),
(49, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:23:10'),
(50, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:23:41'),
(51, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:24:12'),
(52, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:24:43'),
(53, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:25:14'),
(54, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:25:45'),
(55, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:26:16'),
(56, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:26:47'),
(57, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:27:18'),
(58, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:27:49'),
(59, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:28:20'),
(60, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:28:51'),
(61, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:29:22'),
(62, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:29:53'),
(63, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:30:24'),
(64, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:30:55'),
(65, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:31:26'),
(66, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:31:57'),
(67, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:32:28'),
(68, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:32:59'),
(69, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:33:30'),
(70, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:34:01'),
(71, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:34:32'),
(72, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:35:03'),
(73, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:35:34'),
(74, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:36:05'),
(75, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:36:36'),
(76, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:37:07'),
(77, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:37:38'),
(78, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:38:09'),
(79, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:38:40'),
(80, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:39:11'),
(81, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:39:42'),
(82, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:40:13'),
(83, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:40:44'),
(84, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:41:15'),
(85, 2, 2, 'traffic', 'there is more traffic', 'reported', '2025-10-17 18:57:53'),
(86, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:00:34'),
(87, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:01:06'),
(88, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:01:37'),
(89, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:02:07'),
(90, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:02:39'),
(91, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:03:10'),
(92, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:03:41'),
(93, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:04:12'),
(94, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:04:43'),
(95, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:05:14'),
(96, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:05:45'),
(97, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:06:16'),
(98, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:06:47'),
(99, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:07:18'),
(100, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:07:49'),
(101, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:08:20'),
(102, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:08:51'),
(103, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:09:22'),
(104, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:09:53'),
(105, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:10:24'),
(106, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:10:55'),
(107, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:11:26'),
(108, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:11:57'),
(109, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:12:28'),
(110, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:12:59'),
(111, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:13:30'),
(112, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:14:01'),
(113, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:14:32'),
(114, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:15:03'),
(115, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:15:34'),
(116, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:16:05'),
(117, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:16:36'),
(118, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:17:07'),
(119, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:17:38'),
(120, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:18:09'),
(121, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:18:40'),
(122, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:19:11'),
(123, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:19:42'),
(124, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:20:13'),
(125, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:20:44'),
(126, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:21:15'),
(127, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:21:46'),
(128, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:22:17'),
(129, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:22:48'),
(130, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:23:19'),
(131, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:23:50'),
(132, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:24:21'),
(133, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:24:52'),
(134, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:25:23'),
(135, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:25:54'),
(136, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:26:25'),
(137, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:26:56'),
(138, 2, NULL, 'traffic', 'there is more traffic', 'reported', '2025-10-17 19:27:27'),
(139, 3, NULL, 'emergency', 'wesdftgyhjkjnvbcx', 'reported', '2025-10-20 07:49:20'),
(140, 3, NULL, 'emergency', 'wesdftgyhjkjnvbcx', 'reported', '2025-10-20 07:49:51'),
(141, 3, NULL, 'emergency', 'wesdftgyhjkjnvbcx', 'reported', '2025-10-20 07:50:22'),
(142, 3, NULL, 'emergency', 'wesdftgyhjkjnvbcx', 'reported', '2025-10-20 07:50:53'),
(143, 3, NULL, 'emergency', 'wesdftgyhjkjnvbcx', 'reported', '2025-10-20 07:51:24'),
(144, 3, NULL, 'emergency', 'wesdftgyhjkjnvbcx', 'reported', '2025-10-20 07:51:55'),
(145, 3, NULL, 'emergency', 'wesdftgyhjkjnvbcx', 'reported', '2025-10-20 07:52:26'),
(146, 3, NULL, 'emergency', 'wesdftgyhjkjnvbcx', 'reported', '2025-10-20 07:52:57'),
(147, 3, NULL, 'emergency', 'wesdftgyhjkjnvbcx', 'reported', '2025-10-20 07:53:27'),
(148, 3, NULL, 'emergency', 'wesdftgyhjkjnvbcx', 'reported', '2025-10-20 07:53:57'),
(149, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 07:54:16'),
(150, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 07:54:47'),
(151, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 07:55:18'),
(152, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 07:55:49'),
(153, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 07:56:20'),
(154, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 07:56:51'),
(155, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 07:57:22'),
(156, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 07:57:53'),
(157, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 07:58:24'),
(158, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 07:58:55'),
(159, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 07:59:26'),
(160, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 07:59:57'),
(161, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:00:28'),
(162, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:00:59'),
(163, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:01:30'),
(164, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:02:01'),
(165, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:02:32'),
(166, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:03:03'),
(167, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:03:34'),
(168, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:04:05'),
(169, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:04:36'),
(170, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:05:07'),
(171, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:05:38'),
(172, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:06:09'),
(173, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:06:40'),
(174, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:07:11'),
(175, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:07:42'),
(176, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:08:13'),
(177, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:08:44'),
(178, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:09:15'),
(179, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:09:46'),
(180, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:10:17'),
(181, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:10:48'),
(182, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:11:19'),
(183, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:11:50'),
(184, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:12:21'),
(185, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:12:52'),
(186, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:13:23'),
(187, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:13:54'),
(188, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:14:25'),
(189, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:14:56'),
(190, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:15:27'),
(191, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:15:58'),
(192, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:16:29'),
(193, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:17:00'),
(194, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:17:31'),
(195, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:18:02'),
(196, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:18:33'),
(197, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:19:04'),
(198, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:19:35'),
(199, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:20:06'),
(200, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:20:37'),
(201, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:21:08'),
(202, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:21:39'),
(203, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:22:10'),
(204, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:22:41'),
(205, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:23:12'),
(206, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:23:43'),
(207, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:24:14'),
(208, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:24:45'),
(209, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:25:16'),
(210, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:25:47'),
(211, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:26:18'),
(212, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:26:49'),
(213, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:27:20'),
(214, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:27:51'),
(215, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:28:22'),
(216, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:28:53'),
(217, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:29:24'),
(218, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:29:55'),
(219, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:30:26'),
(220, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:30:57'),
(221, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:31:28'),
(222, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:31:59'),
(223, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:32:30'),
(224, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:33:01'),
(225, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:33:32'),
(226, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:34:03'),
(227, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:34:34'),
(228, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:35:04'),
(229, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:35:35'),
(230, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:36:06'),
(231, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:36:37'),
(232, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:37:08'),
(233, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:37:39'),
(234, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:38:10'),
(235, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:38:41'),
(236, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:39:12'),
(237, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:39:43'),
(238, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:40:14'),
(239, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:40:45'),
(240, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:41:16'),
(241, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:41:47'),
(242, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:42:18'),
(243, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:42:49'),
(244, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:43:20'),
(245, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:43:51'),
(246, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:44:22'),
(247, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:44:53'),
(248, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:45:24'),
(249, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:45:55'),
(250, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:46:26'),
(251, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:46:57'),
(252, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:47:28'),
(253, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:47:59'),
(254, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:48:30'),
(255, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:49:01'),
(256, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:49:32'),
(257, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:50:03'),
(258, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:50:34'),
(259, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:51:05'),
(260, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:51:36'),
(261, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:52:07'),
(262, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:52:38'),
(263, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:53:09'),
(264, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:53:40'),
(265, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:54:11'),
(266, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:54:42'),
(267, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:55:13'),
(268, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:55:44'),
(269, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:56:15'),
(270, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:56:46'),
(271, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:57:17'),
(272, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:57:48'),
(273, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:58:19'),
(274, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:58:50'),
(275, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:59:21'),
(276, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 08:59:52'),
(277, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:00:23'),
(278, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:00:54'),
(279, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:01:25'),
(280, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:01:56'),
(281, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:02:27'),
(282, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:02:58'),
(283, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:03:29'),
(284, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:04:00'),
(285, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:04:31'),
(286, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:05:02'),
(287, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:05:33'),
(288, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:06:04'),
(289, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:06:35'),
(290, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:07:06'),
(291, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:07:37'),
(292, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:08:08'),
(293, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:08:39'),
(294, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:09:10'),
(295, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:09:41'),
(296, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:10:12'),
(297, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:10:43'),
(298, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:11:14'),
(299, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:11:45'),
(300, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:12:16'),
(301, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:12:47'),
(302, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:13:18'),
(303, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:13:49'),
(304, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:14:20'),
(305, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:14:51'),
(306, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:15:22'),
(307, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:15:53'),
(308, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:16:24'),
(309, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:16:55'),
(310, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:17:26'),
(311, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:17:57'),
(312, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:18:28'),
(313, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:18:59'),
(314, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:19:30'),
(315, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:20:01'),
(316, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:20:32'),
(317, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:21:03'),
(318, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:21:34'),
(319, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:22:05'),
(320, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:22:36'),
(321, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:23:07'),
(322, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:23:38'),
(323, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:24:09'),
(324, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:24:40'),
(325, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:25:11'),
(326, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:25:42'),
(327, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:26:13'),
(328, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:26:44'),
(329, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:27:15'),
(330, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:27:46'),
(331, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:28:17'),
(332, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:28:48'),
(333, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:29:19'),
(334, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:29:50'),
(335, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:30:21'),
(336, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:30:52'),
(337, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:31:23'),
(338, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:31:54'),
(339, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:32:25'),
(340, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:32:56'),
(341, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:33:27'),
(342, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:33:58'),
(343, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:34:29'),
(344, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:35:00'),
(345, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:35:31'),
(346, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:36:02'),
(347, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:36:33'),
(348, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:37:04'),
(349, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:37:35'),
(350, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:38:06'),
(351, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:38:37'),
(352, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:39:08'),
(353, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:39:39'),
(354, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:40:10'),
(355, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:40:41'),
(356, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:41:12'),
(357, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:41:43'),
(358, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:42:14'),
(359, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:42:45'),
(360, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:43:16'),
(361, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:43:47'),
(362, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:44:18'),
(363, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:44:49'),
(364, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:45:20'),
(365, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:45:51'),
(366, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:46:22'),
(367, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:46:53'),
(368, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:47:24'),
(369, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:47:55'),
(370, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:48:26'),
(371, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:48:57'),
(372, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:49:28'),
(373, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:49:59'),
(374, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:50:30'),
(375, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:51:01'),
(376, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:51:32'),
(377, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:52:03'),
(378, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:52:34'),
(379, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:53:05'),
(380, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:53:36'),
(381, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:54:07'),
(382, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:54:38'),
(383, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:55:09'),
(384, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:55:40'),
(385, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:56:11'),
(386, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:56:42'),
(387, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:57:13'),
(388, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:57:43'),
(389, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:58:14'),
(390, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:58:45'),
(391, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:59:16'),
(392, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 09:59:47'),
(393, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 10:00:18'),
(394, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 10:00:49'),
(395, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 10:01:20'),
(396, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 11:57:58'),
(397, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 11:58:28'),
(398, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 11:58:59'),
(399, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 11:59:30'),
(400, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:00:01'),
(401, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:00:32'),
(402, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:01:03'),
(403, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:01:34'),
(404, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:02:05'),
(405, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:02:36'),
(406, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:03:07'),
(407, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:03:38'),
(408, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:04:09'),
(409, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:04:40'),
(410, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:05:11'),
(411, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:05:42'),
(412, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:06:13'),
(413, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:06:44'),
(414, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:07:15'),
(415, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:07:46'),
(416, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:08:17'),
(417, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:08:48'),
(418, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:09:19'),
(419, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:09:50'),
(420, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:10:21'),
(421, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:10:52'),
(422, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:11:23'),
(423, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:11:54'),
(424, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:12:25'),
(425, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:12:56'),
(426, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:13:27'),
(427, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:13:58'),
(428, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:14:29'),
(429, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:15:00'),
(430, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:15:31'),
(431, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:16:02'),
(432, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:16:33'),
(433, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:17:04'),
(434, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:17:35'),
(435, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:18:06'),
(436, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:18:37'),
(437, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:19:08'),
(438, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:19:39'),
(439, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:20:10'),
(440, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:20:41'),
(441, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:21:12'),
(442, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:21:43'),
(443, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:22:14'),
(444, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:22:45'),
(445, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:23:16'),
(446, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:23:47'),
(447, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:24:18'),
(448, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:24:49'),
(449, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:25:20'),
(450, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:25:51'),
(451, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:26:22'),
(452, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:26:53'),
(453, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:27:24'),
(454, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:27:55'),
(455, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:28:26'),
(456, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:28:57'),
(457, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:29:28'),
(458, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:29:59'),
(459, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:30:30'),
(460, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:31:01'),
(461, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:31:32'),
(462, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:32:03'),
(463, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:32:34'),
(464, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:33:05'),
(465, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:33:36'),
(466, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:34:07'),
(467, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:34:38'),
(468, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:35:09'),
(469, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:35:40'),
(470, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:36:11'),
(471, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:36:42'),
(472, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:37:13'),
(473, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:37:44'),
(474, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:38:15'),
(475, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:38:46'),
(476, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:39:17'),
(477, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:39:48'),
(478, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:40:19'),
(479, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:40:50'),
(480, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:41:21'),
(481, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:41:52'),
(482, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:42:23'),
(483, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:42:54'),
(484, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:43:25'),
(485, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:43:56'),
(486, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:44:27'),
(487, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:44:58'),
(488, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:45:29'),
(489, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:46:00'),
(490, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:46:31'),
(491, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:47:02'),
(492, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:47:33'),
(493, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:48:04'),
(494, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:48:35'),
(495, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:49:06'),
(496, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:49:37'),
(497, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:50:08'),
(498, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:50:39'),
(499, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:51:10'),
(500, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:51:40'),
(501, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:52:11'),
(502, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:52:42'),
(503, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:53:13'),
(504, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:53:44'),
(505, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:54:15'),
(506, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:54:46'),
(507, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:55:17'),
(508, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:55:48'),
(509, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:56:19'),
(510, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:56:50'),
(511, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:57:21'),
(512, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:57:52'),
(513, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:58:23'),
(514, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:58:54'),
(515, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:59:25'),
(516, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 12:59:56'),
(517, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:00:27'),
(518, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:00:58'),
(519, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:01:29'),
(520, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:02:00'),
(521, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:02:31'),
(522, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:03:02'),
(523, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:03:33'),
(524, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:04:04'),
(525, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:04:35'),
(526, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:05:06'),
(527, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:05:37'),
(528, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:06:08'),
(529, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:06:39'),
(530, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:07:10'),
(531, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:07:41'),
(532, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:08:12'),
(533, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:08:43'),
(534, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:09:14'),
(535, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:09:45'),
(536, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:10:16'),
(537, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:10:47'),
(538, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:11:18'),
(539, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:11:49'),
(540, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:12:20'),
(541, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:12:51'),
(542, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:13:22'),
(543, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:13:53'),
(544, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:14:24'),
(545, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:14:55'),
(546, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:15:26'),
(547, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:15:57'),
(548, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:16:28'),
(549, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:16:59'),
(550, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:17:30'),
(551, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:18:01'),
(552, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:18:32'),
(553, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:19:03'),
(554, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:19:34'),
(555, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:20:05'),
(556, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:20:36'),
(557, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:21:07'),
(558, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:21:38'),
(559, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:22:09'),
(560, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:22:40'),
(561, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:23:11'),
(562, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:23:42'),
(563, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:24:13'),
(564, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:24:44'),
(565, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:25:15'),
(566, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:25:46'),
(567, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:26:17'),
(568, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:26:48'),
(569, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:27:19'),
(570, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:27:50'),
(571, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:28:21'),
(572, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:28:52'),
(573, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:29:23'),
(574, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:29:54'),
(575, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:30:25'),
(576, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:30:56'),
(577, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:31:27'),
(578, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:31:58'),
(579, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:32:29'),
(580, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:33:00'),
(581, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:33:31'),
(582, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:34:02'),
(583, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:34:33'),
(584, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:35:04'),
(585, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:35:35'),
(586, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:36:06'),
(587, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:36:37'),
(588, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:37:08'),
(589, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:37:39'),
(590, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:38:10'),
(591, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:38:41'),
(592, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:39:12'),
(593, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:39:43'),
(594, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:40:14'),
(595, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:40:45'),
(596, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:41:16'),
(597, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:41:47'),
(598, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:42:18'),
(599, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:42:49'),
(600, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:43:20'),
(601, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:43:51'),
(602, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:44:22'),
(603, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:44:53'),
(604, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:45:24'),
(605, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:45:55'),
(606, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:46:26'),
(607, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:46:57'),
(608, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:47:28'),
(609, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:47:59'),
(610, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:48:30'),
(611, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:49:01'),
(612, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:49:32'),
(613, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:50:03'),
(614, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:50:34'),
(615, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:51:05'),
(616, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:51:36'),
(617, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:52:07'),
(618, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:52:38'),
(619, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:53:09'),
(620, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:53:40'),
(621, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:54:11'),
(622, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:54:42'),
(623, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:55:13'),
(624, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:55:44'),
(625, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:56:15'),
(626, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:56:46'),
(627, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:57:17'),
(628, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:57:48'),
(629, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:58:19'),
(630, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:58:50'),
(631, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:59:21'),
(632, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 13:59:52'),
(633, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:00:23'),
(634, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:00:54'),
(635, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:01:25'),
(636, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:01:56'),
(637, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:02:27'),
(638, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:02:58'),
(639, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:03:29'),
(640, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:04:00'),
(641, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:04:31'),
(642, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:05:02'),
(643, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:05:33'),
(644, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:06:04'),
(645, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:06:35'),
(646, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:07:06'),
(647, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:07:37'),
(648, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:08:08'),
(649, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:08:39'),
(650, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:09:10'),
(651, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:09:41'),
(652, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:10:12'),
(653, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:10:43'),
(654, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:11:14'),
(655, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:11:45'),
(656, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:12:16'),
(657, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:12:47'),
(658, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:13:18'),
(659, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:13:49'),
(660, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:14:20'),
(661, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:14:51'),
(662, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:15:22'),
(663, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:15:53'),
(664, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:16:24');
INSERT INTO `problem_reports` (`report_id`, `driver_id`, `trip_id`, `problem_type`, `description`, `status`, `created_at`) VALUES
(665, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:16:55'),
(666, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:17:26'),
(667, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:17:57'),
(668, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:18:28'),
(669, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:18:59'),
(670, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:19:30'),
(671, 3, NULL, 'mechanical', 'asdfgh', 'reported', '2025-10-20 14:20:02');

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `route_id` int(11) NOT NULL,
  `departure` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `delay_time` varchar(6) NOT NULL,
  `price_per_seat` decimal(10,2) NOT NULL CHECK (`price_per_seat` > 0),
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`route_id`, `departure`, `destination`, `delay_time`, `price_per_seat`, `status`, `created_at`) VALUES
(1, 'Nyabugogo', 'Musanze', '5', 5000.00, 'active', '2025-10-14 07:34:20'),
(2, 'Nyabugogo', 'Huye', '2', 3000.00, 'active', '2025-10-14 07:34:20'),
(3, 'Nyabugogo', 'Rubavu', '2', 6000.00, 'active', '2025-10-14 07:34:20'),
(4, 'Nyabugogo', 'Gisenyi', '5', 5000.00, 'active', '2025-10-15 07:36:49'),
(5, 'Nyabugogo', 'Ruhango', '2', 2000.00, 'inactive', '2025-10-15 21:37:12'),
(6, 'Nyabugogo', 'Muhanga', '1', 1500.00, 'active', '2025-10-17 11:34:07');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `checked` enum('yes','no') DEFAULT 'no',
  `checked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`ticket_id`, `booking_id`, `checked`, `checked_at`, `created_at`) VALUES
(1, 9, '', NULL, '2025-10-20 16:51:35'),
(2, 11, 'no', '2025-10-20 16:58:25', '2025-10-20 16:58:25'),
(3, 12, 'no', NULL, '2025-10-20 17:01:41'),
(4, 13, 'no', NULL, '2025-10-21 09:50:23'),
(5, 14, 'no', NULL, '2025-10-21 09:50:48'),
(6, 15, 'no', NULL, '2025-10-21 09:53:49'),
(7, 16, 'no', NULL, '2025-10-21 10:24:06'),
(8, 17, 'no', NULL, '2025-10-21 10:35:01'),
(9, 18, 'no', NULL, '2025-10-21 10:57:07'),
(10, 19, 'no', NULL, '2025-10-22 07:44:18'),
(11, 20, 'no', NULL, '2025-10-22 07:46:28'),
(12, 21, 'no', NULL, '2025-10-22 07:58:47'),
(13, 22, 'no', NULL, '2025-10-22 09:03:29');

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `trip_id` int(11) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `departure_datetime` datetime NOT NULL,
  `estimated_arrival` datetime NOT NULL,
  `available_seats` int(11) NOT NULL,
  `status` enum('available','ontrip','arrived','maintenance') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`trip_id`, `bus_id`, `driver_id`, `route_id`, `departure_datetime`, `estimated_arrival`, `available_seats`, `status`, `created_at`) VALUES
(1, 1, 1, 1, '2025-10-22 09:39:00', '2025-10-22 14:39:00', 20, 'ontrip', '2025-10-17 09:25:24'),
(2, 3, 2, 6, '2025-10-19 09:50:00', '2025-10-19 10:50:00', 30, 'arrived', '2025-10-17 12:49:31'),
(3, 4, 3, 1, '2025-10-20 09:43:00', '2025-10-20 14:43:00', 15, 'arrived', '2025-10-17 17:50:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','passenger','driver') DEFAULT 'passenger',
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `contact`, `password`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'Elyse', 'TUYISHIMIRE', 'telyse50@gmail.com', '\'789588151', '12345678', 'passenger', 'active', '2025-10-22 09:02:57', '2025-10-14 07:35:56', '2025-10-22 09:02:57'),
(2, 'Mercie', 'NIKUZWE', 'nikuzwe@gmail.com', '\'789588177', '12345678', 'driver', 'active', NULL, '2025-10-15 09:52:20', '2025-10-15 09:52:20'),
(3, 'Jane ', 'Smith', 'janesmith@gmail.com', '\'0788654321', '1234567890', 'driver', 'active', '2025-10-20 07:24:16', '2025-10-15 19:46:40', '2025-10-20 07:24:16'),
(4, 'Fabrice', 'NSHUTI', 'nshutifabrice@gmail.com', '\'0789786765', '123456789', 'admin', 'active', '2025-10-20 07:16:42', '2025-10-15 20:14:40', '2025-10-20 07:16:42'),
(6, 'Mike ', 'Johnson', 'mikejohnson@gmail.com', '\'0788987654', '1234567890', 'driver', 'active', '2025-10-20 07:45:19', '2025-10-16 11:17:55', '2025-10-20 07:45:19'),
(7, 'John', 'Doe', 'johndoe@gmail.com', '+250788123456', '1234567890', 'driver', 'active', '2025-10-17 20:48:42', '2025-10-17 10:03:02', '2025-10-17 20:48:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `idx_bookings_customer` (`customer_id`),
  ADD KEY `idx_bookings_trip` (`trip_id`);

--
-- Indexes for table `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`bus_id`),
  ADD UNIQUE KEY `plates_number` (`plates_number`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`driver_id`),
  ADD UNIQUE KEY `license` (`license`),
  ADD UNIQUE KEY `unique_user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `idx_payments_booking` (`booking_id`);

--
-- Indexes for table `problem_reports`
--
ALTER TABLE `problem_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `trip_id` (`trip_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`route_id`),
  ADD KEY `idx_routes_departure` (`departure`),
  ADD KEY `idx_routes_destination` (`destination`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `idx_tickets_booking` (`booking_id`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`trip_id`),
  ADD UNIQUE KEY `unique_trip_schedule` (`bus_id`,`departure_datetime`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `idx_trips_departure` (`departure_datetime`),
  ADD KEY `idx_trips_route` (`route_id`),
  ADD KEY `idx_trips_bus` (`bus_id`);

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
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=676;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `buses`
--
ALTER TABLE `buses`
  MODIFY `bus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `driver_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `problem_reports`
--
ALTER TABLE `problem_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=672;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `trip_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`trip_id`);

--
-- Constraints for table `drivers`
--
ALTER TABLE `drivers`
  ADD CONSTRAINT `drivers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE;

--
-- Constraints for table `problem_reports`
--
ALTER TABLE `problem_reports`
  ADD CONSTRAINT `problem_reports_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`driver_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `problem_reports_ibfk_2` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`trip_id`) ON DELETE SET NULL;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE;

--
-- Constraints for table `trips`
--
ALTER TABLE `trips`
  ADD CONSTRAINT `trips_ibfk_1` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`bus_id`),
  ADD CONSTRAINT `trips_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`driver_id`),
  ADD CONSTRAINT `trips_ibfk_3` FOREIGN KEY (`route_id`) REFERENCES `routes` (`route_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
