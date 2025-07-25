-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 25, 2025 at 08:36 AM
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
-- Database: `new_obeo`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `children`
--

CREATE TABLE `children` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `age` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `children`
--

INSERT INTO `children` (`id`, `age`, `created_at`, `updated_at`) VALUES
(157, 15, '2025-07-25 00:33:29', '2025-07-25 00:33:29');

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `currency` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `currency`, `created_at`, `updated_at`) VALUES
(2, 'USD', '2025-05-13 03:28:36', '2025-05-13 03:28:36'),
(3, 'BDT', '2025-05-13 03:28:43', '2025-05-13 03:28:59');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hotelName` varchar(100) NOT NULL,
  `hotelAddress` varchar(300) NOT NULL,
  `commissionType` varchar(50) NOT NULL,
  `expediaCollectsCommission` varchar(3) DEFAULT NULL,
  `hotelCollectsCommission` varchar(3) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `hotelName`, `hotelAddress`, `commissionType`, `expediaCollectsCommission`, `hotelCollectsCommission`, `created_at`, `updated_at`) VALUES
(6, 'Royal Raj Hotel', 'Rajshahi', 'zero', NULL, NULL, '2025-05-13 01:08:02', '2025-05-13 03:29:34'),
(7, 'Hotel Studio 23', 'Dhaka', 'zero', NULL, NULL, '2025-06-01 11:52:35', '2025-06-01 11:52:35'),
(8, 'Wood Burn Hotel', 'Bogura', 'zero', NULL, NULL, '2025-07-25 00:03:35', '2025-07-25 00:03:35');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '2025_04_23_043955_create_users', 1),
(3, '2025_04_23_052543_create_sessions_table', 2),
(4, '2025_05_12_090742_create_hotels', 3),
(5, '2025_05_13_041921_create_hotels', 4),
(6, '2025_05_13_090842_create_currencies', 5),
(7, '2025_05_13_093841_create_rates', 6),
(8, '2025_05_13_100224_create_sources', 7),
(9, '2025_05_14_050156_create_payment_methods', 8),
(10, '2025_05_14_052309_create_reservation_statuses', 9),
(11, '2025_05_18_062805_create_childrens', 10),
(12, '2025_05_18_092937_create_rooms', 10),
(13, '2025_05_18_093604_create_reservations', 10),
(14, '2025_05_18_095713_create_reservation_rooms', 10),
(15, '2025_05_18_100228_create_reservation_childrens', 10),
(16, '2025_05_18_095712_create_reservations', 11),
(17, '2025_05_25_051501_create_reservations', 12),
(18, '2025_05_18_095711_create_reservations', 13),
(19, '2025_05_18_095710_create_reservations', 14),
(20, '2025_05_18_062806_create_reservation_children_table', 15),
(21, '2025_05_18_062806_create_children', 16),
(22, '2025_05_27_055557_create_reservation_children_table', 16),
(23, '2025_05_27_060511_create_reservation_children', 17),
(24, '2025_05_18_095710_create_reservations1', 18),
(25, '2025_05_18_095712_create_reservations1', 19);

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `payment`, `created_at`, `updated_at`) VALUES
(6, 'Hotel Collects', '2025-05-13 23:53:23', '2025-05-13 23:53:23'),
(7, 'Expedia collect', '2025-05-26 04:23:40', '2025-05-26 04:23:40'),
(8, 'Obeo  Bank collect', '2025-05-26 04:24:05', '2025-05-26 04:25:01'),
(9, 'Obeo bKash collect', '2025-05-26 04:24:34', '2025-05-26 04:24:34');

-- --------------------------------------------------------

--
-- Table structure for table `rates`
--

CREATE TABLE `rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rate` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rates`
--

