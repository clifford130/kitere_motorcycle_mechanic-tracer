-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 26, 2025 at 05:34 PM
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
-- Database: `kitere_mechanic_db`
--
CREATE DATABASE IF NOT EXISTS `kitere_mechanic_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `kitere_mechanic_db`;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `password`, `role`) VALUES
(1, 'sean@gmail.com', '$2y$10$8Zm4ZmBk4YUWldbrGvFZ3u.k/zkXAInW/N/QlSeCbkgOdv1zXovO2', 'super_admin');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `mechanic_email` varchar(50) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `description` varchar(677) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_email`, `mechanic_email`, `booking_date`, `booking_time`, `description`, `status`, `created_at`) VALUES
(29, 'cliffordisaboke1@gmail.com', 'cliffordonchomba483@gmail.com', '2026-02-28', '10:51:00', 'brake alignment', 'completed', '0000-00-00 00:00:00'),
(30, 'cliffordisaboke1@gmail.com', 'jay1@gmail.com', '2025-02-25', '04:18:00', 'oil change', 'pending', '0000-00-00 00:00:00'),
(31, 'cliffordisaboke1@gmail.com', 'jay@gmail.com', '2025-02-25', '18:10:00', 'oil change', 'completed', '0000-00-00 00:00:00'),
(32, 'cliffordisaboke1@gmail.com', 'jay@gmail.com', '2025-02-25', '22:01:00', 'wheel alignment', 'completed', '2025-02-25 19:01:23'),
(33, 'cliffordisaboke1@gmail.com', 'jay1@gmail.com', '2025-02-14', '10:10:00', 'wheel alignment', 'pending', '2025-02-25 19:10:41'),
(34, 'clifford1@gmail.com', 'cliffordonchomba483@gmail.com', '2025-02-13', '22:19:00', 'wheel alignment', 'pending', '2025-02-25 19:19:40'),
(35, 'sean11@gmail.com', 'cliffordonchomba483@gmail.com', '2025-02-13', '19:52:00', 'wheel alignment', 'pending', '2025-02-26 13:52:56'),
(36, 'cliffordisaboke1@gmail.com', 'rebecca1@gmail.com', '2025-04-12', '21:20:00', 'oil cahnge and engine  clean', 'completed', '2025-02-26 16:16:35');

-- --------------------------------------------------------

--
-- Table structure for table `mechanics`
--

CREATE TABLE `mechanics` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `garage_name` varchar(255) NOT NULL,
  `experience` int(11) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `services_offered` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mechanics`
--

INSERT INTO `mechanics` (`id`, `email`, `full_name`, `phone_number`, `garage_name`, `experience`, `latitude`, `longitude`, `services_offered`, `password`, `created_at`) VALUES
(2, 'cliffordonchomba483@gmail.com', 'Clifford', '0710698450', 'auto mechanic', 4, -0.82552500, 34.60957600, 'Engine Repair', '$2y$10$yJdxsEubiVMaIzCIuE616e2nUPXAALjznxx/nLirmVespeBmH42Mi', '2025-02-21 11:38:53'),
(4, 'jay1@gmail.com', 'sean', '+254710698450', 'kitere', 2, -0.82561597, 34.60953505, 'Engine Repair', '$2y$10$urQe4sfbCmzAVwbtGSb6ueznpvaYWeKBWrQ5lc2WO3rAdS8ZUnojy', '2025-02-25 13:17:52'),
(3, 'jay@gmail.com', 'sean', '+254710698450', 'kitere', 2, -0.82561633, 34.60963971, 'Oil Change, Tire Replacement, Chain Lubrication', '$2y$10$tUZyMEkW9XjGQSseaXL80eenAnW83BA7KO10aE.xmfx5ZvDHIRuwO', '2025-02-25 13:14:41'),
(5, 'rebecca1@gmail.com', 'rebeccah', '0719009099', 'motorcycle clini', 3, -0.82561935, 34.60956453, 'Oil Change, Engine Repair', '$2y$10$b7gUk/yA/JvJ0xWPSj3yLuxE/PfOY3DlojoiZEo7eMonm.FvCA9p2', '2025-02-26 15:24:53');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('cliffordisaboke1@gmail.com', '1ec9a40c99440709f9172fd75c90450a60ee486d361b898b34166adb19bb24fd451ddad74c778fd93b247d6834634e84e487', '2025-02-21 12:00:31'),
('cliffordonchomba483@gmail.com', '768aebc3d9390211438fe0fff3f1e5c92cf35e58c7409cec7a4799c39274ceb4cbefa2f77f93d9a8afbe99147873482c6741', '2025-02-21 11:55:59');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `mechanic_email` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `review_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `mechanic_email`, `user_email`, `rating`, `review_message`, `created_at`) VALUES
(6, 'cliffordonchomba483@gmail.com', 'cliffordisaboke1@gmail.com', 4, 'wrrrr', '2025-02-25 12:24:08'),
(9, 'rebecca1@gmail.com', 'cliffordisaboke1@gmail.com', 4, 'best mechanic ever', '2025-02-26 16:17:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('motorcyclist','admin') NOT NULL DEFAULT 'motorcyclist',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `full_name`, `phone_number`, `password`, `role`, `created_at`) VALUES
(3, 'cliffordisaboke1@gmail.com', 'clifford', '0710698450', '$2y$10$fuHZAfmMnwyiksiSy3IJ0uZEp9VrCeFPj.WF6jfZYHipCtUbV1xJy', 'motorcyclist', '2025-02-21 11:25:14'),
(8, 'sean11@gmail.com', 'sean Kingstone', '+254710698450', '$2y$10$Hio1Rq9qU/ypcBH5pV2p9eS0/Q9z9CHThH2uiV3A5DEElog2XlVHe', 'motorcyclist', '2025-02-26 13:46:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mechanics`
--
ALTER TABLE `mechanics`
  ADD PRIMARY KEY (`email`),
  ADD UNIQUE KEY `unique` (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `mechanic_email` (`mechanic_email`),
  ADD KEY `user_email` (`user_email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`email`),
  ADD UNIQUE KEY `unique` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `mechanics`
--
ALTER TABLE `mechanics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`mechanic_email`) REFERENCES `mechanics` (`email`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_email`) REFERENCES `users` (`email`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
