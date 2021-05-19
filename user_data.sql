DROP TABLE IF EXISTS `user_data`;
CREATE TABLE `user_data` (
  `id` int,
  `user_id` int,
  `address` text COLLATE utf8_unicode_ci,
  `geodata` text COLLATE utf8_unicode_ci,
  `time` time
  -- `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `user_data`
  ADD PRIMARY KEY (`id`); -- ,

ALTER TABLE `user_data`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

