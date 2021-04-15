-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 15, 2021 at 07:59 AM
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
('000005', 'ACER', '', ''),
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
('000000020', '000000019', 'Игровые ноутбуки', '', '', 0),
('000000016', '000000014', 'Кефир', '', '', 0),
('000000007', '000000004', 'Кнопочные', '', '', 0),
('000000017', '000000014', 'Молоко', '', '', 0),
('000000014', '000000013', 'Молочная продукция', '', '', 0),
('000000015', '000000013', 'Мясные продукты', '', '', 0),
('000000009', '000000008', 'Наушники', '', '', 0),
('000000019', '000000002', 'Ноутбуки', '', '', 0),
('000000022', '000000019', 'Ноутбуки', '', '', 0),
('000000013', '0', 'Продукты питания', '', '', 0),
('000000018', '000000014', 'Ряженка', '', '', 0),
('000000006', '000000004', 'Смартфоны', '', '', 0),
('000000004', '000000003', 'Сотовые телефоны', '', '', 0),
('000000005', '000000003', 'Стационарные телефоны', '', '', 0),
('000000003', '000000002', 'Телефоны', '', '', 0),
('000000001', '0', 'Техника', '', '', 0),
('000000021', '000000019', 'Ультрабуки', '', '', 0),
('000000011', '000000010', 'Холодильники', '', '', 0),
('000000002', '000000001', 'Электроника', '', '', 0),
('000000023', '0', 'ФЭШН ТОВАРЫ', '', '', 0),
('000000024', '000000023', 'Обувь', '', '', 0),
('000000025', '000000023', 'Верхная одежда', '', '', 0),
('000000026', '000000025', 'Пальто', '', '', 0);

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
  `unit` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `description` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `characteristic`
--

