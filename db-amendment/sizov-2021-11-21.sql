/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  plusweb
 * Created: Oct 21, 2021
 */

ALTER TABLE `category` ADD `url` VARCHAR(150) NULL DEFAULT NULL AFTER `sort_order`; 

--
-- Table structure for table `setting`
--

DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` (
  `id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `value` json DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `value`) VALUES

('delivery_params', '{\"hourPrice\": 29900, \"mergePrice\": 5000, \"mergecount\": 4, \"deliveryTax\": 20, \"mergePriceFirst\": 24900}'),
('organisation', '{\"name\": \"saychas\", \"phone\": \"79994444444\", \"taxation\": \"osn\"}');

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD UNIQUE KEY `id` (`id`);
COMMIT;