-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 14, 2021 at 02:25 PM
-- Server version: 8.0.26
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
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
-- Table structure for table `product_rating`
--

DROP TABLE IF EXISTS `product_rating`;
CREATE TABLE `product_rating` (
  `product_id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `rating` tinyint NOT NULL,
  `reviews` int NOT NULL DEFAULT '0',
  `statistic` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE `review` (
  `id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `product_id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `rating` tinyint DEFAULT NULL,
  `user_name` text COLLATE utf8mb4_general_ci NOT NULL,
  `seller_name` text COLLATE utf8mb4_general_ci,
  `user_message` mediumtext COLLATE utf8mb4_general_ci,
  `seller_message` mediumtext COLLATE utf8mb4_general_ci,
  `time_created` bigint NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `time_modified` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review_image`
--

DROP TABLE IF EXISTS `review_image`;
CREATE TABLE `review_image` (
  `id` bigint NOT NULL,
  `review_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `filename` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `product_rating`
--
ALTER TABLE `product_rating`
  ADD PRIMARY KEY (`product_id`);

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
-- AUTO_INCREMENT for table `review_image`
--
ALTER TABLE `review_image`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
