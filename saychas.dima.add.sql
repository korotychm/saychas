-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 07, 2021 at 01:07 PM
-- Server version: 5.7.34-0ubuntu0.18.04.1
-- PHP Version: 7.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `saychas_z`
--

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `average_category_price`$$
CREATE DEFINER=`saychas_z`@`localhost` PROCEDURE `average_category_price` (IN `category` VARCHAR(10), OUT `a_price` INT)  BEGIN

select avg(`price`) into a_price FROM `price` WHERE `product_id` in (select `id` from `product` where `category_id`= category );
   IF a_price > 0 THEN
REPLACE INTO `average_price`(`category_id`, `price`) VALUES (category,a_price);
END IF;
SELECT a_price;
END$$

DROP PROCEDURE IF EXISTS `set_product_rating`$$
CREATE DEFINER=`saychas_z`@`localhost` PROCEDURE `set_product_rating` (IN `productid` VARCHAR(20), IN `userid` BIGINT(20), IN `ratingvalue` TINYINT)  BEGIN
    DECLARE average_rating INT;
    DECLARE review_count INT;
    REPLACE INTO `product_user_rating` (`product_id`, `user_id`, `rating`) VALUES (productid, userid, ratingvalue) ;
    SELECT AVG(`rating`) INTO average_rating FROM `product_user_rating` where `product_id` = productid;
SELECT COUNT(`id`) INTO review_count FROM `review` where `product_id` = productid;
    IF average_rating > 0 THEN
            REPLACE INTO `product_rating`(`product_id`, `rating`, `reviews`) VALUES (productid,average_rating,review_count);
    END IF;
    SELECT average_rating, review_count;
END$$

DELIMITER ;

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
  `reviews` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `product_rating`
--

INSERT INTO `product_rating` (`product_id`, `rating`, `reviews`) VALUES
('000000000001', 43, 1),
('000000000002', 45, 0),
('000000000036', 47, 0);

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
('000000000001', 3079, 50),
('000000000002', 3079, 50),
('000000000036', 3079, 50);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE `review` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `user_name` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `seller_name` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `user_message` text COLLATE utf8_unicode_ci NOT NULL,
  `seller_message` text COLLATE utf8_unicode_ci NOT NULL,
  `time_created` bigint(20) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`id`, `user_id`, `product_id`, `rating`, `user_name`, `seller_name`, `user_message`, `seller_message`, `time_created`, `timestamp`) VALUES
(1, 50, '000000000001', 50, 'Дмитрий Длиннофамильный', 'DNS', 'Телефончик - огонь!', 'Спасибо за отзыв', 1633520430, '2021-10-06 11:40:41');

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
(1, 1, 'test1.jpg'),
(2, 1, 'test2.jpg');

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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `review_image`
--
ALTER TABLE `review_image`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