INSERT INTO `rates` (`id`, `rate`, `created_at`, `updated_at`) VALUES
(3, '170', '2025-05-13 03:52:23', '2025-05-13 03:52:53'),
(4, '200', '2025-05-13 03:52:32', '2025-05-13 03:52:59'),
(5, '199', '2025-05-13 03:52:36', '2025-05-13 03:53:09'),
(8, '122', '2025-07-25 00:04:13', '2025-07-25 00:04:13');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `obeo_sl` bigint(20) NOT NULL,
  `status_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reservation_no` varchar(255) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `reservation_date` date NOT NULL,
  `hotel_id` bigint(20) UNSIGNED NOT NULL,
  `guest_name` varchar(150) NOT NULL,
  `rate_id` bigint(20) UNSIGNED NOT NULL,
  `total_advance` decimal(10,2) DEFAULT NULL,
  `currency_id` bigint(20) UNSIGNED DEFAULT NULL,
  `source_id` bigint(20) UNSIGNED NOT NULL,
  `payment_method_id` bigint(20) UNSIGNED NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `total_adult` int(11) NOT NULL,
  `request` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `obeo_sl`, `status_id`, `reservation_no`, `check_in`, `check_out`, `reservation_date`, `hotel_id`, `guest_name`, `rate_id`, `total_advance`, `currency_id`, `source_id`, `payment_method_id`, `phone`, `email`, `total_adult`, `request`, `comment`, `created_at`, `updated_at`) VALUES
(6, 1, 202507255137, 8, '5709409212', '2025-07-25', '2025-07-26', '2025-07-25', 8, 'dawei li', 8, NULL, NULL, 8, 6, '+86 138 1699 3314', 'dli.970633@guest.booking.com', 2, NULL, 'Address: yangqu road 1388-15-101 shanghai shanghai', '2025-07-25 00:06:29', '2025-07-25 00:29:13'),
(7, 1, 202507259270, NULL, '15655', '2025-07-24', '2025-07-26', '2025-07-25', 6, 'asdcvasdfcs', 3, NULL, NULL, 8, 6, '01876987622', NULL, 1, NULL, NULL, '2025-07-25 00:32:17', '2025-07-25 00:32:17'),
(8, 1, 202507251433, NULL, '321321321', '2025-07-30', '2025-07-31', '2025-07-26', 6, 'asfsafd', 3, 20.00, 2, 8, 6, '01735238409', NULL, 4, 'asfdasdfsd', 'asfdsdfsd', '2025-07-25 00:33:29', '2025-07-25 00:33:29');

-- --------------------------------------------------------

--
-- Table structure for table `reservation_children`
--

CREATE TABLE `reservation_children` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `child_id` bigint(20) UNSIGNED NOT NULL,
  `reservation_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservation_children`
--

INSERT INTO `reservation_children` (`id`, `child_id`, `reservation_id`, `created_at`, `updated_at`) VALUES
(156, 157, 8, '2025-07-25 00:33:29', '2025-07-25 00:33:29');

-- --------------------------------------------------------

--
-- Table structure for table `reservation_rooms`
--

