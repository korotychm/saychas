-- user_id
-- product_id
-- total (количество, целое число)
-- order_id (id заказа) - по умолчанию NULL
-- price -по умолчанию NULL
-- discont -  по умолчанию NULL
-- discont_description  - text
-- timestamp - как решишь

DROP TABLE IF EXISTS `basket`;

CREATE TABLE IF NOT EXISTS `basket` (
	`user_id` INT(11) NOT NULL,
	`product_id` VARCHAR(12) NOT NULL,
	`total` INT(11) NOT NULL DEFAULT 0,
	`order_id` INT(11),
	`price` INT(11) NOT NULL DEFAULT 0,
	`discount` INT(11) NOT NULL DEFAULT 0,
	`discount_description` TEXT,
	`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;


ALTER TABLE `basket`
	ADD PRIMARY KEY (`user_id`, `product_id`);
