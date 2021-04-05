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



select t.rest from (
SELECT  `pr`.*, `pri`.`price` AS `price`,
	( SELECT SUM(`b`.`rest`) AS `rest` FROM stock_balance GROUP BY store_id) as r,
	`img`.`url_http` AS `url_http`,
	`brand`.`title` AS `brandtitle`,
	`ss`.`id` AS `store_id`,
	`ss`.`title` AS `store_title`
FROM `product` AS `pr`
	LEFT JOIN `price` AS `pri` ON `pr`.`id` = `pri`.`product_id`
	LEFT JOIN `stock_balance` AS `b` ON `pr`.`id` = `b`.`product_id`
	LEFT JOIN `product_image` AS `img` ON `pr`.`id` = `img`.`product_id`
	LEFT JOIN `brand` AS `brand` ON `pr`.`brand_id` = `brand`.`id`
	LEFT JOIN (
			SELECT `s`.* FROM `store` AS `s`
				LEFT JOIN `provider` AS `p` ON `p`.`id` = `s`.`provider_id` WHERE `s`.`id` IN ('000000001', '000000004', '000000003', '000000005', '000000002')
		  ) AS `ss` ON `ss`.`provider_id` = `pr`.`provider_id`
WHERE ss.id is not null) t;