CREATE TABLE `reservation_rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `reservation_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservation_rooms`
--

INSERT INTO `reservation_rooms` (`id`, `room_id`, `reservation_id`, `created_at`, `updated_at`) VALUES
(55, 58, 6, '2025-07-25 00:06:29', '2025-07-25 00:06:29'),
(56, 59, 7, '2025-07-25 00:32:17', '2025-07-25 00:32:17'),
(57, 60, 8, '2025-07-25 00:33:29', '2025-07-25 00:33:29'),
(58, 61, 8, '2025-07-25 00:33:29', '2025-07-25 00:33:29');

-- --------------------------------------------------------

--
-- Table structure for table `reservation_statuses`
--

CREATE TABLE `reservation_statuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservation_statuses`
--

INSERT INTO `reservation_statuses` (`id`, `status`, `created_at`, `updated_at`) VALUES
(8, 'Checked In', '2025-07-02 04:17:30', '2025-07-02 04:17:30'),
(9, 'No Show', '2025-07-02 04:17:46', '2025-07-02 04:17:46');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_room` int(11) NOT NULL,
  `total_night` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `currency_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `total_room`, `total_night`, `total_price`, `currency_id`, `created_at`, `updated_at`) VALUES
(58, 'Single Room with Private Bathroom', 2, 1, 41.94, 2, '2025-07-25 00:06:29', '2025-07-25 00:06:29'),
(59, 'sdafasdfsdaf', 1, 1, 120.00, 2, '2025-07-25 00:32:17', '2025-07-25 00:32:17'),
(60, 'asddfsdffds', 1, 1, 120.00, 2, '2025-07-25 00:33:29', '2025-07-25 00:33:29'),
(61, 'asdfasdfsdaf', 1, 1, 120.00, 3, '2025-07-25 00:33:29', '2025-07-25 00:33:29');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('ePcpUBKwRrhwjwAQH3ql2d44vNx1YfrS74nUM6HO', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVzFlR2tNbU8xaXE2Wml4Q3hKd29UYzFFWmdjUWdSVVUyR3NuTDY2NiI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1753424871),
('PAKQU87GI8RC4xMHXyaj0KkAEFysF81x9J3XrwYJ', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicmQyYVdSV1lTcHJObVRpOWhHWmI5eUJKdERxV2pJTjRMbVdDTUloQSI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo2OToiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZC9yZXNlcnZhdGlvbnMvdG9kYXktYWRkZWQtcmVzZXJ2YXRpb25zIjt9fQ==', 1753425272),
('PMSHMttvAfStE4PVBx7mEOMSmstOVgsnYF6VL8VC', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibktDQlZyOEx1VkxLbEdzSTZzVXJ0UmtYNm5Wemk2SzZhWFhaY0NuYSI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo2OToiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZC9yZXNlcnZhdGlvbnMvdG9kYXktYWRkZWQtcmVzZXJ2YXRpb25zIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1753424856),
('zrpkCCj3Dgbzh4XWx7KZ8qsUPOriWq2uTdIfR6Xj', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSkhzbW5JSXZjbU4xWXRXRXQycWFXOUJMa3hkeGhxN3EyRmZIdWp6aiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Njk6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQvcmVzZXJ2YXRpb25zL3RvZGF5LWFkZGVkLXJlc2VydmF0aW9ucyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1753424274);

-- --------------------------------------------------------

--
-- Table structure for table `sources`
--

CREATE TABLE `sources` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `source` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sources`
--

