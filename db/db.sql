-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2017 at 09:22 PM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `symfony_exam_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `items` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:array)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `owner_id`, `items`) VALUES
(11, 2, 'a:1:{i:0;a:4:{s:2:\"id\";i:6;s:4:\"name\";s:10:\"Windows 10\";s:5:\"price\";d:60;s:6:\"amount\";i:2;}}'),
(12, 1, 'a:0:{}');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(5, 'Cars'),
(1, 'Hardware'),
(2, 'Software');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `dateAdded` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `author_id`, `item_id`, `content`, `dateAdded`) VALUES
(1, 2, 6, 'This is the best linux distro', '2017-05-06 18:05:30'),
(2, 2, 6, 'If I install the 32bit twice will I get 64bit?', '2017-05-06 18:08:08'),
(3, 2, 10, 'Great Deal!!! 5/5', '2017-05-06 18:15:01'),
(4, 1, 6, 'Windows 7 is better', '2017-05-08 21:08:23');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `date_added` datetime NOT NULL,
  `discounted` tinyint(1) NOT NULL,
  `discount_value` int(11) NOT NULL,
  `discountExpirationDate` datetime DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `ownerId` int(11) NOT NULL,
  `image_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `category_id`, `name`, `description`, `price`, `date_added`, `discounted`, `discount_value`, `discountExpirationDate`, `quantity`, `ownerId`, `image_name`, `updated_at`) VALUES
(6, 2, 'Windows 10', 'Windows 10 spyware edition.\r\nNow with extra telemetry.', '120.00', '2017-04-28 14:18:34', 1, 50, '2020-01-01 00:00:00', 995, 2, 'alleged-windows-10-retail-boxes-leaked-486660-3.jpg', '2017-04-28 14:18:34'),
(7, 1, 'TEST ITEM', 'none', '100.00', '2017-04-28 14:35:22', 0, 20, '2017-01-01 00:00:00', 1, 2, NULL, '2017-04-28 14:35:22'),
(9, 1, 'CPU I7-7700K', 'Kaby Lake is the codename used by Intel for a processor microarchitecture which was announced on August 30, 2016.[4] Like the preceding Skylake, Kaby Lake is produced using a 14 nanometer manufacturing process technology.[5] Breaking with Intel\'s previous \"tick-tock\" manufacturing and design model, Kaby Lake represents the optimized step of the newer \"process-architecture-optimization\" model.[6] Kaby Lake began shipping to manufacturers and OEMs in the second quarter of 2016,[7][8] and mobile chips have started shipping while Kaby Lake (desktop) chips were officially launched on January 3, 2017.\r\n\r\nSkylake was anticipated to be succeeded by the 10 nanometer Cannonlake, but it was announced on July 16, 2015, that Cannonlake has been delayed until the second half of 2017.[9][10] Kaby Lake is the first Intel platform to lack official driver support from Microsoft for versions of Windows older than Windows 10.[11]', '600.00', '2017-04-28 14:41:39', 0, 10, '2018-01-01 00:00:00', 7, 2, '218141.jpg', '2017-04-28 14:51:27'),
(10, 2, 'Mystery item', 'Buy one for the price of two and get the second one free!', '100.00', '2017-04-28 17:36:58', 1, 20, '2018-01-01 00:00:00', 10, 2, 'present.png', '2017-04-28 17:36:58'),
(12, 5, 'Golf 4 1.9 TDI 90Hp', '2 Fast 2 Furious системата за ускорение е специална разработка на БАН (патентен номер 22-7684-13), по-известна на български като Фифи. Тя служи за фокусиране на тягата на Бас тубите към пропелера.', '4000.00', '2017-05-06 18:53:50', 0, 0, '2017-01-01 00:00:00', 5, 2, 'karuca.jpg', '2017-05-06 19:06:41');

-- --------------------------------------------------------

--
-- Table structure for table `items_used`
--

CREATE TABLE `items_used` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `date_added` datetime NOT NULL,
  `ownerId` int(11) NOT NULL,
  `image_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reference_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `items_used`
--

INSERT INTO `items_used` (`id`, `name`, `description`, `price`, `date_added`, `ownerId`, `image_name`, `reference_id`) VALUES
(8, 'Windows 10', 'Windows 10 spyware edition.\r\nNow with extra telemetry.', '60.00', '2017-05-08 20:33:41', 2, 'alleged-windows-10-retail-boxes-leaked-486660-3.jpg', 6);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'ROLE_ADMIN'),
(2, 'ROLE_EDITOR'),
(3, 'ROLE_USER');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fullName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `money` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `password`, `fullName`, `money`) VALUES
(1, 'user@abv.bg', 'user', '$2y$13$df5Ry51nIA1hi.B7DI.KceKDzD4zoNGkjz8w3rcSlKIC11hKw647S', 'John Smith', '40.00'),
(2, 'admin@abv.bg', 'admin', '$2y$13$ARCYhbiGaFpiZOMYP7ZEFe.T51bWD84S9.okY4ERg0w6Bwdn12blu', 'ADMIN', '9340.00'),
(4, 'pesho@abv.bg', 'pesho', '$2y$13$ClW9R28ws3..PwOJyiTPK.JtvPZg9NkkOE9SUhkCcvyG0L39xh6vy', NULL, '1000.00');

-- --------------------------------------------------------

--
-- Table structure for table `users_roles`
--

CREATE TABLE `users_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users_roles`
--

INSERT INTO `users_roles` (`user_id`, `role_id`) VALUES
(1, 3),
(2, 1),
(4, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_4E004AAC7E3C61F9` (`owner_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_3AF346685E237E06` (`name`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5F9E962AF675F31B` (`author_id`),
  ADD KEY `IDX_5F9E962A126F525E` (`item_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_E11EE94D12469DE2` (`category_id`),
  ADD KEY `IDX_E11EE94DE05EFD25` (`ownerId`);

--
-- Indexes for table `items_used`
--
ALTER TABLE `items_used`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_7EABCD9EE05EFD25` (`ownerId`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_B63E2EC75E237E06` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_1483A5E9E7927C74` (`email`),
  ADD UNIQUE KEY `UNIQ_1483A5E9F85E0677` (`username`);

--
-- Indexes for table `users_roles`
--
ALTER TABLE `users_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `IDX_51498A8EA76ED395` (`user_id`),
  ADD KEY `IDX_51498A8ED60322AC` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `items_used`
--
ALTER TABLE `items_used`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `FK_4E004AAC7E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `FK_5F9E962A126F525E` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `FK_5F9E962AF675F31B` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `FK_E11EE94D12469DE2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `FK_E11EE94DE05EFD25` FOREIGN KEY (`ownerId`) REFERENCES `users` (`id`);

--
-- Constraints for table `items_used`
--
ALTER TABLE `items_used`
  ADD CONSTRAINT `FK_7EABCD9EE05EFD25` FOREIGN KEY (`ownerId`) REFERENCES `users` (`id`);

--
-- Constraints for table `users_roles`
--
ALTER TABLE `users_roles`
  ADD CONSTRAINT `FK_51498A8EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_51498A8ED60322AC` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
