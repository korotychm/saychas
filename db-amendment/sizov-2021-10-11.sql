-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 11, 2021 at 04:23 PM
-- Server version: 5.7.33-0ubuntu0.18.04.1
-- PHP Version: 7.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `saychas_z`
--

-- --------------------------------------------------------

--
-- Table structure for table `average_price`
--

DROP TABLE IF EXISTS `average_price`;
CREATE TABLE `average_price` (
  `category_id` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `average_price`
--

INSERT INTO `average_price` (`category_id`, `price`) VALUES
('000000006', 3362191),
('000000011', 2999334),
('000000020', 4932500);

-- --------------------------------------------------------

--
-- Table structure for table `product_rating`
--

DROP TABLE IF EXISTS `product_rating`;
CREATE TABLE `product_rating` (
  `product_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `reviews` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `product_rating`
--

INSERT INTO `product_rating` (`product_id`, `rating`, `reviews`) VALUES
('000000000001', 44, 0),
('000000000002', 45, 0),
('000000000036', 46, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_user_rating`
--

DROP TABLE IF EXISTS `product_user_rating`;
CREATE TABLE `product_user_rating` (
  `product_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `rating` tinyint(4) NOT NULL DEFAULT '47'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `product_user_rating`
--

INSERT INTO `product_user_rating` (`product_id`, `user_id`, `rating`) VALUES
('000000000036', 50, 50),
('000000000036', 3066, 50),
('000000000036', 3070, 30),
('000000000036', 3071, 50),
('000000000036', 3072, 50),
('000000000036', 100, 50),
('000000000036', 30, 40),
('000000000036', 3073, 50),
('000000000036', 3074, 50),
('000000000001', 3074, 30),
('000000000001', 3077, 40),
('000000000001', 3078, 50),
('000000000002', 3078, 40),
('000000000001', 3079, 40);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE `review` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `user_name` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `seller_name` tinytext COLLATE utf8_unicode_ci,
  `user_message` text COLLATE utf8_unicode_ci,
  `seller_message` text COLLATE utf8_unicode_ci,
  `time_created` bigint(20) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`id`, `user_id`, `product_id`, `rating`, `user_name`, `seller_name`, `user_message`, `seller_message`, `time_created`, `timestamp`) VALUES
(1, 50, '000000000001', 50, 'Дмитрий Длиннофамильный', 'DNS', 'Телефончик - огонь!', 'Спасибо за отзыв', 1633520430, '2021-10-06 11:40:41'),
(4, 50, '000000000001', 50, 'Дмитрий Длиннофамильный-Северный', NULL, 'Отличный товарчик!', NULL, 1633953796, '2021-10-11 12:03:16'),
(5, 50, '000000000001', 50, 'Дмитрий Длиннофамильный-Северный', NULL, 'Отличный товарчик!', NULL, 1633955579, '2021-10-11 12:32:59'),
(6, 50, '000000000001', 50, 'Дмитрий Длиннофамильный-Северный', NULL, 'Отличный товарчик!', NULL, 1633955711, '2021-10-11 12:35:11'),
(7, 50, '000000000001', 50, 'Дмитрий Длиннофамильный-Северный', NULL, 'Отличный товарчик!', NULL, 1633955844, '2021-10-11 12:37:24'),
(8, 50, '000000000001', 50, 'Дмитрий Длиннофамильный-Северный', NULL, 'Отличный товарчик!', NULL, 1633955876, '2021-10-11 12:37:56'),
(9, 50, '000000000001', 50, 'Дмитрий Длиннофамильный-Северный', NULL, 'Отличный товарчик!', NULL, 1633956225, '2021-10-11 12:43:45'),
(10, 50, '000000000001', 50, 'Дмитрий Длиннофамильный-Северный', NULL, 'Отличный товарчик!', NULL, 1633956236, '2021-10-11 12:43:56'),
(11, 50, '000000000001', 50, 'Дмитрий Длиннофамильный-Северный', NULL, 'Отличный товарчик!', NULL, 1633956277, '2021-10-11 12:44:37'),
(12, 50, '000000000001', 50, 'Дмитрий Длиннофамильный-Северный', NULL, 'Отличный товарчик!', NULL, 1633956335, '2021-10-11 12:45:35'),
(13, 50, '000000000001', 50, 'Дмитрий Длиннофамильный-Северный', NULL, 'WWWWW WWWWW WWWWW', NULL, 1633956651, '2021-10-11 12:50:51'),
(14, 50, '000000000001', 50, 'Дмитрий Длиннофамильный-Северный', NULL, 'WWWWW WWWWW WWWWW', NULL, 1633957243, '2021-10-11 13:00:43'),
(15, 50, '000000000001', 50, 'Дмитрий Длиннофамильный-Северный', NULL, 'WWWWW WWWWW WWWWW', NULL, 1633957282, '2021-10-11 13:01:22'),
(16, 50, '000000000001', 50, 'Дмитрий Длиннофамильный-Северный', NULL, 'WWWWW WWWWW WWWWW', NULL, 1633957314, '2021-10-11 13:01:54'),
(17, 50, '000000000001', 50, 'Дмитрий Длиннофамильный-Северный', NULL, 'ssssss', NULL, 1633957342, '2021-10-11 13:02:22'),
(18, 50, '000000000001', 50, 'Дмитрий Длиннофамильный-Северный', NULL, 'ssssss', NULL, 1633957369, '2021-10-11 13:02:49'),
(19, 50, '000000000001', 50, 'Дмитрий Длиннофамильный-Северный', NULL, 'ssssss', NULL, 1633957374, '2021-10-11 13:02:54'),
(20, 50, '000000000001', 50, 'Дмитрий Длиннофамильный-Северный', NULL, 'ssssss', NULL, 1633957394, '2021-10-11 13:03:14'),
(21, 50, '000000000001', 50, 'Дмитрий Длиннофамильный-Северный', NULL, 'ssssss', NULL, 1633957395, '2021-10-11 13:03:15'),
(22, 50, '000000000001', 50, 'Дмитрий Длиннофамильный-Северный', '', 'ssssss', '', 1633957549, '2021-10-11 13:05:49');

-- --------------------------------------------------------

--
-- Table structure for table `review_image`
--

DROP TABLE IF EXISTS `review_image`;
CREATE TABLE `review_image` (
  `id` bigint(20) NOT NULL,
  `review_id` int(11) NOT NULL,
  `filename` tinytext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `review_image`
--

INSERT INTO `review_image` (`id`, `review_id`, `filename`) VALUES
(7, 4, '50_16339537966164280405ed4.png'),
(8, 5, '50_163395557961642efb76ab8.png'),
(9, 6, '50_163395571161642f7f9392b.png'),
(10, 7, '50_16339558446164300407195.png'),
(11, 8, '50_16339558766164302452419.png'),
(12, 9, '50_16339562256164318165608.png'),
(13, 10, '50_16339562366164318c8b29b.png'),
(14, 11, '50_1633956277616431b54f97f.png'),
(15, 12, '50_1633956335616431efcc511.png'),
(16, 13, '50_16339566516164332be8090.png'),
(17, 13, '50_16339566516164332be9a45.png'),
(18, 13, '50_16339566516164332bea79c.png'),
(19, 14, '50_16339572436164357b28fef.png'),
(20, 14, '50_16339572436164357b2ac55.png'),
(21, 14, '50_16339572436164357b2bbd0.png'),
(22, 15, '50_1633957282616435a22d128.png'),
(23, 15, '50_1633957282616435a22de99.png'),
(24, 15, '50_1633957282616435a22edf7.png'),
(25, 16, '50_1633957314616435c286599.png'),
(26, 16, '50_1633957314616435c287ada.png'),
(27, 16, '50_1633957314616435c288ac0.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `average_price`
--
ALTER TABLE `average_price`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `product_rating`
--
ALTER TABLE `product_rating`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_user_rating`
--
ALTER TABLE `product_user_rating`
  ADD PRIMARY KEY (`product_id`,`user_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `review_image`
--
ALTER TABLE `review_image`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `review_image`
--
ALTER TABLE `review_image`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
