DROP TABLE IF EXISTS `post`;
CREATE TABLE `post` (
	id		INT(11),
	title		TEXT,
	content		TEXT,
	status		TEXT,
	date_created	date
)ENGINE=MyISAM;


SELECT `product`.*, `pri`.`price` AS `price`
	FROM `product`
	LEFT JOIN `price` AS `pri` ON `product`.`id` = `pri`.`product_id`
	WHERE `price` <= :where1 AND `price` >= :where2 AND `category_id` IN (:where3)

SELECT p.id, pc.characteristic_id as characteristic_id, pc.value as value
	FROM product p LEFT JOIN product_characteristic pc ON pc.product_id = p.id
	WHERE ( characteristic_id = '000000001-000000006' AND value IN(156,704) ) OR ( characteristic_id = '000000004-000000006' AND value IN(000000002,000000011,000000012) ) ;
