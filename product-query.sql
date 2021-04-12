SELECT `p`.*,
	`pr`.`price` AS `price`,
	`b`.`rest` AS `rest`,
	`img`.`http_url` AS `http_url`,
	`brand`.`title` AS `brand_title`
	FROM `product` AS `p`
	LEFT JOIN `price` AS `pr` ON `p`.`id` = `pr`.`product_id`
	LEFT JOIN `stock_balance` AS `b` ON `p`.`id` = `b`.`product_id`
	LEFT JOIN `product_image` AS `img` ON `p`.`id` = `img`.`product_id`
	LEFT JOIN `brand` AS `brand` ON `p`.`brand_id` = `brand`.`id`
WHERE `p`.`provider_id` IN (SELECT `store`.`provider_id` AS `provider_id` FROM `store` WHERE store.id in ('000000001', '000000005') );
