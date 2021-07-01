DROP TABLE IF EXISTS `marker`;
CREATE TABLE `marker` (
  `id`	varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `product_id` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `marker_index` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

ALTER TABLE `marker`
  ADD PRIMARY KEY (`id`);