INSERT INTO `sources` (`id`, `source`, `created_at`, `updated_at`) VALUES
(8, 'Booking.com', '2025-05-13 22:46:59', '2025-05-13 23:52:25'),
(9, 'Expedia', '2025-05-26 04:21:47', '2025-05-26 04:21:47'),
(10, 'Airbnb', '2025-05-26 04:22:00', '2025-05-26 04:22:00'),
(11, 'Ctrip', '2025-05-26 04:22:16', '2025-05-26 04:22:16'),
(12, 'Makemytrip', '2025-05-26 04:22:34', '2025-05-26 04:22:34'),
(13, 'Direct', '2025-05-26 04:22:45', '2025-05-26 04:22:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fullName` varchar(40) NOT NULL,
  `userName` varchar(20) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(40) NOT NULL,
  `role` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL,
  `otp` varchar(10) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullName`, `userName`, `phone`, `email`, `role`, `password`, `otp`, `created_at`, `updated_at`) VALUES
(1, 'Rakib Gazi', 'gusy', '+1 (512) 391-5527', 'admin@gmail.com', 'Role', 'Pass@12345', '0', '2025-04-22 23:51:04', '2025-05-10 09:39:07'),
(2, 'Hayfa Smith', 'fyworo', '+1 (945) 538-1092', 'pijy@mailinator.com', 'Moderator', 'Pa$$w0rd!', '0', '2025-04-23 00:17:44', '2025-04-23 00:17:44'),
(3, 'Alice Harris', 'fazimowire', '+1 (633) 986-3195', 'zobumiduxe@mailinator.com', 'Admin', 'Pa$$w0rd!', '0', '2025-04-23 00:29:19', '2025-04-23 00:29:19'),
(4, 'Perry Callahan', 'laqiryc', '+1 (271) 165-4072', 'mosafe@mailinator.com', 'Role', 'Pa$$w0rd!', '0', '2025-04-23 00:41:39', '2025-04-23 00:41:39'),
(9, 'Jermaine Perkins', 'hedazujel', '+1 (741) 899-8873', 'neful@mailinator.com', 'Role', 'Pa$$w0rd!', '0', '2025-04-23 03:26:56', '2025-04-23 03:26:56'),
(10, 'Jerome Leblanc', 'naqepunug', '+1 (438) 763-1027', 'sycytozesa@mailinator.com', 'Role', 'Pa$$w0rd!', '0', '2025-04-23 03:45:22', '2025-04-23 03:45:22'),
(11, 'Adria Carpenter', 'xyloji', '+1 (986) 356-3022', 'qutypu@mailinator.com', 'Role', 'Pa$$w0rd!', '0', '2025-04-23 03:47:11', '2025-04-23 03:47:11'),
(12, 'Pamela Ware', 'zysilu', '+1 (596) 585-4855', 'dupyg@mailinator.com', 'Moderator', 'Pa$$w0rd!', '0', '2025-04-23 03:53:12', '2025-04-23 03:53:12'),
(13, 'Melanie Sparks', 'dahaqobe', '+1 (131) 134-3181', 'mapi@mailinator.com', 'Role', 'Pa$$w0rd!', '0', '2025-04-23 04:04:23', '2025-04-23 04:04:23'),
(14, 'Odysseus Curry', 'lytamir', '+1 (168) 458-7474', 'fekovokyv@mailinator.com', 'Role', 'Pa$$w0rd!', '0', '2025-04-23 04:04:59', '2025-04-23 04:04:59'),
(15, 'Gareth Conner', 'fudurytyl', '+1 (343) 663-6794', 'pahek@mailinator.com', 'Role', 'Pa$$w0rd!', '0', '2025-04-23 04:05:06', '2025-04-23 04:05:06'),
(16, 'Kylee York', 'xojywonodo', '+1 (562) 498-1415', 'jynevizyny@mailinator.com', 'Admin', 'Pa$$w0rd!', '0', '2025-04-29 03:22:50', '2025-04-29 03:22:50'),
(17, 'Yuri Howard', 'mexegyf', '+1 (157) 954-6888', 'pohif@mailinator.com', 'Admin', 'Pa$$w0rd!', '0', '2025-04-29 04:15:25', '2025-04-29 04:15:25'),
(18, 'Edan Conley', 'pumojar', '+1 (514) 973-6982', 'xyfi@mailinator.com', 'Moderator', 'Pa$$w0rd!', '0', '2025-04-29 04:18:17', '2025-04-29 04:18:17'),
(19, 'Allistair Goff', 'mikesirev', '+1 (119) 763-4166', 'wyjo@mailinator.com', 'Admin', 'Pa$$w0rd!', '0', '2025-04-29 04:22:24', '2025-04-29 04:22:24'),
(20, 'Kyla Rollins', 'bypobel', '+1 (828) 903-7743', 'rowici@mailinator.com', 'Admin', 'Pa$$w0rd!', '0', '2025-04-29 04:24:16', '2025-04-29 04:24:16'),
(21, 'Dante Valenzuela', 'baqujalono', '+1 (961) 459-9522', 'nyva@mailinator.com', 'Admin', 'Pa$$w0rd!', '0', '2025-04-29 04:25:31', '2025-04-29 04:25:31'),
(22, 'Hadley Roth', 'rofatobyba', '+1 (282) 481-7696', 'pagakol@mailinator.com', 'Moderator', 'Pa$$w0rd!', '0', '2025-04-29 04:26:43', '2025-04-29 04:26:43'),
(23, 'Leonard Knowles', 'rujamava', '+1 (129) 822-1043', 'puqumi@mailinator.com', 'Admin', 'Pa$$w0rd!', '0', '2025-04-29 23:22:36', '2025-04-29 23:22:36'),
(34, 'Molestiae esse occae', 'Qudfg', '00000000000', 'rycykysik@mailinator.com', 'Moderator', 'Pa$$w0rd!', '0', '2025-05-10 00:23:52', '2025-05-10 00:23:52'),
(35, 'Unde velit laborum e', 'Even', '00000000001', 'jajupehoqu@mailinator.com', 'Admin', 'Pa$$w0rd!', '0', '2025-05-10 00:28:29', '2025-05-10 00:28:29'),
(36, 'Dolor veniam aspern', 'Officia', '00000000002', 'dowilo@mailinator.com', 'Moderator', 'Pa$$w0rd!', '0', '2025-05-10 00:33:27', '2025-05-10 00:33:27'),
(37, 'Esse soluta fugiat', 'Dese', '00000000056', 'newur@mailinator.com', 'Moderator', 'Pa$$w0rd!', '0', '2025-05-10 00:35:11', '2025-05-10 00:35:11'),
(38, 'At voluptate possimu', 'Dolor', '01735238404', 'vaxeme@mailinator.com', 'Moderator', 'Pa$$w0rd!', '0', '2025-05-13 01:07:34', '2025-05-13 01:07:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `children`
--
ALTER TABLE `children`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `currencies_currency_unique` (`currency`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hotels_hotelname_unique` (`hotelName`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_methods_payment_unique` (`payment`);

--
-- Indexes for table `rates`
--
ALTER TABLE `rates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rates_rate_unique` (`rate`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reservations_obeo_sl_unique` (`obeo_sl`),
  ADD UNIQUE KEY `reservations_reservation_no_unique` (`reservation_no`),
  ADD KEY `reservations_user_id_foreign` (`user_id`),
  ADD KEY `reservations_status_id_foreign` (`status_id`),
  ADD KEY `reservations_hotel_id_foreign` (`hotel_id`),
  ADD KEY `reservations_rate_id_foreign` (`rate_id`),
  ADD KEY `reservations_currency_id_foreign` (`currency_id`),
  ADD KEY `reservations_source_id_foreign` (`source_id`),
  ADD KEY `reservations_payment_method_id_foreign` (`payment_method_id`);

--
-- Indexes for table `reservation_children`
--
ALTER TABLE `reservation_children`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_children_child_id_foreign` (`child_id`),
  ADD KEY `reservation_children_reservation_id_foreign` (`reservation_id`);

--
-- Indexes for table `reservation_rooms`
--
ALTER TABLE `reservation_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_rooms_room_id_foreign` (`room_id`),
  ADD KEY `reservation_rooms_reservation_id_foreign` (`reservation_id`);

--
-- Indexes for table `reservation_statuses`
--
ALTER TABLE `reservation_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reservation_statuses_status_unique` (`status`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rooms_currency_id_foreign` (`currency_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `sources`
--
ALTER TABLE `sources`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sources_source_unique` (`source`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`userName`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `children`
--
ALTER TABLE `children`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `rates`
--
ALTER TABLE `rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reservation_children`
--
ALTER TABLE `reservation_children`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT for table `reservation_rooms`
--
ALTER TABLE `reservation_rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `reservation_statuses`
--
ALTER TABLE `reservation_statuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `sources`
--
ALTER TABLE `sources`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_rate_id_foreign` FOREIGN KEY (`rate_id`) REFERENCES `rates` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_source_id_foreign` FOREIGN KEY (`source_id`) REFERENCES `sources` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `reservation_statuses` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `reservation_children`
--
ALTER TABLE `reservation_children`
  ADD CONSTRAINT `reservation_children_child_id_foreign` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_children_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `reservation_rooms`
--
ALTER TABLE `reservation_rooms`
  ADD CONSTRAINT `reservation_rooms_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_rooms_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
