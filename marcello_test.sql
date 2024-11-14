-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2024 at 04:59 PM
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
-- Database: `marcello_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id_comment` int(11) NOT NULL,
  `id_event` int(11) NOT NULL,
  `comment` varchar(400) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id_event` int(11) NOT NULL,
  `event_pic` varchar(255) NOT NULL,
  `event_name` varchar(150) NOT NULL,
  `owner` int(11) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `attendees` int(255) NOT NULL,
  `is_banned` tinyint(1) NOT NULL,
  `event_type` varchar(255) NOT NULL,
  `description` varchar(500) NOT NULL,
  `place` varchar(100) NOT NULL,
  `guest_list` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id_event`, `event_pic`, `event_name`, `owner`, `start_date`, `end_date`, `attendees`, `is_banned`, `event_type`, `description`, `place`, `guest_list`) VALUES
(4, 'uploads/6736173a72720-jakob-dalbjorn-cuKJre3nyYc-unsplash.jpg', 'Magic', 2, '2024-11-15 00:00:00', '2024-11-16 00:00:00', 36, 0, 'concert', 'blabla', 'Subotica, Ferenca Sepa, 5', ''),
(5, 'uploads/67361a84df072-att.QhblHCMzksB9B3ShOGbLvIyr3lYB3ylUZidz4xuJM6g.jpg', 'Testing logo', 2, '2024-11-15 00:00:00', '2024-11-23 00:00:00', 165, 0, 'wedding', 'asdadasdasdasdasdasdada', 'Senta, JNA, 5', '');

-- --------------------------------------------------------

--
-- Table structure for table `event_invites`
--

CREATE TABLE `event_invites` (
  `id_event_invite` int(11) NOT NULL,
  `id_event` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `invite_token` varchar(255) NOT NULL,
  `invite_expire` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_wishlists`
--

CREATE TABLE `event_wishlists` (
  `id_event_wishlist` int(11) NOT NULL,
  `id_event` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `events` text NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gift_wishlists`
--

CREATE TABLE `gift_wishlists` (
  `id_wishlist` int(11) NOT NULL,
  `id_event` int(11) NOT NULL,
  `wishes` text NOT NULL,
  `chosen_gifts` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `session_tokens`
--

CREATE TABLE `session_tokens` (
  `id_token` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiry_date` datetime NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `firstname` varchar(40) NOT NULL,
  `lastname` varchar(40) NOT NULL,
  `username` varchar(50) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) NOT NULL,
  `is_banned` tinyint(1) NOT NULL,
  `new_password_token` varchar(255) DEFAULT NULL,
  `registration_token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `firstname`, `lastname`, `username`, `phone`, `password`, `is_verified`, `is_banned`, `new_password_token`, `registration_token`) VALUES
(2, 'Mate', 'Mucsi', 'mucsimate07@gmail.com', '123 1234567890', '$2y$10$fWNZ1jykOjpoWOxAhKUbHuiFIt.G/mt8fs68RxS4iJzn0NiHKbkla', 0, 0, NULL, 'd808143621dacdc2566752c57e23bacb');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id_comment`),
  ADD KEY `fk_comments_events` (`id_event`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id_event`),
  ADD KEY `fk_events_users` (`owner`);

--
-- Indexes for table `event_invites`
--
ALTER TABLE `event_invites`
  ADD PRIMARY KEY (`id_event_invite`),
  ADD UNIQUE KEY `invite_token` (`invite_token`),
  ADD KEY `fk_event_invites_users` (`id_user`),
  ADD KEY `fk_event_invites_events` (`id_event`);

--
-- Indexes for table `event_wishlists`
--
ALTER TABLE `event_wishlists`
  ADD PRIMARY KEY (`id_event_wishlist`),
  ADD KEY `fk_event_wishlists_users` (`id_user`),
  ADD KEY `fk_event_wishlists_events` (`id_event`);

--
-- Indexes for table `gift_wishlists`
--
ALTER TABLE `gift_wishlists`
  ADD PRIMARY KEY (`id_wishlist`),
  ADD KEY `fk_gift_wishlists_events` (`id_event`);

--
-- Indexes for table `session_tokens`
--
ALTER TABLE `session_tokens`
  ADD PRIMARY KEY (`id_token`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `fk_session_tokens_users` (`id_user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`username`),
  ADD UNIQUE KEY `registration_token` (`registration_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id_comment` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id_event` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `event_invites`
--
ALTER TABLE `event_invites`
  MODIFY `id_event_invite` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_wishlists`
--
ALTER TABLE `event_wishlists`
  MODIFY `id_event_wishlist` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gift_wishlists`
--
ALTER TABLE `gift_wishlists`
  MODIFY `id_wishlist` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `session_tokens`
--
ALTER TABLE `session_tokens`
  MODIFY `id_token` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_events` FOREIGN KEY (`id_event`) REFERENCES `events` (`id_event`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_events_users` FOREIGN KEY (`owner`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event_invites`
--
ALTER TABLE `event_invites`
  ADD CONSTRAINT `fk_event_invites_events` FOREIGN KEY (`id_event`) REFERENCES `events` (`id_event`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_event_invites_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event_wishlists`
--
ALTER TABLE `event_wishlists`
  ADD CONSTRAINT `fk_event_wishlists_events` FOREIGN KEY (`id_event`) REFERENCES `events` (`id_event`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_event_wishlists_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `gift_wishlists`
--
ALTER TABLE `gift_wishlists`
  ADD CONSTRAINT `fk_gift_wishlists_events` FOREIGN KEY (`id_event`) REFERENCES `events` (`id_event`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `session_tokens`
--
ALTER TABLE `session_tokens`
  ADD CONSTRAINT `fk_session_tokens_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
