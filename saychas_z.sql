-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 13, 2021 at 08:21 AM
-- Server version: 8.0.23
-- PHP Version: 7.4.15

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
-- Table structure for table `brand`
--

DROP TABLE IF EXISTS `brand`;
CREATE TABLE `brand` (
  `id` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `logo` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`id`, `title`, `description`, `logo`) VALUES
('000001', 'BOSH', '', ''),
('000003', 'HUAWEI', '', ''),
('000002', 'SONY', '', ''),
('000004', 'Простоквашино', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` varchar(9) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` varchar(9) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `title` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sort_order` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Категории товаров';

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `parent_id`, `title`, `description`, `icon`, `sort_order`) VALUES
('000000012', '000000003', 'DECT', '', '', 0),
('000000008', '000000002', 'Аксесуары', '', '', 0),
('000000010', '000000001', 'Бытовая техника', '', '', 0),
('000000016', '000000014', 'Кефир', '', '', 0),
('000000007', '000000004', 'Кнопочные', '', '', 0),
('000000017', '000000014', 'Молоко', '', '', 0),
('000000014', '000000013', 'Молочная продукция', '', '', 0),
('000000015', '000000013', 'Мясные продукты', '', '', 0),
('000000009', '000000008', 'Наушники', '', '', 0),
('000000013', '0', 'Продукты питания', '', '', 0),
('000000018', '000000014', 'Ряженка', '', '', 0),
('000000006', '000000004', 'Смартфоны', '', '', 0),
('000000004', '000000003', 'Сотовые телефоны', '', '', 0),
('000000005', '000000003', 'Стационарные телефоны', '', '', 0),
('000000003', '000000002', 'Телефоны', '', '', 0),
('000000001', '0', 'Техника', '', '', 0),
('000000011', '000000010', 'Холодильники', '', '', 0),
('000000002', '000000001', 'Электроника', '', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `characteristic`
--

DROP TABLE IF EXISTS `characteristic`;
CREATE TABLE `characteristic` (
  `id` varchar(9) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `category_id` varchar(9) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `filter` tinyint(1) NOT NULL DEFAULT '0',
  `group` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '1',
  `unit` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `characteristic_value`
--

DROP TABLE IF EXISTS `characteristic_value`;
CREATE TABLE `characteristic_value` (
  `id` varchar(9) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `characteristic_id` varchar(9) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `characteristic_value`
--

INSERT INTO `characteristic_value` (`id`, `title`, `characteristic_id`) VALUES
('000000002', 'Беспроводной', '000000001'),
('000000001', 'Проводной', '000000001'),
('000000004', '16 ГБ', '000000007'),
('000000005', '32 ГБ', '000000007'),
('000000003', '8 ГБ', '000000007'),
('000000007', '128 ГБ', '000000008'),
('000000008', '256 ГБ', '000000008'),
('000000009', '512 ГБ', '000000008'),
('000000006', '64 ГБ', '000000008');

-- --------------------------------------------------------

--
-- Table structure for table `characteristic_value2`
--

DROP TABLE IF EXISTS `characteristic_value2`;
CREATE TABLE `characteristic_value2` (
  `id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `characteristic_id` varchar(9) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
CREATE TABLE `country` (
  `id` int NOT NULL,
  `title` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `code` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
CREATE TABLE `customer` (
  `id` int NOT NULL,
  `name` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `phone` int NOT NULL,
  `email` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `filtered_product`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `filtered_product`;
CREATE TABLE `filtered_product` (
`id` varchar(9)
,`param_value_list` text
,`param_variable_list` text
,`price` int
,`product_id` varchar(12)
,`product_title` text
,`provider_id` varchar(6)
,`rest` int
,`title` text
);

-- --------------------------------------------------------

--
-- Table structure for table `price`
--

DROP TABLE IF EXISTS `price`;
CREATE TABLE `price` (
  `product_id` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `reserve` int NOT NULL,
  `store_id` varchar(9) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `unit` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `price` int NOT NULL,
  `provider_id` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `price`
--

INSERT INTO `price` (`product_id`, `reserve`, `store_id`, `unit`, `price`, `provider_id`) VALUES
('000000000001', 0, '', '', 580000, '00003'),
('000000000002', 0, '', '', 470100, '00003'),
('000000000003', 0, '', '', 5699000, '00004'),
('000000000004', 0, '', '', 6500, '00003'),
('000000000006', 0, '', '', 8300, '00003'),
('000000000005', 0, '', '', 7800, '00003');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `provider_id` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `category_id` varchar(9) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `vendor_code` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `param_value_list` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `param_variable_list` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `brand_id` varchar(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `provider_id`, `category_id`, `title`, `description`, `vendor_code`, `param_value_list`, `param_variable_list`, `brand_id`) VALUES
('000000000001', '00003', '000000006', 'Смартфон vivo Y31, голубой океан', '', 'PL_08/17', '', '[{\"id\":\"000000006\",\"value\":6.76},{\"id\":\"000000007\",\"value\":\"000000003\"},{\"id\":\"000000008\",\"value\":\"\"},{\"id\":\"000000009\",\"value\":\"50/20/12/TOF\"},{\"id\":\"000000010\",\"value\":\"13/TOF\"}]', '000002'),
('000000000002', '00003', '000000009', 'Наушники True Wireless Huawei Freebuds Pro угольный черный', '', '50141256', '', '[{\"id\":\"000000003\",\"value\":\"29 u041eu043c\"},{\"id\":\"000000002\",\"value\":\"20 u0413u0446 - 20 u043au0413u0446\"},{\"id\":\"000000001\",\"value\":\"000000002\"},{\"id\":\"000000004\",\"value\":true},{\"id\":\"000000005\",\"value\":true}]', '000003'),
('000000000004', '00003', '000000017', 'Молоко', '', 'М-0011', '', '', '000004'),
('000000000005', '00003', '000000016', 'Кефир', '', 'К-0012', '', '', '000004'),
('000000000006', '00003', '000000018', 'Ряженка', '', 'Р-00123', '', '', '000004'),
('000000000003', '00004', '000000011', 'Холодильник Bosch Serie | 4 VitaFresh KGN39XW27R', '', '20068499', '', '[{\"id\":\"000000012\",\"value\":\"\"}]', '000001');

-- --------------------------------------------------------

--
-- Table structure for table `product_image`
--

DROP TABLE IF EXISTS `product_image`;
CREATE TABLE `product_image` (
  `product_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ftp_url` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `http_url` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `provider`
--

DROP TABLE IF EXISTS `provider`;
CREATE TABLE `provider` (
  `id` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `icon` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `provider`
--

INSERT INTO `provider` (`id`, `title`, `description`, `icon`) VALUES
('00002', 'Apple', '', ''),
('00003', 'Барамба', '', ''),
('00004', 'М-Видео', '', ''),
('00001', 'Ситилинк', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `shop`
--

DROP TABLE IF EXISTS `shop`;
CREATE TABLE `shop` (
  `id` int NOT NULL,
  `provider_id` int NOT NULL,
  `title` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `geox` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `geoy` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `icon` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_balance`
--

DROP TABLE IF EXISTS `stock_balance`;
CREATE TABLE `stock_balance` (
  `product_id` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `rest` int NOT NULL DEFAULT '0',
  `store_id` varchar(9) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `stock_balance`
--

INSERT INTO `stock_balance` (`product_id`, `rest`, `store_id`) VALUES
('000000000002', 1, '000000004'),
('000000000002', 10, '000000002'),
('000000000002', 3, '000000003'),
('000000000003', 8, '000000005'),
('000000000004', 6, '000000002'),
('000000000004', 4, '000000003'),
('000000000004', 2, '000000004'),
('000000000005', 15, '000000002'),
('000000000005', 38, '000000003'),
('000000000005', 42, '000000004'),
('000000000006', 30, '000000002'),
('000000000006', 14, '000000003'),
('000000000006', 69, '000000004');

-- --------------------------------------------------------

--
-- Table structure for table `store`
--

DROP TABLE IF EXISTS `store`;
CREATE TABLE `store` (
  `id` varchar(9) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `provider_id` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `geox` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `geoy` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `icon` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `store`
--

INSERT INTO `store` (`id`, `provider_id`, `title`, `description`, `address`, `geox`, `geoy`, `icon`) VALUES
('000000002', '00003', 'Магазин 1 (Барамба)', '', '', '55.657123', '37.739375', ''),
('000000003', '00003', 'Магазин 2 (Барамба)', '', '', '55.895247', '37.57195', ''),
('000000004', '00003', 'Магазин 3 (Барамба)', '', '', '55.72993', '37.496337', ''),
('000000005', '00004', 'На Волгоградке (м-видео)', '', '', '55.721462', '37.697468', ''),
('000000001', '00001', 'Ситилинк(ул. 5-я кожуховская)', '', '', '55.704362', '37.683324', ''),
('000000006', '00001', 'ТестАдреса', '', '', '55.676907', '37.576968', '');

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

DROP TABLE IF EXISTS `test`;
CREATE TABLE `test` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure for view `filtered_product`
--
DROP TABLE IF EXISTS `filtered_product`;

DROP VIEW IF EXISTS `filtered_product`;
CREATE ALGORITHM=UNDEFINED DEFINER=`saychas_z`@`localhost` SQL SECURITY DEFINER VIEW `filtered_product`  AS  select distinct `s`.`id` AS `id`,`s`.`provider_id` AS `provider_id`,`s`.`title` AS `title`,`pr`.`id` AS `product_id`,`pr`.`title` AS `product_title`,`sb`.`rest` AS `rest`,`pri`.`price` AS `price`,`pr`.`param_value_list` AS `param_value_list`,`pr`.`param_variable_list` AS `param_variable_list` from ((((`store` `s` join `provider` `p` on((`p`.`id` = `s`.`provider_id`))) join `product` `pr` on((`pr`.`provider_id` = `s`.`provider_id`))) left join `stock_balance` `sb` on(((`sb`.`product_id` = `pr`.`id`) and (`sb`.`store_id` = `s`.`id`)))) left join `price` `pri` on(((`pri`.`product_id` = `pr`.`id`) and (`pri`.`provider_id` = `p`.`id`)))) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `characteristic`
--
ALTER TABLE `characteristic`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `characteristic_value`
--
ALTER TABLE `characteristic_value`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `characteristic_value2`
--
ALTER TABLE `characteristic_value2`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `price`
--
ALTER TABLE `price`
  ADD PRIMARY KEY (`product_id`,`provider_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_image`
--
ALTER TABLE `product_image`
  ADD PRIMARY KEY (`product_id`,`ftp_url`);

--
-- Indexes for table `provider`
--
ALTER TABLE `provider`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shop`
--
ALTER TABLE `shop`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_balance`
--
ALTER TABLE `stock_balance`
  ADD PRIMARY KEY (`product_id`,`store_id`);

--
-- Indexes for table `store`
--
ALTER TABLE `store`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
