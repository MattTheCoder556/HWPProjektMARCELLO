-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 17, 2024 at 08:55 AM
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
  `guest_list` text NOT NULL,
  `public` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id_event`, `event_pic`, `event_name`, `owner`, `start_date`, `end_date`, `attendees`, `is_banned`, `event_type`, `description`, `place`, `guest_list`, `public`) VALUES
(12, 'uploads/6744893d7699a-logoZentaBGRM.png', 'Qwee', 6, '2024-11-26 00:00:00', '2024-11-30 00:00:00', 36, 0, 'wedding', 'qqewqew', 'Senta, Ferenca Sepa, 5', '', 0),
(13, 'uploads/674489942428c-cityLogo.png', 'City', 6, '2024-11-26 04:31:00', '2024-11-27 21:32:00', 600, 0, 'Test', '', 'Senta, Hotel , 9', '', 0),
(14, 'uploads/67448ac453907-NKA_logó-removebg-preview.png', 'Tessting', 6, '2024-11-27 12:09:00', '2024-11-30 08:33:00', 330, 0, 'concert', '', 'Senta, Senta, 3', '', 0),
(22, 'uploads/674878a35269c-jakob-dalbjorn-cuKJre3nyYc-unsplash.jpg', 'Testing', 6, '2024-11-29 15:04:00', '2024-11-30 15:05:00', 98, 0, 'Concert', 'Testintesting31245456+', 'Test, TestS, 1', '', 1),
(23, 'uploads/6748791aef616-Múzsák logó.jpeg', 'Testsetsets', 6, '2024-12-05 15:07:00', '2024-12-06 15:07:00', 23, 0, 'Wedding', '3r', 'wwer, wer, 3', '', 1),
(24, 'uploads/67487c2832038-Silentio Transeo JAVITVA5.jpg', 'adsf', 6, '2024-12-06 15:20:00', '2024-12-07 15:20:00', 234, 0, 'Wedding', '', 'qr4, q243, 3', '', 1),
(25, 'uploads/67610a30959d8-att.QhblHCMzksB9B3ShOGbLvIyr3lYB3ylUZidz4xuJM6g.jpg', 'Teszt esemény', 6, '2024-12-20 07:00:00', '2024-12-22 22:00:00', 95, 0, 'Concert', 'Ez egy teszt esemény hogy megmutassuk hogyan tud működni az esemény foglalás.', 'Subotica, Utca Név, 35', '', 1),
(26, 'uploads/676124193228e-top-view-tasty-salad-with-vegetables.jpg', 'Kaja', 6, '2024-12-17 08:10:00', '2024-12-17 10:10:00', 3, 0, 'Ebed', 'Kajaa', 'Subotica, Utca, 5', '', 1),
(27, 'uploads/676129704cc98-my_warner_bros__wallpaper__16x9__by_ptbf2002_dhtpti2-fullview.jpg', 'Teszt Esem;ny 3', 6, '2024-12-18 09:30:00', '2024-12-20 13:00:00', 56, 0, 'Bowling', 'Ez egy teszt event ahol bowlingozunk', 'Subotica, Utca, 98', '', 1);

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
-- Table structure for table `event_signups`
--

CREATE TABLE `event_signups` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `signup_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_signups`
--

INSERT INTO `event_signups` (`id`, `event_id`, `user_id`, `signup_date`) VALUES
(4, 23, 6, '2024-12-16 11:37:44'),
(5, 22, 6, '2024-12-17 06:12:59'),
(11, 26, 6, '2024-12-17 08:16:41');

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
-- Table structure for table `registration_tokens`
--

CREATE TABLE `registration_tokens` (
  `id_token` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `expiry_date` datetime NOT NULL,
  `reg_token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registration_tokens`
--

INSERT INTO `registration_tokens` (`id_token`, `username`, `expiry_date`, `reg_token`) VALUES
(1, 'mucsimate07@gmail.com', '2024-11-15 10:34:09', '3fc2c7776f9024956b1e42187278e356'),
(2, 'mucsimate07@yahoo.com', '2024-11-20 01:16:46', '69bf3cc9c4e4e1a4aa6346b26a76c3a1'),
(3, 'abrakadabra@yamail.com', '2024-11-20 01:23:55', '83a321c70c49399390718343efd60060'),
(4, 'testing@testing.com', '2024-11-25 16:45:49', '69dc4159985e051cb0bdea48fdbec0e2');

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

--
-- Dumping data for table `session_tokens`
--

INSERT INTO `session_tokens` (`id_token`, `token`, `expiry_date`, `id_user`) VALUES
(1, 'c118a432c6aaf84ef336289bd20777b41e57f343b2dc3673656104eae9d792d8', '2024-11-20 01:48:00', 5),
(2, '02ee9c62cf614ee34254302cfb3d5742250511656b6344245f0fefcb0ad4bdcf', '2024-11-25 16:46:49', 6),
(3, 'fdbea987f4b044fe2d52c34d62ff524e3c0b46fd5b3ee08a52fcf6513739fb57', '2024-11-25 19:01:50', 6),
(4, '3eb11b18105fd170d81420d8a987e8b9710d8e73f3969268e89cf0bbf4728d4a', '2024-11-28 15:27:37', 6),
(5, '92d06f134e75490b988e4ab9ed42d7333c3375b12ac25b68754ce5e764a22e26', '2024-11-28 15:39:43', 6),
(6, '23db414c1bea55cfb3c541d59e76b25676402b0fd911d134139d418f210ddb9e', '2024-12-08 15:10:57', 6),
(7, 'd12e7607e8e0b07926c4ab41e29e7e82485a3087af21e2f86bcb2b0a022fab28', '2024-12-08 22:58:30', 6),
(8, '384514fb48924cfe1f2206612c055ff250ea61b8b43d83306275ccd064b37dd9', '2024-12-10 10:03:09', 6),
(9, '8cd50d669e79e533595f4ba3f45c185b6e85491f56ec3aee48900c751b2b043a', '2024-12-16 13:37:33', 6),
(10, '66ee9b9b89d8524623a66ee2e363de07725d898a60ee78c986170c365a1e51ce', '2024-12-16 14:09:08', 6),
(11, '0822df49d7789c363def56f23ecfcaefaada7cdb88c972f2b0da60ab631db1a9', '2024-12-16 14:33:07', 6),
(12, '9b19ccad8aa87aaa0c5b036d5630341ebaaa32fde47270afcce2205717b00c85', '2024-12-17 01:42:33', 6),
(13, 'b703ca6c85e46c9479d8cc7cc2826cebcb1e2768a9dd00c8cccff2769459327c', '2024-12-17 08:05:33', 6),
(14, '0e1473464c4e0186cca7b7755ad7acfee4a79e7c5226901761274915b5db3c01', '2024-12-17 09:55:02', 6),
(15, '7f460ccf86bcb5993e8fecb71f9f28ada266a3cfd9bb2a83954735f35bf40dbe', '2024-12-17 10:27:18', 6),
(16, '0a18a6918869b8bc393cd7959e189092d1eb9167cf7a465db307cf37fcb9881f', '2024-12-17 10:32:09', 6);

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
  `new_password_token_expiry` datetime DEFAULT NULL,
  `registration_token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `firstname`, `lastname`, `username`, `phone`, `password`, `is_verified`, `is_banned`, `new_password_token`, `new_password_token_expiry`, `registration_token`) VALUES
