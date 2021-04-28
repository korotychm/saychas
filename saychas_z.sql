-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 28, 2021 at 01:53 AM
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
('000004', 'Простоквашино', '', ''),
('000006', 'Lenovo', '', ''),
('000007', 'Asus', '', ''),
('000008', 'Hewlett-Packard', '', ''),
('000009', 'Nokia', '', ''),
('000010', 'Xiaomi', '', '');

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
('000000001', '000000006', 'Поставщик', 1, 0, 0, 11, '', ''),
('000000010', '000000006', 'Фронтальная камера МПикс', 1, 1, 0, 10, '', ''),
('000000004', '000000009', 'Система активного подавле', 3, 0, 0, 4, '', ''),
('000000005', '000000009', 'Поддержка AAC', 3, 0, 0, 5, '', ''),
('000000009', '000000006', 'Основная камера МПикс', 1, 1, 0, 9, '', ''),
('000000019', '000000006', 'Камера', 1, 0, 0, 8, '', ''),
('000000008', '000000006', 'Встроенная память (ROM)', 4, 1, 0, 7, '', ''),
('000000011', '000000011', 'Цвет', 4, 1, 0, 2, '', ''),
('000000013', '000000014', 'Жирность', 2, 1, 0, 1, '', ''),
('000000014', '000000019', 'Операционная система', 4, 1, 0, 1, '', ''),
('000000007', '000000006', 'Оперативная память (RAM)', 4, 1, 0, 6, '', ''),
('000000018', '000000006', 'Память', 1, 0, 0, 5, '', ''),
('000000006', '000000006', 'Экран', 2, 1, 1, 4, '', ''),
('000000017', '000000006', 'Основные характеристики', 1, 0, 0, 3, '', ''),
('000000002', '000000006', 'Бренд', 4, 0, 0, 2, '', ''),
('000000012', '000000011', 'Описание', 1, 0, 0, 1, '', ''),
('000000003', '000000006', 'Цвет', 4, 0, 0, 1, '', '');

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
('000000013', '2 ГБ', '000000007'),
('000000005', '32 ГБ', '000000007'),
('000000003', '8 ГБ', '000000007'),
('000000007', '128 ГБ', '000000008'),
('000000008', '256 ГБ', '000000008'),
('000000014', '32 ГБ', '000000008'),
('000000009', '512 ГБ', '000000008'),
('000000006', '64 ГБ', '000000008'),
('000000011', 'Android', '000000014'),
('000000012', 'Mac OS', '000000014'),
('000000010', 'MS Windows', '000000014'),
('000000015', 'Общие характеристики', '000000003'),
('0f9543f2468b55a0651354429b18475b', '6.76', '000000006'),
('436456ee66c0bce03907b80160bb0189', '50/20/12/TOF', '000000009'),
('2644687e42d50d94f401fc28c20945f5', '13/TOF', '000000010'),
('1fb544b3ea0cc262206f7886e160c730', '6.76', '000000006'),
('a0f41bcf2a7f4ce777391ca449d31c40', '50/20/12/TOF', '000000009'),
('60c0aaa99112286cd938969cdb42e5d3', '13/TOF', '000000010'),
('9ed7faa3ec750225ae2445e67b1b26f4', '6.53', '000000006'),
('5417d83a0a6e766d2058224fa9d9590c', '13 Мп', '000000009'),
('ec44b05eab13fc1baafabbaa057430d9', '5 Мп', '000000010'),
('1d1bdfa86f650f5a94ea33c035a13c65', '6.53', '000000006'),
('1d91edb26545e820893cdf64b250ddca', '13 Мп', '000000009'),
('71c9467235b1f22fe0a4f9355b9f6b14', '5 Мп', '000000010'),
('85ce67a5b75159167c02b38438cfe53f', '000009', '000000002'),
('09b06a918b81cd2cae4249877a0948b4', '6.5', '000000006'),
('c534db13fe183f7f865a38a006cd70e5', '13+2 Мп', '000000009'),
('ecf8596cc6a290810db324cf2435db83', '5 Мп', '000000010'),
('511f6f94756f172166598b4408d0df3a', '00005', '000000001'),
('787166f4136884b1d4ad1e0018a879d4', '000009', '000000002'),
('308fb4276f78018be36f11cf4ce89623', '6.5', '000000006'),
('4010e0ef8ce2782a9eadb4c434af32a2', '13+2 Мп', '000000009'),
('dce997c2392dc57431e7aa40fb1bf8bf', '5 Мп', '000000010'),
('7522a9565e46c271b606921f100ee7cf', '00005', '000000001'),
('cf0244523770616035761f025c6deb5a', '6.76', '000000006'),
('8e58ad920b540203a04010b0a105fd1c', '50/20/12/TOF', '000000009'),
('b5b363f173da3249868b6d7f577fc554', '13/TOF', '000000010'),
('334f9d67ae88d9323ca05aabe1aed62b', '6.76', '000000006'),
('ea8a487edce005abf0f6bac11cb51f3c', '50/20/12/TOF', '000000009'),
('28df44bc206fe9de7deadbcafe62ec7e', '13/TOF', '000000010'),
('f89783f0296bc539406fdf782f278492', '6.76', '000000006'),
('d5636ee8c90d06d68d76d25fb6aa743f', '50/20/12/TOF', '000000009'),
('33a0c1f1c42e901bf1aa4ef4b788e14a', '13/TOF', '000000010'),
('20f75ef1ac19b8ea4f2cb5580b11b968', '6.76', '000000006'),
('c255aecbb9480c8c9ee7bb1bc9aa1f76', '50/20/12/TOF', '000000009'),
('d0998efec3d6e88cd14c99bc8f059754', '13/TOF', '000000010'),
('64c4aec52034ecc31fe7565eb46e6058', '6.76', '000000006'),
('1dfdfcc07390cdf9c14bb33627818eea', '50/20/12/TOF', '000000009'),
('21f17367ce421f84f22f6b8c945fd8e2', '13/TOF', '000000010'),
('b3d124d68c5f5ea938cf735db77d64e1', '6.76', '000000006'),
('b3e97dce4871f8bdf25d48a85bd6e197', '50/20/12/TOF', '000000009'),
('d9d283c75f2d5e457ecf94111083255b', '13/TOF', '000000010'),
('7822461ad42487c0fef6d7f27c0d2f34', '6.76', '000000006'),
('b6234072a170df6a54af0a13a7a9fe90', '50/20/12/TOF', '000000009'),
('fa0a9dd3bd4a069c959b22e2cd8b0219', '13/TOF', '000000010'),
('30527c6954b8861aeae0c283bcaca68e', '6.76', '000000006'),
('fbfb6e69980630a7ecf2fbf112f4f34f', '50/20/12/TOF', '000000009'),
('f60f04b120a394f5b0df195ef3353193', '13/TOF', '000000010'),
('423e1848f1d634e42de730a2e0f089a8', '6.76', '000000006'),
('0833eb51ad727c095ee94126b814d1dd', '50/20/12/TOF', '000000009'),
('e341262594f85704442cbe1fcaba2b5d', '13/TOF', '000000010'),
('b0e6f86e45679840ba0095ebfd9cd344', '6.76', '000000006'),
('a36aa3179bded833e4705a7d76a51009', '50/20/12/TOF', '000000009'),
('49ec587314d942c4be82266b064363e4', '13/TOF', '000000010'),
('20b2e0bf5d186dd0b4286447d5a21918', '6.76', '000000006'),
('7f81cd5e15ef7d9cb3a7d5fb4da2b1cd', '50/20/12/TOF', '000000009'),
('c64c80c4f19ba796fd89eb5b271fcc45', '13/TOF', '000000010'),
('3f1e071dad93c5beaa2cca9c0f8e25d0', '6.76', '000000006'),
('147ceae0b54e124925bbf81e749e850e', '50/20/12/TOF', '000000009'),
('e04076231578948842a7ee14417d53c8', '13/TOF', '000000010');

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
-- Table structure for table `post`
--

