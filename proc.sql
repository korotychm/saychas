DELIMITER //

DROP PROCEDURE IF EXISTS get_products_by_categories//

CREATE PROCEDURE get_products_by_categories
(
	cat_id VARCHAR(9),
	store_list TEXT
)
BEGIN
	DROP TABLE IF EXISTS temp;

	CREATE TABLE temp AS SELECT * FROM (
	SELECT  id as category_id
	FROM    (SELECT * FROM category
        	 ORDER BY parent_id, id) category_sorted,
	        (SELECT @pv := cat_id) initialisation
	WHERE   FIND_IN_SET(`category_sorted`.parent_id, @pv)
	AND     LENGTH(@pv := CONCAT(@pv, ',', id)) ) temp;

	SELECT  `p`.provider_id,
		`p`.category_id,
	        `pr`.`price` AS `price`,
	        `b`.`rest` AS `rest`,
	        `img`.`url_http` AS `url_http`,
	        `brand`.`title` AS `brand_title`,

                `store`.`title` AS `store_title`,
                `store`.`address` AS `store_address`,
                `store`.`description` AS `store_description`,

                `p`.`param_value_list`,
                `p`.`param_variable_list`,
		`p`.`title`,
		`p`.`description`,
		`p`.`vendor_code`
	FROM `product` AS `p`
	        LEFT JOIN `price` AS `pr` ON `p`.`id` = `pr`.`product_id`
	        LEFT JOIN `stock_balance` AS `b` ON `p`.`id` = `b`.`product_id`
	        LEFT JOIN `product_image` AS `img` ON `p`.`id` = `img`.`product_id`
	        LEFT JOIN `brand` AS `brand` ON `p`.`brand_id` = `brand`.`id`
		LEFT JOIN `store` ON find_in_set(`store`.id, store_list)
	WHERE `p`.`provider_id` IN (
	        SELECT `store`.`provider_id` AS `provider_id` FROM `store`
	) AND `p`.category_id IN ( SELECT category_id FROM temp );

	SET @pv = cat_id;

END//

DELIMITER ;
