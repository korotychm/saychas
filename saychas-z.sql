-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Мар 17 2021 г., 07:18
-- Версия сервера: 8.0.22
-- Версия PHP: 7.4.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `saychas_z`
--

-- --------------------------------------------------------

--
-- Структура таблицы `brand`
--

DROP TABLE IF EXISTS `brand`;
CREATE TABLE `brand` (
  `id` int NOT NULL,
  `title` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `logo` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `category`
--

-- DROP TABLE IF EXISTS `category`;
-- CREATE TABLE `category` (
--   `group_name` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
--   `parent` VARCHAR(9) NOT NULL DEFAULT '0',
--   `comment` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
--   `id_1C_group` VARCHAR(9) NOT NULL,
--   `icon`  VARCHAR(11) NOT NULL DEFAULT '',
--   `rang` int NOT NULL DEFAULT '0'
-- ) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_unicode_ci COMMENT='Категории товаров';


-- $sql = sprintf("replace INTO `category`(`group_name`, `parent`, `comment`, `id_1C_group`, `icon`, `rang`) VALUES ( '%s', '%s', '%s', '%s', %u, %u)",
--         $row['title'], empty($row['parent_id']) ? '0' : $row['parent_id'], $row['description'], $row['id'], $row['icon'], $row['sort_order']);

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` VARCHAR(9) NOT NULL,
  `parent_id` VARCHAR(9) NOT NULL DEFAULT '0',
  `title` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `icon`  VARCHAR(11) NOT NULL DEFAULT '',
  `sort_order` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_unicode_ci COMMENT='Категории товаров';

--
-- Дамп данных таблицы `category`
--

-- INSERT INTO `category` (`group_name`, `parent`, `comment`, `id_1C_group`, `icon`, `rang`) VALUES
INSERT INTO `category` (`title`, `parent_id`, `description`, `id`, `icon`, `sort_order`) VALUES
('Алкотестеры', 2, '', 29, 0, 0),
('Физиотерапия и магнитотерапия', 2, '', 25, 0, 0),
('Кварцевание/Облучатели бактерицидные', 2, '', 26, 0, 0),
('Тонометры', 2, '', 21, 0, 0),
('Глюкометры, тест-полоски', 2, '', 24, 0, 0),
('Ингаляторы и небулайзеры', 2, '', 23, 0, 0),
('Термометры', 2, '', 28, 0, 0),
('Грелки электрические и солевые', 2, '', 27, 0, 0),
('Приспособления для стоп', 4, '', 47, 0, 0),
('Матрасы ортопедические', 4, '', 4912, 0, 0),
('Подушки ортопедические', 4, '', 41, 0, 0),
('Бандажи/Корсеты/Пояса', 4, '', 43, 0, 0),
('Бандажи/ Фиксаторы суставов', 4, '', 44, 0, 0),
('пусто', 4, '', 4911, 0, 0),
('Бинты эластичные и самофиксирующиеся', 4, '', 48, 0, 0),
('Детская ортопедическая обувь', 4, '', 4811, 0, 0),
('Стельки ортопедические', 4, '', 46, 0, 0),
('Бинты полимерный (полиуретановые)', 4, '', 49, 0, 0),
('Детская ортопедия', 4, '', 491, 0, 0),
('Бандажи до и после родовые', 4, '', 42, 0, 0),
('Компрессионный трикотаж', 4, '', 45, 0, 0),
('Компрессионные гольфы', 5, '', 51, 0, 0),
('Компрессионные чулки', 5, '', 52, 0, 0),
('Компрессионные колготки', 5, '', 53, 0, 0),
('Госпитальный трикотаж', 5, '', 54, 0, 0),
('Компрессионные рукава', 5, '', 56, 0, 0),
('Моночулок', 5, '', 58, 0, 0),
('Пусто', 5, '', 57, 0, 0),
('Устройства для надевания компрессионного трикотажа', 5, '', 59, 0, 0),
('Велотренажеры', 6, '', 67, 0, 0),
('Кресла-коляски инвалидные', 6, '', 63, 0, 0),
('Костыли', 6, '', 65, 0, 0),
('Трости', 6, '', 66, 0, 0),
('Ходунки, опоры', 6, '', 64, 0, 0),
('Матрасы противопролежневые и подушки', 6, '', 62, 0, 0),
('Кровати медицинские', 6, '', 61, 0, 0),
('Подъемники и пандусы', 6, '', 69, 0, 0),
('Приспособления для мытья и гигиены', 8, '', 8901, 0, 0),
('Ванны надувные и ступени для ванной', 8, '', 84, 0, 0),
('Стулья и сиденья для ванной', 8, '', 83, 0, 0),
('Приспособление для туалета', 8, '', 86, 0, 0),
('Биотулеты', 8, '', 82, 0, 0),
('Кресла-туалеты', 8, '', 81, 0, 0),
('Судно подкладное', 8, '', 89, 0, 0),
('Поручни для ванной и туалета', 8, '', 88, 0, 0),
('Калоприемники и уход за стомой', 10, '', 107, 0, 0),
('Антисептики', 10, '', 109, 0, 0),
('Мочеприемники', 10, '', 106, 0, 0),
('Пеленки и клеенки гигиенические', 10, '', 102, 0, 0),
('Подгузники и трусики для взрослых', 10, '', 101, 0, 0),
('Прокладки урологические', 10, '', 103, 0, 0),
('Повязки и пластыри противовоспалительные', 10, '', 104, 0, 0),
('Шампуни, гели , крема для лежачих больных', 10, '', 108, 0, 0),
('Ватная и марлевая продукция', 10, '', 1091, 0, 0),
('Катетеры', 10, '', 105, 0, 0),
('Грелки резиновые, спринцовки, клизмы, жгуты', 10, '', 1092, 0, 0),
('Контейнеры для биопроб', 10, '', 1093, 0, 0),
('Автоматические тонометры', 21, '', 211, 0, 0),
('Автоматические тонометры на запястье', 21, '', 212, 0, 0),
('Полуавтоматические тонометры', 21, '', 213, 0, 0),
('Механические тонометры', 21, '', 214, 0, 0),
('Стетоскопы', 21, '', 215, 0, 0),
('Запасные части к тонометрам', 21, '', 216, 0, 0),
('Компрессорные ингаляторы и небулайзеры', 23, '', 231, 0, 0),
('Ультразвуковые ингаляторы и небулайзеры', 23, '', 232, 0, 0),
('Паровые ингаляторы', 23, '', 233, 0, 0),
('Запасные части к ингаляторам и небулайзерам', 23, '', 234, 0, 0),
('Экспересс-тесты', 24, '', 245, 0, 0),
('Носки для диабетиков', 24, '', 246, 0, 0),
('Глюкометры', 24, '', 241, 0, 0),
('Тест-полоски', 24, '', 242, 0, 0),
('Ланцеты, ручки-прокалыватели', 24, '', 243, 0, 0),
('Принадлежности для ввода инсулина', 24, '', 244, 0, 0),
('Приборы Елатомского завода', 25, '', 251, 0, 0),
('Аппараты ДЭНАС', 25, '', 253, 0, 0),
('Аппараты лазерной, физио/магнито терапии', 25, '', 254, 0, 0),
('Приборы фототерапии', 25, '', 258, 0, 0),
('БИОМАГ магнитотерапия', 25, '', 255, 0, 0),
('Приборы Витафон', 25, '', 252, 0, 0),
('Запасные части', 26, '', 264, 0, 0),
('Рециркуляторы/облучатели бактерицидные закрытого типа', 26, '', 261, 0, 0),
('Облучатели бактерицидные открытого типа', 26, '', 262, 0, 0),
('Облучатели ультрафиолетовые, инфракрасные', 26, '', 263, 0, 0),
('Электропростыни', 27, '', 272, 0, 0),
('Электрогрелки', 27, '', 273, 0, 0),
('Электроодеяло', 27, '', 274, 0, 0),
('ПУСТО Электрические грелки и простыни', 27, '', 271, 0, 0),
('Солевые аппликаторы', 27, '', 275, 0, 0),
('Гигрометры - термометры', 28, '', 284, 0, 0),
('Электронные термометры', 28, '', 281, 0, 0),
('Инфракрасные термометры', 28, '', 282, 0, 0),
('Ртутные/безртутные термометры, Гигрометры', 28, '', 283, 0, 0),
('Подушки под голову', 41, '', 411, 0, 0),
('Подушки под спину и на сиденье', 41, '', 412, 0, 0),
('Подушки для детей и беременных', 41, '', 413, 0, 0),
('Подушки для путешествий', 41, '', 414, 0, 0),
('Наволочки для ортопедических подушек', 41, '', 415, 0, 0),
('Бандажи послеоперационные', 43, '', 432, 0, 0),
('Корсеты ортопедические', 43, '', 434, 0, 0),
('Бандажи грыжевые/ при опущение малого таза', 43, '', 433, 0, 0),
('Корректоры осанки', 43, '', 435, 0, 0),
('Пояса согревающие', 43, '', 436, 0, 0),
('Бандажи для шеи (Шина Шанца)', 43, '', 431, 0, 0),
('Детские бандажи', 43, '', 437, 0, 0),
('Бандажи на коленный сустав', 44, '', 441, 0, 0),
('Бандажи на голеностопный сустав', 44, '', 442, 0, 0),
('Бандажи на плечевой и локтевой сустав', 44, '', 443, 0, 0),
('Бандажи на лучезапястный сустав', 44, '', 444, 0, 0),
('Фиксаторы тазобедренного сустава', 44, '', 445, 0, 0),
('ПУСТО Профилактические', 45, '', 452, 0, 0),
('ПУСТО Первый класс компрессии', 45, '', 453, 0, 0),
('ПУСТО Второй класс компрессии', 45, '', 454, 0, 0),
('ПУСТО VENOTEKS', 45, '', 451, 0, 0),
('ПУСТО Третий класс компрессии', 45, '', 455, 0, 0),
('ПУСТО Госпитальный трикотаж', 45, '', 456, 0, 0),
('ПУСТО Колготки для беременных', 45, '', 457, 0, 0),
('ПУСТО Рукава компрессионные', 45, '', 458, 0, 0),
('Стельки ортопедические', 46, '', 4601, 0, 0),
('Стельки ортопедические детские', 46, '', 4602, 0, 0),
('Полустельки', 46, '', 4603, 0, 0),
('Стельки SCHOLL', 46, '', 4604, 0, 0),
('Подпяточники', 47, '', 471, 0, 0),
('Корректоры стопы', 47, '', 472, 0, 0),
('Гольфы 1 класс компрессии 18-22 мм рт', 51, '', 512, 0, 0),
('Гольфы 2 класс компресии 23-32 мм рт', 51, '', 513, 0, 0),
('Гольфы антиэмболические', 51, '', 514, 0, 0),
('Гольфы профилактические 15-18 мм рт', 51, '', 511, 0, 0),
('Чулки профилактические 15-18 мм рт', 52, '', 522, 0, 0),
('Чулки 1 класс компрессии 18-22 мм рт', 52, '', 523, 0, 0),
('Чулки 2 класс компрессии 23-32 мм рт', 52, '', 524, 0, 0),
('Чулки 3 класс компресии 30-35 мм рт', 52, '', 525, 0, 0),
('Колготки профилактические 15-18 мм рт', 53, '', 532, 0, 0),
('Колготки 1 класс компресии 18-22 мм рт', 53, '', 533, 0, 0),
('Колготки 2 класс компрессии 23-32 мм рт', 53, '', 534, 0, 0),
('Колготки для беременных компрессионные', 53, '', 536, 0, 0),
('Кровати механические функциональные', 61, '', 611, 0, 0),
('Кровати электрические функциональные', 61, '', 612, 0, 0),
('Матрасы для кроватей', 61, '', 613, 0, 0),
('Кресла-коляски для активных пользователей', 63, '', 631, 0, 0),
('Кресла-коляски для пассивных пользователей', 63, '', 632, 0, 0),
('Дополнительные приспособления', 63, '', 63903, 0, 0),
('Кресла-коляски со складной спинкой', 63, '', 634, 0, 0),
('Кресла-коляски повышенной грузоподъемности', 63, '', 635, 0, 0),
('Кресла-коляски с рычажным управлением', 63, '', 637, 0, 0),
('Детские кресла коляски', 63, '', 638, 0, 0),
('Кресла-коляски с санитарным оснащением', 63, '', 636, 0, 0),
('Кресла-коляски для больных ДЦП', 63, '', 63902, 0, 0),
('Кресла-коляски электрические', 63, '', 639, 0, 0),
('Ходунки, опоры', 64, '', 641, 0, 0),
('Ходунки- ролляторы (на колесах)', 64, '', 642, 0, 0),
('Пусто', 64, '', 643, 0, 0),
('Костыли подмышечные', 65, '', 651, 0, 0),
('Костыли с опорой под локоть', 65, '', 652, 0, 0),
('Аксессуары для костылей и тростей', 65, '', 653, 0, 0),
('Телескопические, нерегулируемые', 66, '', 661, 0, 0),
('Трости-опоры', 66, '', 662, 0, 0),
('Аксессуары для тростей', 66, '', 665, 0, 0),
('Эксклюзивные трости', 66, '', 663, 0, 0),
('Беруши и свечи ушные', 120, '', 1208, 0, 0),
('Кухонные принадлежности', 120, '', 1205, 0, 0),
('Подголовники и поручни прикроватные', 120, '', 1204, 0, 0),
('Захваты, ледоходы', 120, '', 1206, 0, 0),
('Противогрибковая обработка обуви', 120, '', 12094, 0, 0),
('Лупы увеличительные', 120, '', 1207, 0, 0),
('Столики прикроватные', 120, '', 1202, 0, 0),
('Пусто', 120, '', 1203, 0, 0),
('Таблетницы', 120, '', 1209, 0, 0),
('Тренажеры вагинальные', 140, '', 1409999, 0, 0),
('Пояса и миостимуляторы', 140, '', 1405, 0, 0),
('Массажеры  для ног', 140, '', 1404, 0, 0),
('Массажеры для тела', 140, '', 1401, 0, 0),
('Аккупунктурные коврики', 140, '', 1409, 0, 0),
('Массажеры для шеи и плеч', 140, '', 1402, 0, 0),
('Массажные кресла', 140, '', 14097, 0, 0),
('Вакуумный массаж', 140, '', 1407, 0, 0),
('Ванны гидромассажные', 140, '', 14099, 0, 0),
('Массажные накидки и подушки', 140, '', 1406, 0, 0),
('Массажеры механические', 140, '', 14093, 0, 0),
('Аппликаторы ЛЯПКО и КУЗНЕЦОВА', 140, '', 1408, 0, 0),
('Массажеры для глаз и головы', 140, '', 1403, 0, 0),
('Массажные столы', 140, '', 14095, 0, 0),
('Су Джок товары (иглы аккупунктурные)', 160, '', 1604, 0, 0),
('Трикотаж с увлажняющим гелем', 160, '', 1606, 0, 0),
('Назальные аспираторы', 160, '', 1607, 0, 0),
('Бельё корректирующее', 160, '', 1603, 0, 0),
('Косметология', 160, '', 1601, 0, 0),
('Уход за полостью рта', 160, '', 1602, 0, 0),
('Очки релаксационные, реабилитационные', 160, '', 1605, 0, 0),
('Алтайский бальзамы', 170, '', 1701, 0, 0),
('Пантовая продукция', 170, '', 1702, 0, 0),
('Мед Алтая', 170, '', 1703, 0, 0),
('Алтайски травы и фиточаи', 170, '', 1704, 0, 0),
('Натуральная косметика', 170, '', 1705, 0, 0),
('Косметические и Эфирные масла', 170, '', 1707, 0, 0),
('Балансировочные подушки, диски', 180, '', 1808, 0, 0),
('Охлаждающие принадлежности', 180, '', 180999, 0, 0),
('Палки для скандинавской ходьбы', 180, '', 1806, 0, 0),
('Кинезио тейпы', 180, '', 1804, 0, 0),
('Тренажеры', 180, '', 1803, 0, 0),
('Шагомеры и пульсометры', 180, '', 18099, 0, 0),
('Часы песочные', 180, '', 1809, 0, 0),
('Эспандеры', 180, '', 1805, 0, 0),
('Мячи гимнастичесие', 180, '', 1807, 0, 0),
('Весы', 180, '', 1802, 0, 0),
('Запасные части', 200, '', 2006, 0, 0),
('Кислородные концентраторы', 200, '', 2001, 0, 0),
('Кислородные коктейлеры и смеси', 200, '', 2002, 0, 0),
('Кислородные балончики, подушки', 200, '', 2003, 0, 0),
('Дыхательные тренажеры', 200, '', 2004, 0, 0),
('Пульсоксиметры', 200, '', 2005, 0, 0),
('Пусто', 202, '', 2029, 0, 0),
('Солевые лампы', 202, '', 2023, 0, 0),
('Воздухоочистители-ионизаторы', 202, '', 2021, 0, 0),
('Увлажнители воздуха', 202, '', 2022, 0, 0),
('Нитрат-тестеры, дозиметры', 202, '', 2025, 0, 0),
('Осеребрители и Активаторы воды', 202, '', 2024, 0, 0),
('Ароматерапия', 202, '', 2026, 0, 0),
('Обогреватели', 202, '', 2027, 0, 0),
('Запасные части', 202, '', 2028, 0, 0),
('Молокоотсосы, стерилизаторы, блендеры, подогреватели', 203, '', 2034, 0, 0),
('Гигиена для будущих мам', 203, '', 2033, 0, 0),
('Сумки и рюкзаки', 203, '', 2038, 0, 0),
('Бельё для беременных и кормящих мам', 203, '', 2031, 0, 0),
('Питание для беременных и кормящих', 203, '', 2036, 0, 0),
('Колготки для беременных', 203, '', 2032, 0, 0),
('Подгузники', 204, '', 2042, 0, 0),
('Пусто', 204, '', 2041, 0, 0),
('Детское питание', 204, '', 2043, 0, 0),
('Кормление и уход', 204, '', 2044, 0, 0),
('Детская гигиена', 204, '', 2045, 0, 0),
('Здоровье и безопасность', 204, '', 2046, 0, 0),
('Игрушки', 204, '', 2047, 0, 0),
('Предметы женской гигиены', 205, '', 2051, 0, 0),
('Уход за полостью рта', 205, '', 2052, 0, 0),
('Бумажно-ватные изделия', 205, '', 2053, 0, 0),
('Тесты на беременность, овуляцию', 205, '', 2056, 0, 0),
('Презервативы, гель-смазки', 205, '', 2054, 0, 0),
('Медицинские халаты, блузы, брюки', 206, '', 2061, 0, 0),
('Медицинские костюмы', 206, '', 2062, 0, 0),
('Маски, экраны и комбинизоны защитные', 206, '', 2063, 0, 0),
('Бахилы', 206, '', 2064, 0, 0),
('Головные уборы', 206, '', 2065, 0, 0),
('Простыни гигиенические', 206, '', 2066, 0, 0),
('Перчатки медицинские', 208, '', 2089, 0, 0),
('Микроскопы', 208, '', 2087, 0, 0),
('Гели для УЗИ', 208, '', 2083, 0, 0),
('Дезинфекция и стерилизация', 208, '', 2084, 0, 0),
('Медицинский инструмент', 208, '', 2082, 0, 0),
('Отсасыватели хирургические', 208, '', 2085, 0, 0),
('Эндопротезы', 208, '', 2093, 0, 0),
('Офтальмологическое оборудование', 208, '', 2092, 0, 0),
('Диагностическое оборудование', 208, '', 2096, 0, 0),
('Термоконтейнеры медицинские', 208, '', 2097, 0, 0),
('Весы медицинские', 208, '', 2099, 0, 0),
('Светильники диагностические и негатоскопы', 208, '', 2088, 0, 0),
('Гинекология', 208, '', 2086, 0, 0),
('Аптечки специализированные', 208, '', 2094, 0, 0),
('Медицинская мебель', 208, '', 2081, 0, 0),
('Товары искусственнной вентиляции легких', 208, '', 2095, 0, 0),
('Шприцы одноразовые, многоразовые', 208, '', 2098, 0, 0),
('Товары для лабораторий', 208, '', 2091, 0, 0),
('Слуховые аппараты', 300, '', 3001, 0, 0),
('Усилители звука', 300, '', 3002, 0, 0),
('Аксессуары к слуховым аппаратам', 300, '', 3004, 0, 0),
('Очки водительские', 302, '', 3022, 0, 0),
('Аптечки автомобильные', 302, '', 3025, 0, 0),
('Для путешествий', 302, '', 3023, 0, 0),
('Алкотестеры', 302, '', 3021, 0, 0),
('Глюкометры', 304, '', 3041, 0, 0),
('Тест-полоски', 304, '', 3042, 0, 0),
('Ланцеты, ручки-прокалыватели', 304, '', 3043, 0, 0),
('Питание при диабете', 304, '', 3044, 0, 0),
('Прокат медтехники', 308, '', 3081, 0, 0),
('Прокат средств реабилитации', 308, '', 3082, 0, 0),
('Бандажи для шеи (Шина Шанца)', 435, '', 4351, 0, 0),
('Приборы Дарсонваль', 1601, '', 16011, 0, 0),
('Дезинфекция и стерилизация', 1601, '', 16016, 0, 0),
('Приборы по уходу за лицом и телом', 1601, '', 16012, 0, 0),
('Приборы для маникюра и педикюра', 1601, '', 16013, 0, 0),
('Маникюрные принадлежности и зеркала', 1601, '', 16014, 0, 0),
('Оборудование для салонов красоты', 1601, '', 16015, 0, 0),
('Зубная паста и  ополаскиватели', 1602, '', 16024, 0, 0),
('Зубные ершики и нити', 1602, '', 16025, 0, 0),
('Ирригаторы полости рта', 1602, '', 16021, 0, 0),
('Электрические зубные щетки', 1602, '', 16022, 0, 0),
('Сменные насадки', 1602, '', 16023, 0, 0),
('Весы электронные напольные', 1802, '', 18021, 0, 0),
('Весы диагностические с анализатором жира и воды', 1802, '', 18022, 0, 0),
('Весы детские электронные', 1802, '', 18023, 0, 0),
('Весы кухонные, бытовые', 1802, '', 18024, 0, 0),
('Весы мини цифровые', 1802, '', 18025, 0, 0),
('Солевые лампы СКАЛА', 2023, '', 20231111, 0, 0),
('Солевые лампы ФИГУРНЫЕ', 2023, '', 20311112, 0, 0),
('Изделия из соли', 2023, '', 20311113, 0, 0),
('Дополнительные принадлежности', 2023, '', 20311114, 0, 0),
('Подгузники', 2042, '', 20421, 0, 0),
('Трусики', 2042, '', 20422, 0, 0),
('Пеленки и клеенки', 2042, '', 20423, 0, 0),
('Смеси и каши', 2043, '', 20431, 0, 0),
('Соки и напитки', 2043, '', 20432, 0, 0),
('Пюре', 2043, '', 20433, 0, 0),
('Вода', 2043, '', 20434, 0, 0),
('Бутылки и соски', 2044, '', 20441, 0, 0),
('Пустышки', 2044, '', 20443, 0, 0),
('Прорезыватели', 2044, '', 20444, 0, 0),
('Детская посуда, контейнеры', 2044, '', 20445, 0, 0),
('Предметы ухода', 2044, '', 20446, 0, 0),
('пусто', 2044, '', 20448, 0, 0),
('Наборы', 2044, '', 20447, 0, 0),
('Поильники', 2044, '', 20442, 0, 0),
('Влажные салфетки и ватные изделия', 2045, '', 20451, 0, 0),
('Мыло детское', 2045, '', 20452, 0, 0),
('Крема, масла, гели , присыпки', 2045, '', 20453, 0, 0),
('Шампуни и пены', 2045, '', 20454, 0, 0),
('Уход за полость рта', 2045, '', 20455, 0, 0),
('Назальные аспираторы', 2046, '', 20469, 0, 0),
('Радионяни и видеоняни', 2046, '', 20461, 0, 0),
('Ингаляторы и небулайзеры', 2046, '', 20462, 0, 0),
('Весы детские', 2046, '', 20463, 0, 0),
('Термометры', 2046, '', 20464, 0, 0),
('Облучатели и рециркуляторы', 2046, '', 20465, 0, 0),
('Детская бытовая химия', 2046, '', 20466, 0, 0),
('Ограничители', 2046, '', 20467, 0, 0),
('Круги на шею', 2046, '', 20468, 0, 0),
('Игрушки-погремушки', 2047, '', 20471, 0, 0),
('Развивающие игрушки и коврики', 2047, '', 20472, 0, 0),
('Музыкальные игрушки и подвески', 2047, '', 20473, 0, 0),
('Конструкторы и пирамиды', 2047, '', 20474, 0, 0),
('Игрушки для ванной', 2047, '', 20475, 0, 0),
('Игрушки для творчества, пазлы', 2047, '', 20476, 0, 0),
('Мягкая игрушка', 2047, '', 20477, 0, 0),
('Прокладки и тампоны', 2051, '', 20511, 0, 0),
('Шампуни и гели', 2051, '', 20512, 0, 0),
('Бальзамы и ополаскиватели', 2051, '', 20513, 0, 0),
('Косметика для лица', 2051, '', 20514, 0, 0),
('Косметика для волос', 2051, '', 20515, 0, 0),
('Косметика для тела', 2051, '', 20516, 0, 0),
('Зубные щетки', 2052, '', 20522, 0, 0),
('Зубные пасты', 2052, '', 20521, 0, 0),
('Ополаскиватели', 2052, '', 20523, 0, 0),
('Туалетная бумага', 2053, '', 20531, 0, 0),
('Ватные палоски и диски', 2053, '', 20532, 0, 0),
('Влажные салфетки', 2053, '', 20533, 0, 0),
('Платки носовые', 2053, '', 20534, 0, 0),
('Кушетки и банкетки медицинские', 2081, '', 20812, 0, 0),
('Столики и банкетки медицинские', 2081, '', 20813, 0, 0),
('Шкафы медицинские', 2081, '', 20811, 0, 0),
('Пусто', 2081, '', 20819, 0, 0),
('Шкафы медицинские для раздевалок', 2081, '', 20814, 0, 0),
('Носилки и Шины', 2081, '', 20818, 0, 0),
('Ширмы и Штативы медицинские', 2081, '', 20817, 0, 0),
('Ростомеры', 2081, '', 20816, 0, 0),
('Акушерство и гинекология', 2082, '', 20821, 0, 0),
('Инструменты разные', 2082, '', 20826, 0, 0),
('Логопедические инструменты', 2082, '', 20827, 0, 0),
('Ножницы и зажимы', 2082, '', 20822, 0, 0),
('Пинцеты и скальпели', 2082, '', 20823, 0, 0),
('Шпатели', 2082, '', 20824, 0, 0),
('Пусто', 2082, '', 20299999, 0, 0),
('Камертоны', 2082, '', 2082902, 0, 0),
('Молотки неврологические', 2082, '', 20825, 0, 0),
('Отоскопы', 2082, '', 20829, 0, 0),
('Офтальмоскопы', 2082, '', 2082901, 0, 0),
('Дерматоскопы', 2082, '', 20828, 0, 0),
('Камеры стерилизационные', 2084, '', 20842, 0, 0),
('Дезинфицирующие средства, Дозаторы', 2084, '', 20841, 0, 0),
('Контейнеры для хранения, стерилизации и отходов', 2084, '', 20843, 0, 0),
('Пробирки, стаканы и штативы', 2091, '', 20912, 0, 0),
('Весы лабораторные', 2091, '', 20913, 0, 0),
('Холодильники и центрифуги', 2091, '', 20914, 0, 0),
('Компрессионный трикотаж', 0, '', 5, 0, 0),
('Красота и здоровье', 0, '', 160, 0, 0),
('Товары для диабетиков', 0, '', 304, 0, 0),
('Товары для водителей', 0, '', 302, 0, 0),
('Товары для медицинских учреждений', 0, '', 208, 0, 0),
('Пусто', 0, '', 400, 0, 0),
('Массажное оборудование', 0, '', 140, 0, 0),
('Экология в доме', 0, '', 202, 0, 0),
('Кислородное оборудование', 0, '', 200, 0, 0),
('Товары для слабовидящих и слабослышащих', 0, '', 300, 0, 0),
('Алтайская продукция', 0, '', 170, 0, 0),
('Товары для детей', 0, '', 204, 0, 0),
('Реабилитационная техника', 0, '', 6, 0, 0),
('Санитарные приспособления', 0, '', 8, 0, 0),
('Гигиена и уход за больными', 0, '', 10, 0, 0),
('Спорт и фитнес', 0, '', 180, 0, 0),
('Ортопедия', 0, '', 4, 0, 0),
('Медтехника для дома', 0, '', 2, 0, 0),
('Прокат медицинской техники', 0, '', 308, 0, 0),
('Медицинская одежда', 0, '', 206, 0, 0),
('Предметы облегчающие быт', 0, '', 120, 0, 0),
('Средства гигиены', 0, '', 205, 0, 0),
('Пусто', 0, '', 307, 0, 0),
('Товары для беременных и кормящих мам', 0, '', 203, 0, 0),
('Вспомогательные средства для ухода', 120, '', 12096, 0, 0),
('Перчатки медицинские', 206, '', 2068, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `country`
--

DROP TABLE IF EXISTS `country`;
CREATE TABLE `country` (
  `id` int NOT NULL,
  `title` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `code` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `customer`
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
-- Структура таблицы `param_title`
--

DROP TABLE IF EXISTS `param_title`;
CREATE TABLE `param_title` (
  `id` int NOT NULL,
  `title` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `filter` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `param_value`
--

DROP TABLE IF EXISTS `param_value`;
CREATE TABLE `param_value` (
  `id` int NOT NULL,
  `parent_id` int NOT NULL,
  `type` int NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `price`
--

DROP TABLE IF EXISTS `price`;
CREATE TABLE `price` (
  `product_id` VARCHAR(12) NOT NULL,
  `reserve` int NOT NULL,
  `store_id` VARCHAR(9) NOT NULL,
  `unit` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `price` INT(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` VARCHAR(12) NOT NULL,
  `provider_id` VARCHAR(5) NOT NULL,
  `category_id` VARCHAR(9) NOT NULL,
  `title` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `vendor_code` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `product`
--

INSERT INTO `product` (`id`, `provider_id`, `category_id`, `title`, `description`, `vendor_code`) VALUES
(1, 1, 1, 'товар', '', ''),
(2, 1, 1, 'товар2', '', ''),
(3, 1, 1, 'товар3', '', ''),
(4, 1, 1, 'товар4', '', ''),
(5, 1, 1, 'товар5', '', ''),
(6, 1, 1, 'товар6', '', ''),
(7, 1, 1, 'товар7', '', ''),
(8, 1, 1, 'товар8', '', ''),
(9, 1, 1, 'товар9', '', ''),
(10, 1, 1, 'товар10', '', ''),
(11, 1, 1, 'товар11', '', ''),
(12, 1, 1, 'товар12', '', ''),
(13, 2, 2, 'товар13', '', ''),
(14, 2, 2, 'товар14', '', ''),
(15, 2, 2, 'товар15', '', ''),
(16, 2, 2, 'товар16', '', ''),
(17, 2, 2, 'товар17', '', ''),
(18, 2, 2, 'товар18', '', ''),
(19, 2, 2, 'товар19', '', ''),
(20, 2, 2, 'товар20', '', ''),
(21, 2, 2, 'товар21', '', ''),
(22, 2, 2, 'товар22', '', ''),
(23, 2, 2, 'товар23', '', ''),
(24, 2, 2, 'товар24', '', ''),
(25, 2, 2, 'товар25', '', ''),
(26, 2, 2, 'товар26', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `provider`
--

DROP TABLE IF EXISTS `provider`;
CREATE TABLE `provider` (
  `id` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `icon` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `provider`
--

INSERT INTO `provider` (`id`, `title`, `description`, `icon`) VALUES
('1', 'рога и копыты', '', ''),
('2', 'Тест сервис', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `stock_balance`
--

DROP TABLE IF EXISTS `stock_balance`;
CREATE TABLE `stock_balance` (
  `product_id` VARCHAR(12) NOT NULL,
  `rest` int NOT NULL,
  `store_id` VARCHAR(9) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Структура таблицы `store`
--

DROP TABLE IF EXISTS `store`;
CREATE TABLE `store` (
  `id` VARCHAR(9) NOT NULL,
  `provider_id` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `geox` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `geoy` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `icon` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `store`
--

INSERT INTO `store` (`id`, `provider_id`, `title`, `description`, `address`, `geox`, `geoy`, `icon`) VALUES
(1, '1', 'Магазин рогов и копыт №1', '', '', '', '', ''),
(5, '1', 'Магазин рогов и копыт №1', '', '', '', '', ''),
(2, '1', 'Магазин рогов и копыт №2', '', '', '', '', ''),
(3, '1', 'Магазин рогов и копыт №3', '', '', '', '', ''),
(4, '2', 'Магазин тест сервиса №1', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `test`
--

DROP TABLE IF EXISTS `test`;
CREATE TABLE `test` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `category`
--
-- ALTER TABLE `category`
--   ADD PRIMARY KEY (`id_1C_group`);
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `param_title`
--
ALTER TABLE `param_title`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `param_value`
--
ALTER TABLE `param_value`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `price`
--
ALTER TABLE `price`
  ADD UNIQUE KEY `id_product` (`product_id`,`store_id`);

--
-- Индексы таблицы `provider`
--
ALTER TABLE `provider`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `stock_balance`
--
ALTER TABLE `stock_balance`
  ADD UNIQUE KEY `product_id` (`product_id`,`store_id`);

--
-- Индексы таблицы `store`
--
ALTER TABLE `store`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `test`
--
ALTER TABLE `test`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
