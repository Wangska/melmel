-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 30, 2026 at 07:40 PM
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
-- Database: `hiking_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `hike_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(120) NOT NULL,
  `customer_email` varchar(160) NOT NULL,
  `date` date NOT NULL,
  `guests` int(11) NOT NULL,
  `total_price` int(11) NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hikes`
--

CREATE TABLE `hikes` (
  `id` int(11) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `name` varchar(120) NOT NULL,
  `location` varchar(120) NOT NULL,
  `difficulty` varchar(64) NOT NULL,
  `duration_hours_min` int(11) NOT NULL,
  `duration_hours_max` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `description` text NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hikes`
--

INSERT INTO `hikes` (`id`, `slug`, `name`, `location`, `difficulty`, `duration_hours_min`, `duration_hours_max`, `price`, `description`, `image_url`) VALUES
(1, 'op', 'Osmeña Peak', 'Dalaguete', 'Easy to Moderate', 1, 3, 450, 'The highest point in Cebu, offering a stunning 360-degree view of jagged hills and the coastline. Great for sunrise hikes.', 'images/osmena_peak.jpg'),
(2, 'sp', 'Sirao Peak (Mt. Kan-Irag)', 'Busay, Cebu City', 'Moderate', 3, 5, 550, 'A famous beginner trail known for panoramic views of Metro Cebu and the nearby Sirao Flower Garden.', 'images/sirao_peak.jpg'),
(3, 'mm', 'Mt. Mago', 'Carmen/Danao', 'Easy', 2, 4, 600, 'A tri-boundary mountain known for its gentle rolling hills and frequent sea of clouds. A perfect, relaxing hike.', 'images/mt_mago.jpg'),
(4, 'cp', 'Casino Peak', 'Dalaguete', 'Easy', 1, 1, 400, 'A short, steep scramble offering views of the unique \"Chocolate Hills\"-like formations in Southern Cebu.', 'images/casino_peak.jpg'),
(5, 'mn', 'Mt. Naupa', 'City of Naga', 'Easy', 1, 2, 350, 'The highest peak in Naga City, popular for easy day-hikes, camping, and excellent sunset watching.', 'images/mt_naupa.jpg'),
(6, 'mnl', 'Mt. Manunggal', 'Balamban', 'Challenging', 5, 7, 750, 'Historically significant as the crash site of President Ramon Magsaysay\'s plane. Features rugged, steep terrain.', 'images/mt_manunggal.jpg'),
(7, 'my', 'Mt. Babag/RCPI Towers', 'Cebu City', 'Moderate', 3, 5, 500, 'A classic Cebu City training climb with consistently steep ascents to the ridge where the towers stand.', 'images/mt_babag.jpg'),
(8, 'kp', 'Kandungaw Peak', 'Dalaguete/Badian', 'Moderate', 2, 3, 500, 'An epic ridge hike featuring multiple thrilling viewpoints and breathtaking vistas.', 'images/kandungaw_peak.jpg'),
(9, 'ml', 'Mt. Lanaya', 'Alegria', 'Challenging', 4, 6, 650, 'A coastal peak known for its challenging, rocky ascent and spectacular views of the Tañon Strait.', 'images/mt_lanaya.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `password_history`
--

CREATE TABLE `password_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_history`
--

INSERT INTO `password_history` (`id`, `user_id`, `password_hash`, `created_at`) VALUES
(1, 1, '$2y$10$LPOgfKxTivdgh7vvm5UafuLp3XbvNPyu.cEbSCAHwduhliI9AZ7xW', '2026-01-30 18:38:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`, `is_active`) VALUES
(1, 'admin', 'admin@hikebook.com', '$2y$10$LPOgfKxTivdgh7vvm5UafuLp3XbvNPyu.cEbSCAHwduhliI9AZ7xW', 'admin', '2026-01-30 18:38:38', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_hike_id` (`hike_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_customer_email` (`customer_email`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `hikes`
--
ALTER TABLE `hikes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_location` (`location`),
  ADD KEY `idx_difficulty` (`difficulty`);

--
-- Indexes for table `password_history`
--
ALTER TABLE `password_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hikes`
--
ALTER TABLE `hikes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `password_history`
--
ALTER TABLE `password_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`hike_id`) REFERENCES `hikes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `password_history`
--
ALTER TABLE `password_history`
  ADD CONSTRAINT `password_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
