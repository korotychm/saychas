
-- Generation Time: Oct 18, 2021 at 10:09 AM

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `saychas_z`
--

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