INSERT INTO `characteristic` (`id`, `category_id`, `title`, `type`, `filter`, `group`, `sort_order`, `unit`, `description`) VALUES
('000000001', '000000009', 'Тип подключения', 4, 1, 0, 3, '', ''),
('000000002', '000000009', 'Частотный диапазон', 1, 0, 0, 2, '', ''),
('000000003', '000000009', 'Сопротивление', 1, 0, 0, 1, '', ''),
('000000004', '000000009', 'Система активного подавле', 3, 0, 0, 4, '', ''),
('000000005', '000000009', 'Поддержка AAC', 3, 0, 0, 5, '', ''),
('000000006', '000000006', 'Экран', 2, 1, 1, 1, '', ''),
('000000007', '000000019', 'Оперативная память (RAM)', 4, 0, 0, 2, '', ''),
('000000008', '000000006', 'Встроенная память (ROM)', 4, 0, 0, 3, '', ''),
('000000009', '000000006', 'Основная камера МПикс', 1, 0, 0, 4, '', ''),
('000000010', '000000006', 'Фронтальная камера МПикс', 1, 0, 0, 5, '', ''),
('000000012', '000000011', 'Описание', 1, 0, 0, 1, '', ''),
('000000013', '000000014', 'Жирность', 2, 1, 0, 1, '', ''),
('000000014', '000000019', 'Операционная система', 4, 1, 0, 1, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `characteristic_value`
--

DROP TABLE IF EXISTS `characteristic_value`;
CREATE TABLE `characteristic_value` (
  `id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
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
('000000006', '64 ГБ', '000000008'),
('000000011', 'Android', '000000014'),
('000000012', 'Mac OS', '000000014'),
('000000010', 'MS Windows', '000000014'),
('e40b46b5a49d8ea31b17667af69e73b3', '20 Гц - 20 кГц', '000000002'),
('a064124b7ceedfef5a39be926b67e326', '29 Ом', '000000003'),
('c097bffb265ca788813aca5f2e6e47fc', '13/TOF', '000000010'),
('4afaf5166370676b6746d5a57bb64e74', '50/20/12/TOF', '000000009'),
('3ebc10f77d4430db8f5e8c80edccb61d', '6.76', '000000006'),
('788a87c59e2046e192210c2fc38a88f0', '1', '000000005'),
('0eb8894c04ffc63e6d014dc8d0bd1033', '1', '000000004'),
('2c4d637866bb854bd56fa65ea57c422a', '20 Гц - 20 кГц', '000000002'),
('cdc8348bf96b18087509a154c0a08b77', '29 Ом', '000000003'),
('76aedb70139e732e54e0b57c14b276fb', '13/TOF', '000000010'),
('23f392862c9ab49a42ba977bbcd42283', '50/20/12/TOF', '000000009'),
('2a8cfc28bc74bb228726535e12658464', '6.76', '000000006'),
('8b9259a72b4a4f6824d727fca85b0c79', '1', '000000005'),
('befc4eea8e8a3bb18c1709dde800461a', '1', '000000004'),
('52a4b956c1b57c8bd08d4d7ae6fc51aa', '20 Гц - 20 кГц', '000000002'),
('3b3b196213c3621f6c575b0caad1c9a8', '29 Ом', '000000003'),
('072446b56492b7047042a8a64a377a8b', '13/TOF', '000000010'),
('ce7c379985e2962c4fd62fd64b413358', '50/20/12/TOF', '000000009'),
('646eff2725f84710cf55d960600a2f80', '6.76', '000000006'),
('59a288a7c8940695db36c8f7f4b7843a', '13/TOF', '000000010'),
('1fb13690d1c07e9b894aad3f1f7b6b05', '29 Ом', '000000003'),
('295daa7e2850b76db8af7dbbfdd2a189', '20 Гц - 20 кГц', '000000002'),
('e909992ea59586ac40aacd95ddf6cb15', '1', '000000004'),
('2863c6c9311982912dd19cec2d0908f3', '1', '000000005'),
('28cfa696a393d891d2a6cf8deaa7de01', '2.5', '000000013'),
('0d8d728796337aaf81d214f35bd5d0f6', '3.2', '000000013'),
('e93d69825e700bc21681d9f7746a6071', '3.2', '000000013'),
('59cdc575eb2f7ed7f88027765f09a1ae', '', '000000012'),
('a1109ff3551872d9c6ba29b535818534', '', '000000014'),
('14ebfaef6f8cd936b8c416327b5acd35', '', '000000007'),
('32fa60f7cfffc13664ce562eae8ff8e3', '6.76', '000000006'),
('e3629786980b6a5161c8d1c6f5d246b9', '50/20/12/TOF', '000000009'),
('f0520302a3c5cf8f1a1070245523c5ff', '13/TOF', '000000010'),
('2c559ff49dfceca6ef691f63b0c3fd24', '29 Ом', '000000003'),
('c9ed82c3c8febd5b74a62b3b269ec655', '20 Гц - 20 кГц', '000000002'),
('854fcd84e3c06f5074bbbf26651e69e0', '1', '000000004'),
('02d8dc725e2bee3702033c24a7cf1473', '1', '000000005'),
('4efb20f50dcf550b65b9e2b456a5270f', '6.76', '000000006'),
('d8baf2509c0af98fd7f9dc86d0aac219', '50/20/12/TOF', '000000009'),
('82756362c3cbb61230550bb6ef78566e', '13/TOF', '000000010'),
('84cf72c315aae0b381502eedb8872ae8', '29 Ом', '000000003'),
('e87c7886b6371c17f9f9a9760feb92bb', '20 Гц - 20 кГц', '000000002'),
('c4ef243c7998a71ccf808f617d6781a3', '1', '000000004'),
('31c2bd46b21e5bbd95d8ac74c380e8bb', '1', '000000005'),
('6e3fb1f5999973fdc35b45ff49836a31', '6.76', '000000006'),
('25cf77471e15be4e1b3feaee3122d998', '50/20/12/TOF', '000000009'),
('0ddb8b88e25ea766d9dbb14beb5dcd3f', '13/TOF', '000000010'),
('ac58a8004615351446db6000eb9f49a8', '29 Ом', '000000003'),
('810050fa26372ae61310e80da3ca7e09', '20 Гц - 20 кГц', '000000002'),
('b5548debc35642c67682dbdd4400ee7c', '1', '000000004'),
('f0207890be96316465a0083bba020c9a', '1', '000000005'),
('', '1', '000000005'),
('banzaii', '1', '000000005'),
('3e980872d971672f7cb5668cb5da4b3e', '1', '000000004'),
('95fc6db8c8895624cc1bf84b0d466e32', '1', '000000005'),
('3b8d3958f1b2ddc93907f6bdd2e65e59', '2.5', '000000013'),
('94f4b4f7419ca73117a72820a06df144', '3.2', '000000013'),
('4a49ea9f75a6e407f8dc02f597f5526f', '3.2', '000000013');

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
('000000000001', '00003', '000000006', 'Смартфон vivo Y31, голубой океан', '', 'PL_08/17', '000000003,3ebc10f77d4430db8f5e8c80edccb61d,4afaf5166370676b6746d5a57bb64e74,c097bffb265ca788813aca5f2e6e47fc', '[{\"id\":\"000000006\",\"value\":6.76},{\"id\":\"000000008\",\"value\":\"\"},{\"id\":\"000000009\",\"value\":\"50/20/12/TOF\"},{\"id\":\"000000010\",\"value\":\"13/TOF\"}]', '000002'),
('000000000002', '00003', '000000009', 'Наушники True Wireless Huawei Freebuds Pro угольный черный', '', '50141256', '000000002,a064124b7ceedfef5a39be926b67e326,e40b46b5a49d8ea31b17667af69e73b3,3e980872d971672f7cb5668cb5da4b3e,95fc6db8c8895624cc1bf84b0d466e32', '[{\"id\":\"000000003\",\"value\":\"29 u041eu043c\"},{\"id\":\"000000002\",\"value\":\"20 u0413u0446 - 20 u043au0413u0446\"},{\"id\":\"000000004\",\"value\":true},{\"id\":\"000000005\",\"value\":true}]', '000003'),
('000000000004', '00003', '000000017', 'Молоко', '', 'М-0011', '3b8d3958f1b2ddc93907f6bdd2e65e59', '[{\"id\":\"000000013\",\"value\":2.5}]', '000004'),
('000000000005', '00003', '000000016', 'Кефир', '', 'К-0012', '94f4b4f7419ca73117a72820a06df144', '[{\"id\":\"000000013\",\"value\":3.2}]', '000004'),
('000000000006', '00003', '000000018', 'Ряженка', '', 'Р-00123', '4a49ea9f75a6e407f8dc02f597f5526f', '[{\"id\":\"000000013\",\"value\":3.2}]', '000004'),
('000000000003', '00004', '000000011', 'Холодильник Bosch Serie | 4 VitaFresh KGN39XW27R', '', '20068499', '', '[{\"id\":\"000000012\",\"value\":\"\"}]', '000001'),
('000000000007', '00005', '000000020', 'Ноутбук Acer Nitro 5 AN515-43-R45P черный', '', '1645276', '', '[{\"id\":\"000000014\",\"value\":\"\"},{\"id\":\"000000007\",\"value\":\"\"}]', '000005');

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

--
-- Dumping data for table `product_image`
--

INSERT INTO `product_image` (`product_id`, `ftp_url`, `http_url`, `sort_order`) VALUES
('000000000001', '30046493b.jpg', '30046493b.jpg', 0),
('000000000001', '30046493b2.jpg', '30046493b2.jpg', 0),
('000000000002', '50141256b.jpg', '50141256b.jpg', 0),
('000000000002', '50141256b2.jpg', '50141256b2.jpg', 0),
('000000000002', '50141256b4.jpg', '50141256b4.jpg', 0),
('000000000004', '40ec61411c92eac07cd543f0cee4ad81.jpg', '40ec61411c92eac07cd543f0cee4ad81.jpg', 0),
('000000000005', '1350x.jpg', '1350x.jpg', 0),
('000000000006', '571756.jpg', '571756.jpg', 0),
('000000000003', '20068499b.jpg', '20068499b.jpg', 0),
('000000000003', '20068499b3.jpg', '20068499b3.jpg', 0),
('000000000003', '20068499b1.jpg', '20068499b1.jpg', 0),
('000000000007', '', '', 0),
('000000000007', '1f19bc91ff7262a0d4c4d93e1ee663d403ee7f5888d07a80978c7b81b8c1cb35.jpg', '1f19bc91ff7262a0d4c4d93e1ee663d403ee7f5888d07a80978c7b81b8c1cb35.jpg', 0);

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
('00005', 'DNS', '', ''),
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
('000000000006', 69, '000000004'),
('000000000001', 0, '000000002'),
('000000000001', 0, '000000003'),
('000000000001', 0, '000000004');

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
