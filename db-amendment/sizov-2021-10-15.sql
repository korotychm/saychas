-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 15, 2021 at 10:10 AM
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
  `product_id` varchar(20) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `reviews` int(11) NOT NULL DEFAULT '0',
  `statistic` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE `review` (
  `id` varchar(50) NOT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `product_id` varchar(20) NOT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `user_name` text NOT NULL,
  `seller_name` text,
  `user_message` mediumtext,
  `seller_message` mediumtext,
  `time_created` bigint(20) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `time_modified` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `review_image`
--

DROP TABLE IF EXISTS `review_image`;
CREATE TABLE `review_image` (
  `id` bigint(20) NOT NULL,
  `review_id` varchar(50) NOT NULL,
  `filename` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
