
--
-- Структура таблицы `provider`
--

DROP TABLE IF EXISTS `provider`;
CREATE TABLE `provider` (
  `id` int NOT NULL,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `icon`	tinytext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `shops`
--
DROP TABLE IF EXISTS `shop`;
CREATE TABLE `shop` (
  `id`		int NOT NULL,
  `provider_id`	int NOT NULL,
  `title`	text COLLATE utf8_unicode_ci NOT NULL,
  `description`	text COLLATE utf8_unicode_ci NOT NULL,
  `address`	text COLLATE utf8_unicode_ci NOT NULL,
  `geox`	text COLLATE utf8_unicode_ci NOT NULL,
  `geoy`	text COLLATE utf8_unicode_ci NOT NULL,
  `icon`	text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Индексы таблицы `provider`
--
ALTER TABLE `provider`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `shops`
--
ALTER TABLE `shop`
  ADD PRIMARY KEY (`id`);
COMMIT;
