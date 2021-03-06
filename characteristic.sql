--
-- Table structure for table `characteristic`
--

DROP TABLE IF EXISTS `characteristic`;
CREATE TABLE `characteristic` (
  `id` varchar(9) NOT NULL DEFAULT '',
  `category_id` varchar(9) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `filter` tinyint(1) NOT NULL DEFAULT '0',
  `group` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '1',
  PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `predefined_characteristic_value`;
DROP TABLE IF EXISTS `predef_char_value`;

CREATE TABLE `predef_char_value` (
	`id`	VARCHAR(9) NOT NULL DEFAULT '',
	`title`	VARCHAR(255) NOT NULL DEFAULT '',
	`characteristic_id` VARCHAR(9)  NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `characteristic` (`id`) VALUES ('000000001');
