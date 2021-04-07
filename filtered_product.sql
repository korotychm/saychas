DROP VIEW IF EXISTS filtered_product;
CREATE VIEW filtered_product AS
SELECT DISTINCT
    s.id,
    s.provider_id,
    s.title,
    pr.id AS product_id,
    pr.title AS product_title,
    sb.rest
FROM
    store s
INNER JOIN provider p ON
    p.id = s.provider_id
INNER JOIN product pr ON
    pr.provider_id = s.provider_id
LEFT JOIN stock_balance sb ON
    sb.product_id = pr.id AND sb.store_id = s.id;
-- WHERE
--    s.id IN('000000005', '000000004');

