--
-- Table structure for table `site_header`
--

DROP TABLE IF EXISTS `site_header`;
CREATE TABLE `site_header` (
        `id`  VARCHAR(9) NOT NULL,
        `category_id` VARCHAR(9) NOT NULL,
        `title` TINYTEXT,
        `index_number` INT(11)

) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for table `site_header`
--

ALTER TABLE `site_header`
  ADD UNIQUE KEY `site_header_key` (`id`, `category_id`);

