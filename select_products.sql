SELECT `p`.*,
	`pr`.`price` AS `price`,
	`b`.`rest` AS `rest`,
	`img`.`http_url` AS `http_url`,
	`brand`.`title` AS `brandtitle`
FROM `product` AS `p`
	LEFT JOIN `price` AS `pr` ON `p`.`id` = `pr`.`product_id`
	LEFT JOIN `stock_balance` AS `b` ON `p`.`id` = `b`.`product_id`
	LEFT JOIN `product_image` AS `img` ON `p`.`id` = `img`.`product_id`
	LEFT JOIN `brand` AS `brand` ON `p`.`brand_id` = `brand`.`id`
WHERE `p`.`provider_id` IN (
	SELECT `store`.`provider_id` AS `provider_id` FROM `store` WHERE `id` = '000000004'
) AND `b`.`store_id` = '000000004';

select  id,
        title,
        parent_id
from    (select * from category
         order by parent_id, id) category_sorted,
        (select @pv := '000000003') initialisation
where   find_in_set(parent_id, @pv)
and     length(@pv := concat(@pv, ',', id));


select  id
from    (select * from category
         order by parent_id, id) category_sorted,
        (select @pv := '000000003') initialisation
where   find_in_set(parent_id, @pv)
and     length(@pv := concat(@pv, ',', id));


SELECT `p`.*,
        `pr`.`price` AS `price`,
        `b`.`rest` AS `rest`,
        `img`.`http_url` AS `http_url`,
        `brand`.`title` AS `brandtitle`
FROM `product` AS `p`
        LEFT JOIN `price` AS `pr` ON `p`.`id` = `pr`.`product_id`
        LEFT JOIN `stock_balance` AS `b` ON `p`.`id` = `b`.`product_id`
        LEFT JOIN `product_image` AS `img` ON `p`.`id` = `img`.`product_id`
        LEFT JOIN `brand` AS `brand` ON `p`.`brand_id` = `brand`.`id`
WHERE `p`.`provider_id` IN (
        SELECT `store`.`provider_id` AS `provider_id` FROM `store` WHERE `id` = '000000004'
) AND `b`.`store_id` = '000000004'  AND `p`.category_id in (
select  id
from    (select * from category
         order by parent_id, id) category_sorted,
        (select @pv := '0') initialisation
where   find_in_set(parent_id, @pv)
and     length(@pv := concat(@pv, ',', id))
);




SELECT `p`.provider_id, `p`.category_id,
        `pr`.`price` AS `price`,
        `b`.`rest` AS `rest`,
        `img`.`http_url` AS `http_url`,
        `brand`.`title` AS `brandtitle`
FROM `product` AS `p`
        LEFT JOIN `price` AS `pr` ON `p`.`id` = `pr`.`product_id`
        LEFT JOIN `stock_balance` AS `b` ON `p`.`id` = `b`.`product_id`
        LEFT JOIN `product_image` AS `img` ON `p`.`id` = `img`.`product_id`
        LEFT JOIN `brand` AS `brand` ON `p`.`brand_id` = `brand`.`id`
WHERE `p`.`provider_id` IN (
        SELECT `store`.`provider_id` AS `provider_id` FROM `store`
) AND `p`.category_id in (
select  id as category_id
from    (select * from category
         order by parent_id, id) category_sorted,
        (select @pv := '000000003') initialisation
where   find_in_set(`category_sorted`.parent_id, @pv)
and     length(@pv := concat(@pv, ',', id))
);