DROP TABLE IF EXISTS `post`;
CREATE TABLE `post` (
  `id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `blog` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`id`, `email`, `blog`) VALUES
('001', 'asdf@b.com', 'blog 1'),
('002', 'aaa@bbb.com', 'blog2'),
('003', 'a@c.com', 'blog 3');

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
('000000000001', '00003', '000000006', 'Смартфон vivo Y31, голубой океан', '', 'PL_08/17', '000000003,3f1e071dad93c5beaa2cca9c0f8e25d0,147ceae0b54e124925bbf81e749e850e,e04076231578948842a7ee14417d53c8', '[{\"id\":\"000000002\",\"is_title\":false,\"value\":\"\"},{\"id\":\"000000017\",\"is_title\":true,\"value\":\"\"},{\"id\":\"000000006\",\"is_title\":false,\"value\":6.76},{\"id\":\"000000018\",\"is_title\":true,\"value\":\"\"},{\"id\":\"000000008\",\"is_title\":false,\"value\":\"\"},{\"id\":\"000000019\",\"is_title\":true,\"value\":\"\"},{\"id\":\"000000009\",\"is_title\":false,\"value\":\"50/20/12/TOF\"},{\"id\":\"000000010\",\"is_title\":false,\"value\":\"13/TOF\"},{\"id\":\"000000001\",\"is_title\":false,\"value\":\"\"}]', '000002');

-- --------------------------------------------------------

--
-- Table structure for table `product_image`
--

DROP TABLE IF EXISTS `product_image`;
CREATE TABLE `product_image` (
  `product_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ftp_url` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `http_url` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
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
('000000000012', '86d9153895ba98a3a9a8faccce9ff52e8284f4e102d77fc6c5e121f12e4c8f59.jpg', '86d9153895ba98a3a9a8faccce9ff52e8284f4e102d77fc6c5e121f12e4c8f59.jpg', 0),
('000000000012', 'bf02a57f79dd12440b9443ae6de8c0e9909957955900acce183db41c145df138.jpg', 'bf02a57f79dd12440b9443ae6de8c0e9909957955900acce183db41c145df138.jpg', 0),
('000000000011', '74d209fa322b0ea21ae8cd5464fa7524c9dbfac43874a701cc45854495201cd7.jpg', '74d209fa322b0ea21ae8cd5464fa7524c9dbfac43874a701cc45854495201cd7.jpg', 0),
('000000000010', '6a8924e4368922057a4789f35c5045b013680f391a4728b99c8902fd4315536f.jpg', '6a8924e4368922057a4789f35c5045b013680f391a4728b99c8902fd4315536f.jpg', 0),
('000000000010', '95c1e5f9fdc0a8f268d50f22bb5930bb514fabb8a979540bb2e2c485e9e1d5cf.jpg', '95c1e5f9fdc0a8f268d50f22bb5930bb514fabb8a979540bb2e2c485e9e1d5cf.jpg', 0),
('000000000009', '79f9c8d1590e5036d2533c10a6d3030c4c3f37d57d93ce3ddab4d6a8a8586c69.jpg', '79f9c8d1590e5036d2533c10a6d3030c4c3f37d57d93ce3ddab4d6a8a8586c69.jpg', 0),
('000000000008', '2aea360b4ac330436b47f0f3edaae48d3a8bdac9ceb00fb4d1504c28a1c2821d.jpg', '2aea360b4ac330436b47f0f3edaae48d3a8bdac9ceb00fb4d1504c28a1c2821d.jpg', 0),
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
-- Table structure for table `size`
--

DROP TABLE IF EXISTS `size`;
CREATE TABLE `size` (
  `id` varchar(9) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `size`
--

INSERT INTO `size` (`id`, `title`) VALUES
('000000002', 'XXL'),
('000000001', 'XXXL');

-- --------------------------------------------------------

--
-- Table structure for table `stock_balance`
--

DROP TABLE IF EXISTS `stock_balance`;
CREATE TABLE `stock_balance` (
  `product_id` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `rest` int NOT NULL DEFAULT '0',
  `size` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `store_id` varchar(9) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `stock_balance`
--

INSERT INTO `stock_balance` (`product_id`, `rest`, `size`, `store_id`) VALUES
('000000000002', 1, '', '000000004'),
('000000000002', 10, '', '000000002'),
('000000000002', 3, '', '000000003'),
('000000000003', 8, '', '000000005'),
('000000000004', 6, '', '000000002'),
('000000000004', 4, '', '000000003'),
('000000000004', 2, '', '000000004'),
('000000000005', 15, '', '000000002'),
('000000000005', 38, '', '000000003'),
('000000000005', 42, '', '000000004'),
('000000000006', 30, '', '000000002'),
('000000000006', 14, '', '000000003'),
('000000000006', 69, '', '000000004'),
('000000000001', 0, '', '000000002'),
('000000000001', 0, '', '000000003'),
('000000000001', 0, '', '000000004'),
('000000000007', 10, '', '000000005');

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
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int NOT NULL,
  `name` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `phone` int DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci NOT NULL,
  `geodata` text COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
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
-- Indexes for table `size`
--
ALTER TABLE `size`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_balance`
--
ALTER TABLE `stock_balance`
  ADD PRIMARY KEY (`product_id`,`size`,`store_id`);

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
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`);

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

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
