SELECT  `p`.*,
	`pr`.`price` AS `price`,
	`b`.`rest` AS `rest`,
	`img`.`url_http` AS `url_http`,
	`brand`.`title` AS `brandtitle`,
	`st`.`id` AS `store_id`,
	`st`.`title` AS `store_title`,
	`st`.`address` AS `store_address`
FROM `product` AS `p`
	LEFT JOIN `price` AS `pr` ON `p`.`id` = `pr`.`product_id`
	LEFT JOIN `stock_balance` AS `b` ON `p`.`id` = `b`.`product_id`
	LEFT JOIN `product_image` AS `img` ON `p`.`id` = `img`.`product_id`
	LEFT JOIN `brand` AS `brand` ON `p`.`brand_id` = `brand`.`id`
	LEFT JOIN `store` AS `st` ON `st`.`id` IN ('000000001')
WHERE `p`.`provider_id` IN (SELECT `store`.`provider_id` AS `provider_id` FROM `store`)






SELECT `p`.*,
	`pr`.`price` AS `price`,
	`b`.`rest` AS `rest`,
	`img`.`url_http` AS `url_http`,
	`brand`.`title` AS `brandtitle`, 
	`st`.`id` AS `store_id`,
	`st`.`title` AS `store_title`,
	`st`.`address` AS `store_address`
FROM `product` AS `p`
	LEFT JOIN `price` AS `pr` ON `p`.`id` = `pr`.`product_id`
	LEFT JOIN `stock_balance` AS `b` ON `p`.`id` = `b`.`product_id`
	LEFT JOIN `product_image` AS `img` ON `p`.`id` = `img`.`product_id`
	LEFT JOIN `brand` AS `brand` ON `p`.`brand_id` = `brand`.`id`
	LEFT JOIN `store` AS `st` ON `st`.`id` IN ('000000003', '000000008', '000000009')
WHERE `p`.`provider_id` IN (SELECT `store`.`provider_id` AS `provider_id` FROM `store`)
