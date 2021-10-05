CREATE TABLE `average_price` (
  `category_id` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `average_price`
  ADD PRIMARY KEY (`category_id`);
COMMIT;

DELIMITER $$
CREATE DEFINER=`saychas_z`@`localhost` PROCEDURE `average_category_price`(IN `category` VARCHAR(10), OUT `a_price` INT)
BEGIN
    SELECT AVG(`price`) into a_price FROM `price` WHERE `product_id` in (select `id` from `product` where `category_id`= category );
    IF a_price > 0 THEN
        REPLACE INTO `average_price`(`category_id`, `price`) VALUES (category,a_price);
    END IF;
    SELECT a_price;
END$$
DELIMITER ;