(3, 'Mate', 'Mucsi', 'mucsimate07@gmail.com', '123 1234567890', '$2y$10$oXrGAFs5zGyMjh3uppCKpO/yqTGnwc3uZXaaBlBLtrVJlvTy1B8uC', 1, 0, '4ddf4ff39e4535f80ea6df366f914406', '2024-11-25 15:19:25', '3fc2c7776f9024956b1e42187278e356'),
(4, 'Matt', 'M', 'mucsimate07@yahoo.com', '065 2099422', '$2y$10$i5vPmdB8IwAiw0GA8h7UT.GMQN5jO2gO8MNVOOrBD1y59ompyee0W', 0, 0, NULL, NULL, '69bf3cc9c4e4e1a4aa6346b26a76c3a1'),
(5, 'abraka', 'dabra', 'abrakadabra@yamail.com', '123 61524859', '$2y$10$ECcXHPmV4Eydqf8KsJlM/.zU2SCkebf3rm9PKjZk3Cog81iUib706', 1, 0, NULL, NULL, '83a321c70c49399390718343efd60060'),
(6, 'Test', 'Ing', 'testing@testing.com', '195 162458790', '$2y$10$4IlUHTP8Yca7DA.hIiQGXejoWNzNM8ySmKMCn7pE7Q9QiHGUw2hla', 1, 0, NULL, NULL, '69dc4159985e051cb0bdea48fdbec0e2');

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
-- Indexes for table `event_signups`
--
ALTER TABLE `event_signups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `event_id` (`event_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

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
-- Indexes for table `registration_tokens`
--
ALTER TABLE `registration_tokens`
  ADD PRIMARY KEY (`id_token`),
  ADD KEY `username` (`username`);

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
  MODIFY `id_event` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `event_invites`
--
ALTER TABLE `event_invites`
  MODIFY `id_event_invite` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_signups`
--
ALTER TABLE `event_signups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
-- AUTO_INCREMENT for table `registration_tokens`
--
ALTER TABLE `registration_tokens`
  MODIFY `id_token` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `session_tokens`
--
ALTER TABLE `session_tokens`
  MODIFY `id_token` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- Constraints for table `event_signups`
--
ALTER TABLE `event_signups`
  ADD CONSTRAINT `event_signups_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id_event`),
  ADD CONSTRAINT `event_signups_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`);

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
-- Constraints for table `registration_tokens`
--
ALTER TABLE `registration_tokens`
  ADD CONSTRAINT `registration_tokens_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`);

--
-- Constraints for table `session_tokens`
--
ALTER TABLE `session_tokens`
  ADD CONSTRAINT `fk_session_tokens_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
