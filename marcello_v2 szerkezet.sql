-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2025. Ápr 17. 00:24
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `marcello_v2`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `admins`
--

CREATE TABLE `admins` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `comments`
--

CREATE TABLE `comments` (
  `id_comment` int(11) NOT NULL,
  `id_event` int(11) NOT NULL,
  `comment` varchar(400) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `events`
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
  `public` tinyint(1) NOT NULL,
  `comments_enabled` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `event_comments`
--

CREATE TABLE `event_comments` (
  `id_comment` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `event_invites`
--

CREATE TABLE `event_invites` (
  `id_event_invite` int(11) NOT NULL,
  `id_event` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `selected_gift` varchar(255) DEFAULT NULL,
  `invited_by` varchar(50) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `invite_token` varchar(255) NOT NULL,
  `invite_expire` datetime NOT NULL,
  `gift_selected` tinyint(1) DEFAULT 0 COMMENT 'Jelzi, hogy történt-e már ajándékválasztás'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `event_signups`
--

CREATE TABLE `event_signups` (
  `signup_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `signup_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `event_wishlists`
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
-- Tábla szerkezet ehhez a táblához `gift_wishlists`
--

CREATE TABLE `gift_wishlists` (
  `id_wishlist` int(11) NOT NULL,
  `id_event` int(11) NOT NULL,
  `wishes` text NOT NULL,
  `url_friendly_wishes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`url_friendly_wishes`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `registration_tokens`
--

CREATE TABLE `registration_tokens` (
  `id_token` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `expiry_date` datetime NOT NULL,
  `reg_token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `session_tokens`
--

CREATE TABLE `session_tokens` (
  `id_token` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiry_date` datetime NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
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
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- A tábla indexei `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id_comment`),
  ADD KEY `fk_comments_events` (`id_event`);

--
-- A tábla indexei `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id_event`),
  ADD KEY `fk_events_users` (`owner`);

--
-- A tábla indexei `event_comments`
--
ALTER TABLE `event_comments`
  ADD PRIMARY KEY (`id_comment`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `user_id` (`user_id`);

--
-- A tábla indexei `event_invites`
--
ALTER TABLE `event_invites`
  ADD PRIMARY KEY (`id_event_invite`),
  ADD UNIQUE KEY `invite_token` (`invite_token`),
  ADD KEY `fk_event_invites_users` (`id_user`),
  ADD KEY `fk_event_invites_events` (`id_event`),
  ADD KEY `fk_invited_by_user` (`invited_by`);

--
-- A tábla indexei `event_signups`
--
ALTER TABLE `event_signups`
  ADD PRIMARY KEY (`signup_id`);

--
-- A tábla indexei `event_wishlists`
--
ALTER TABLE `event_wishlists`
  ADD PRIMARY KEY (`id_event_wishlist`),
  ADD KEY `fk_event_wishlists_users` (`id_user`),
  ADD KEY `fk_event_wishlists_events` (`id_event`);

--
-- A tábla indexei `gift_wishlists`
--
ALTER TABLE `gift_wishlists`
  ADD PRIMARY KEY (`id_wishlist`),
  ADD UNIQUE KEY `id_event` (`id_event`),
  ADD KEY `fk_gift_wishlists_events` (`id_event`);

--
-- A tábla indexei `registration_tokens`
--
ALTER TABLE `registration_tokens`
  ADD PRIMARY KEY (`id_token`),
  ADD KEY `registration_tokens_ibfk_1` (`username`);

--
-- A tábla indexei `session_tokens`
--
ALTER TABLE `session_tokens`
  ADD PRIMARY KEY (`id_token`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `fk_session_tokens_users` (`id_user`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`username`),
  ADD UNIQUE KEY `registration_token` (`registration_token`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `admins`
--
ALTER TABLE `admins`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `comments`
--
ALTER TABLE `comments`
  MODIFY `id_comment` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `events`
--
ALTER TABLE `events`
  MODIFY `id_event` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `event_comments`
--
ALTER TABLE `event_comments`
  MODIFY `id_comment` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `event_invites`
--
ALTER TABLE `event_invites`
  MODIFY `id_event_invite` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `event_signups`
--
ALTER TABLE `event_signups`
  MODIFY `signup_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `event_wishlists`
--
ALTER TABLE `event_wishlists`
  MODIFY `id_event_wishlist` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `gift_wishlists`
--
ALTER TABLE `gift_wishlists`
  MODIFY `id_wishlist` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `registration_tokens`
--
ALTER TABLE `registration_tokens`
  MODIFY `id_token` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `session_tokens`
--
ALTER TABLE `session_tokens`
  MODIFY `id_token` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_events` FOREIGN KEY (`id_event`) REFERENCES `events` (`id_event`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_events_users` FOREIGN KEY (`owner`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `event_comments`
--
ALTER TABLE `event_comments`
  ADD CONSTRAINT `event_comments_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id_event`),
  ADD CONSTRAINT `event_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`);

--
-- Megkötések a táblához `event_invites`
--
ALTER TABLE `event_invites`
  ADD CONSTRAINT `fk_event_invites_events` FOREIGN KEY (`id_event`) REFERENCES `events` (`id_event`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_event_invites_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_invited_by_user` FOREIGN KEY (`invited_by`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `event_wishlists`
--
ALTER TABLE `event_wishlists`
  ADD CONSTRAINT `fk_event_wishlists_events` FOREIGN KEY (`id_event`) REFERENCES `events` (`id_event`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_event_wishlists_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `gift_wishlists`
--
ALTER TABLE `gift_wishlists`
  ADD CONSTRAINT `fk_gift_wishlists_events` FOREIGN KEY (`id_event`) REFERENCES `events` (`id_event`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `registration_tokens`
--
ALTER TABLE `registration_tokens`
  ADD CONSTRAINT `registration_tokens_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `session_tokens`
--
ALTER TABLE `session_tokens`
  ADD CONSTRAINT `fk_session_tokens_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
